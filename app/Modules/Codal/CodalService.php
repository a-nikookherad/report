<?php

namespace App\Modules\Codal;


use Illuminate\Support\Facades\File;

class CodalService
{
    private function getCrawler(): CrawlerClientInterface
    {
        return resolve(CrawlerClientInterface::class);
    }

    private function getUrlParser(string $url): UrlParser
    {
        return resolve(UrlParser::class, [$url]);
    }

    private function getSheetResolver(): SheetIdResolver
    {
        return resolve(SheetIdResolver::class);
    }

    private function getManifest(): Manifest
    {
        return resolve(Manifest::class);
    }

    private function getExtractor(): Extractor
    {
        return resolve(Extractor::class);
    }

    public function import(string $url, string $symbol, ?bool $force, ?string $sheetId)
    {
        $symbolOverride = trim($symbol);

        $urlParser = $this->getUrlParser($url);
        $letterSerial = $urlParser->getLetterSerial();
        $queryParams = $urlParser->getQueryParams();

        $sheetResolver = $this->getSheetResolver();
        $selectedSheetIds = $sheetResolver->resolve($sheetId);

        $manifest = $this->getManifest();
        $manifestPath = $manifest->getManifestPath($letterSerial);
        $metadata = $manifest->loadManifest($manifestPath);
//        $metadata = $manifest;

        /*
         * If metadata is already available, we can determine
         * the destination without contacting Codal.
         */
        if ($metadata !== null) {
            //Using local manifest.
            $symbol = $symbolOverride !== ''
                ? $this->normalizeSymbol($symbolOverride)
                : "unknown";

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
            $directory = $manifest->getOutputDirectory(
                $symbol,
                $periodEndDate
            );

            $pendingSheetIds = array_values(
                array_filter(
                    $selectedSheetIds,
                    fn(string $id) => !File::exists(
                        "{$directory}/" . SheetIdResolver::STATEMENTS[$id] . '.json'
                    )
                )
            );

            if (empty($pendingSheetIds)) {
                /*'All requested JSON files already exist. '
                . 'No request sent to Codal.'*/
                return true;
            }
        }

        /*
         * When no manifest exists, fetch the first requested
         * statement to discover symbol and reporting date.
         */
        $firstResponseHtml = null;
        $extractor = $this->getExtractor();
        $crawler = $this->getCrawler();

        if ($metadata === null) {
            $firstSheetId = $pendingSheetIds[0];

            //"Fetching initial metadata using sheetId={$firstSheetId}"
            $firstResponseHtml = $crawler->crawl(
                $url,
                $queryParams,
                $firstSheetId
            );

            $metadata = $extractor->extractMetadata(
                $firstResponseHtml,
                $symbolOverride
            );

            if ($metadata === null) {
                throw new FailedExtractException("Could not extract symbol or reporting date from Codal.");
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

        $outputDirectory = $manifest->getOutputDirectory(
            $symbol,
            $periodEndDate
        );

        File::ensureDirectoryExists($outputDirectory);

        /*
         * Process only missing files, unless force=1.
         */
        foreach ($pendingSheetIds as $currentSheetId) {
            $fileName = SheetIdResolver::STATEMENTS[$currentSheetId] . '.json';

            $filePath = "{$outputDirectory}/{$fileName}";

            if (
                !$force
                && File::exists($filePath)
            ) {
                continue;
            }

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
                $html = $crawler->requestStatement(
                    $url,
                    $queryParams,
                    $currentSheetId
                );
            }

            $datasource = $extractor->extractDatasource($html);

            if ($datasource === null) {
                //Datasource extraction failed for sheetId={$currentSheetId}.
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

            //Saved: {$fileName}
        }


        //Finished successfully.
        return true;
    }

    private function store()
    {

    }

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
}
