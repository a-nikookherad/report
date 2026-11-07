<?php

namespace App\Console\Commands;

use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;

class CodalStatementCommand extends Command
{
    protected $signature = 'codal:statement
        {url : Complete Codal report URL}
        {sheetId? : Optional financial statement ID}
        {--force=0 : Download again and replace existing JSON files}
        {--symbol= : Optional Latin symbol override, e.g. fanoghre}';


    protected $description = 'Download Codal financial statements as JSON';

    private const BASE_STORAGE_PATH = 'instruments/json';

    private const BASE_URL = 'https://www.codal.ir/Reports/Decision.aspx';

    /**
     * Only the four required financial statements.
     */
    private const STATEMENTS = [
        '0' => 'balance_sheet',
        '1' => 'income_statements',
        '9' => 'cashflow',
        '1060' => 'change_in_property_right',
    ];

    private Client $client;

    public function handle(): int
    {
        try {
            $url = trim($this->argument('url'));

            $sheetId = $this->argument('sheetId');

            $force = (string)$this->option('force') === '1';

            $symbolOverride = trim(
                (string)$this->option('symbol')
            );

            $parsedUrl = $this->parseCodalUrl($url);

            if ($parsedUrl === null) {
                $this->error('Invalid Codal URL or missing LetterSerial.');

                return self::FAILURE;
            }

            $letterSerial = $parsedUrl['letterSerial'];

            $queryParams = $parsedUrl['queryParams'];

            $selectedSheetIds = $this->resolveSheetIds($sheetId);

            if ($selectedSheetIds === null) {
                $this->error(
                    'Invalid sheetId. Allowed values: '
                    . implode(', ', array_keys(self::STATEMENTS))
                );

                return self::FAILURE;
            }

            $this->initializeClient();

            $manifestPath = $this->getManifestPath($letterSerial);

            $manifest = $this->loadManifest($manifestPath);

            $metadata = $manifest;

            /*
             * If metadata is already available, we can determine
             * the destination without contacting Codal.
             */
            if ($metadata !== null) {
                $this->info('Using local manifest.');

                $symbol = $symbolOverride !== ''
                    ? $this->normalizeSymbol($symbolOverride)
                    : $metadata['symbol'];

                $periodEndDate = $metadata['periodEndDate'];
            } else {
                $symbol = null;
                $periodEndDate = null;
            }

            /*
             * Determine which files actually need downloading.
             */
            $pendingSheetIds = $selectedSheetIds;

            if (
                $metadata !== null
                && !$force
            ) {
                $directory = $this->getOutputDirectory(
                    $symbol,
                    $periodEndDate
                );

                $pendingSheetIds = array_values(
                    array_filter(
                        $selectedSheetIds,
                        fn(string $id) => !File::exists(
                            "{$directory}/" . self::STATEMENTS[$id] . '.json'
                        )
                    )
                );

                if (empty($pendingSheetIds)) {
                    $this->info(
                        'All requested JSON files already exist. '
                        . 'No request sent to Codal.'
                    );

                    return self::SUCCESS;
                }
            }

            /*
             * When no manifest exists, fetch the first requested
             * statement to discover symbol and reporting date.
             */
            $firstResponseHtml = null;

            if ($metadata === null) {
                $firstSheetId = $pendingSheetIds[0];

                $this->info(
                    "Fetching initial metadata using sheetId={$firstSheetId}"
                );

                $firstResponseHtml = $this->requestStatement(
                    $queryParams,
                    $firstSheetId,
                    $url
                );

                $symbolOverride = (string)$this->option('symbol');

                $metadata = $this->extractMetadata(
                    $firstResponseHtml,
                    $symbolOverride
                );

                if ($metadata === null) {
                    $this->error(
                        'Could not extract symbol or reporting date from Codal.'
                    );

                    return self::FAILURE;
                }

                $symbol = $symbolOverride !== ''
                    ? $this->normalizeSymbol($symbolOverride)
                    : $this->normalizeSymbol($metadata['rawSymbol']);

                $periodEndDate = $metadata['periodEndDate'];

                $manifest = [
                    'letterSerial' => $letterSerial,
                    'symbol' => $symbol,
                    'rawSymbol' => $metadata['rawSymbol'],
                    'periodEndDate' => $periodEndDate,
                    'createdAt' => now()->toIso8601String(),
                ];

                File::ensureDirectoryExists(
                    dirname($manifestPath)
                );

                File::put(
                    $manifestPath,
                    json_encode(
                        $manifest,
                        JSON_PRETTY_PRINT
                        | JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    )
                );

                $this->info(
                    "Symbol: {$symbol} | Period: {$periodEndDate}"
                );
            }

            /*
             * If the user supplied a symbol override, persist it.
             */
            if (
                $metadata !== null
                && $symbolOverride !== ''
                && $manifest !== null
            ) {
                $manifest['symbol'] = $symbol;

                File::put(
                    $manifestPath,
                    json_encode(
                        $manifest,
                        JSON_PRETTY_PRINT
                        | JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    )
                );
            }

            $outputDirectory = $this->getOutputDirectory(
                $symbol,
                $periodEndDate
            );

            File::ensureDirectoryExists($outputDirectory);

            $this->info("Output directory: {$outputDirectory}");

            /*
             * Process only missing files, unless force=1.
             */
            foreach ($pendingSheetIds as $currentSheetId) {
                $fileName = self::STATEMENTS[$currentSheetId] . '.json';

                $filePath = "{$outputDirectory}/{$fileName}";

                if (
                    !$force
                    && File::exists($filePath)
                ) {
                    $this->line(
                        "Skipped {$fileName}: already exists."
                    );

                    continue;
                }

                $this->newLine();

                $this->info(
                    "Processing {$fileName} (sheetId={$currentSheetId})"
                );

                /*
                 * Reuse the first response if it was already fetched.
                 */
                if (
                    $firstResponseHtml !== null
                    && $currentSheetId === $pendingSheetIds[0]
                ) {
                    $html = $firstResponseHtml;

                    $firstResponseHtml = null;
                } else {
                    $html = $this->requestStatement(
                        $queryParams,
                        $currentSheetId,
                        $url
                    );
                }

                $datasource = $this->extractDatasource($html);

                if ($datasource === null) {
                    $this->warn(
                        "Datasource extraction failed for sheetId={$currentSheetId}."
                    );

                    continue;
                }

                $json = json_encode(
                    $datasource,
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_THROW_ON_ERROR
                );

                /*
                 * Write to a temporary file first, then rename.
                 * This prevents partially written JSON files.
                 */
                $temporaryPath = $filePath . '.tmp';

                File::put($temporaryPath, $json);

                if (!File::move($temporaryPath, $filePath)) {
                    throw new \RuntimeException(
                        "Could not move temporary file to {$filePath}"
                    );
                }

                $this->info("Saved: {$fileName}");
            }

            $this->newLine();

            $this->info('Finished successfully.');

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }

    /**
     * Initialize HTTP client.
     */
    private function initializeClient(): void
    {
        $this->client = new Client([
            'cookies' => true,
            'allow_redirects' => true,
            'verify' => true,
            'timeout' => 120,
            'connect_timeout' => 20,

            'headers' => [
                'User-Agent' =>
                    'Mozilla/5.0 (X11; Linux x86_64) '
                    . 'AppleWebKit/537.36 '
                    . '(KHTML, like Gecko) '
                    . 'Chrome/156.0.0.0 Safari/537.36',

                'Accept' =>
                    'text/html,application/xhtml+xml,'
                    . 'application/xml;q=0.9,*/*;q=0.8',

                'Accept-Language' => 'fa-IR,fa;q=0.9,en;q=0.8',
            ],
        ]);
    }

    /**
     * Validate and parse the complete Codal URL.
     */
    private function parseCodalUrl(string $url): ?array
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        $parts = parse_url($url);

        if (
            !isset($parts['host'])
            || !in_array(
                strtolower($parts['host']),
                ['codal.ir', 'www.codal.ir'],
                true
            )
        ) {
            return null;
        }

        if (
            !isset($parts['path'])
            || !str_ends_with($parts['path'], '/Reports/Decision.aspx')
        ) {
            return null;
        }

        parse_str($parts['query'] ?? '', $queryParams);

        $letterSerial = $queryParams['LetterSerial'] ?? null;

        if (!$letterSerial) {
            return null;
        }

        unset($queryParams['sheetId']);

        return [
            'letterSerial' => $letterSerial,
            'queryParams' => $queryParams,
        ];
    }

    /**
     * Resolve requested sheet IDs.
     */
    private function resolveSheetIds(?string $sheetId): ?array
    {
        if ($sheetId === null || $sheetId === '') {
            return array_keys(self::STATEMENTS);
        }

        $sheetId = trim($sheetId);

        if (!array_key_exists($sheetId, self::STATEMENTS)) {
            return null;
        }

        return [$sheetId];
    }

    /**
     * Request a financial statement using GET.
     */
    private function requestStatement(
        array  $queryParams,
        string $sheetId,
        string $referer
    ): string
    {
        $this->line("Requesting sheetId={$sheetId}");

        $response = $this->client->get(
            self::BASE_URL,
            [
                'query' => array_merge(
                    $queryParams,
                    ['sheetId' => $sheetId]
                ),

                'headers' => [
                    'Referer' => $referer,
                ],
            ]
        );

        $this->line(
            "HTTP {$response->getStatusCode()}"
        );

        return (string)$response->getBody();
    }

    private function extractMetadata(
        string $html,
        string $symbolOverride = ''
    ): ?array
    {
        $datasource = $this->extractDatasource($html);

        if ($datasource === null) {
            return null;
        }

        $periodEndDate = trim(
            (string)($datasource['periodEndToDate'] ?? '')
        );

        if (!preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $periodEndDate)) {
            return null;
        }

        $symbol = trim($symbolOverride);

        if ($symbol === '') {
            $dom = $this->createDom($html);
            $xpath = new DOMXPath($dom);

            $symbol = trim(
                $xpath->evaluate(
                    'string(//*[@id="ctl00_lblDisplaySymbol"])'
                )
            );
        }

        if ($symbol === '') {
            return null;
        }

        return [
            'rawSymbol' => $symbol,
            'periodEndDate' => $periodEndDate,
        ];
    }

    /**
     * Normalize symbol for directory naming.
     *
     * For exact Latin ticker names, use --symbol=.
     */
    private function normalizeSymbol(string $symbol): string
    {
        $symbol = trim($symbol);

        $symbol = str_replace(
            [' ', '-', '/', '\\'],
            '_',
            $symbol
        );

        $symbol = preg_replace('/_+/', '_', $symbol);

        return trim($symbol, '_');
    }

    /**
     * Build output directory.
     *
     * Example:
     * storage/app/instruments/json/fanoghre/1405_06_31
     */
    private function getOutputDirectory(
        string $symbol,
        string $periodEndDate
    ): string
    {
        $jalaliDate = str_replace(
            '/',
            '_',
            $periodEndDate
        );

        return storage_path(
            self::BASE_STORAGE_PATH
            . "/{$symbol}/{$jalaliDate}"
        );
    }

    /**
     * Manifest path based on LetterSerial.
     */
    private function getManifestPath(string $letterSerial): string
    {
        $hash = hash('sha256', $letterSerial);

        return storage_path(
            self::BASE_STORAGE_PATH
            . "/.manifest/{$hash}.json"
        );
    }

    /**
     * Read local manifest.
     */
    private function loadManifest(string $path): ?array
    {
        if (!File::exists($path)) {
            return null;
        }

        $manifest = json_decode(
            File::get($path),
            true
        );

        if (
            !is_array($manifest)
            || empty($manifest['symbol'])
            || empty($manifest['periodEndDate'])
        ) {
            return null;
        }

        return $manifest;
    }

    /**
     * Extract datasource JavaScript object.
     */
    private function extractDatasource(string $html): ?array
    {
        $patterns = [
            '/\b(?:var|let|const)\s+datasource\s*=/',
            '/\bwindow\.datasource\s*=/',
            '/\bdatasource\s*=/',
        ];

        foreach ($patterns as $pattern) {
            if (!preg_match(
                $pattern,
                $html,
                $matches,
                PREG_OFFSET_CAPTURE
            )) {
                continue;
            }

            $offset = $matches[0][1] + strlen($matches[0][0]);

            $json = $this->extractBalancedObject(
                $html,
                $offset
            );

            if ($json === null) {
                continue;
            }

            $decoded = json_decode($json, true);

            if (
                json_last_error() === JSON_ERROR_NONE
                && is_array($decoded)
            ) {
                return $decoded;
            }
        }

        return null;
    }

    /**
     * Extract a balanced JavaScript object.
     */
    private function extractBalancedObject(
        string $content,
        int    $offset
    ): ?string
    {
        $start = strpos($content, '{', $offset);

        if ($start === false) {
            return null;
        }

        $length = strlen($content);

        $depth = 0;
        $quote = null;
        $escaped = false;

        for ($i = $start; $i < $length; $i++) {
            $char = $content[$i];

            if ($quote !== null) {
                if ($escaped) {
                    $escaped = false;
                    continue;
                }

                if ($char === '\\') {
                    $escaped = true;
                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }

                continue;
            }

            if (
                $char === '"'
                || $char === "'"
                || $char === '`'
            ) {
                $quote = $char;
                continue;
            }

            if ($char === '{') {
                $depth++;
            }

            if ($char === '}') {
                $depth--;

                if ($depth === 0) {
                    return substr(
                        $content,
                        $start,
                        $i - $start + 1
                    );
                }
            }
        }

        return null;
    }

    /**
     * Create DOMDocument safely.
     */
    private function createDom(string $html): DOMDocument
    {
        $previousState = libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');

        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . $html,
            LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING
        );

        libxml_clear_errors();

        libxml_use_internal_errors($previousState);

        return $dom;
    }
}
