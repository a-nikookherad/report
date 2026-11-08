<?php

namespace App\Modules\Codal;


use App\Modules\Codal\Contracts\CrawlerClientInterface;
use App\Modules\Codal\Contracts\DataSourceExtractorInterface;
use App\Modules\Codal\Exceptions\FailedExtractException;
use App\Modules\Codal\PersistenceServices\StorableInterface;
use App\Modules\Codal\Services\FinancialStatementDetector;
use App\Modules\Codal\Services\Manifest;
use App\Modules\Codal\Services\SheetIdResolver;
use App\Modules\Codal\Services\UrlParser;
use App\Modules\Codal\Services\Utilities;
use Illuminate\Support\Facades\File;

class CodalService
{
    public function __construct(
        private readonly Manifest $manifest,
        private readonly UrlParser $urlParser,
        private readonly SheetIdResolver $sheetIdResolver,
        private readonly CrawlerClientInterface $crawler,
        private readonly DataSourceExtractorInterface $dataSourceExtractor,
        private readonly FinancialStatementDetector $financialStatementDetector,
        private readonly Utilities $utilities,
        private readonly array $persistences
    )
    {

    }

    public function import(string $url, string $symbol, ?bool $force, ?string $sheetId): bool
    {
        $symbol = $this->utilities->normalizeSymbol($symbol);

        $letterSerial = $this->urlParser->getLetterSerial();
        $queryParams = $this->urlParser->getQueryParams();

        $selectedSheetIds = $this->sheetIdResolver->resolve($sheetId);

        $manifestPath = $this->manifest->getManifestPath($letterSerial);
        $manifest = $this->manifest->loadManifest($manifestPath);
        $metadata = $manifest;

        /*
         * If metadata is already available, we can determine
         * the destination without contacting Codal.
         */
        if ($metadata !== null) {
            //Using local manifest.
            $periodEndDate = $this->utilities->dateNormalize($metadata['periodEndDate']);
        } else {
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
            $directory = $this->manifest->getOutputDirectory(
                $symbol,
                $periodEndDate
            );
            $pendingSheetIds = $this->manifest->getPendingSheetIds($selectedSheetIds, $directory);

            if (empty($pendingSheetIds)) {
                /*'All requested JSON files already exist. No request sent to Codal.'*/
                return true;
            }
        }

        /*
         * When no manifest exists, fetch the first requested
         * statement to discover symbol and reporting date.
         */
        $firstResponseHtml = null;

        if ($metadata === null) {
            $firstSheetId = $pendingSheetIds[0];

            //"Fetching initial metadata using sheetId={$firstSheetId}"
            $firstResponseHtml = $this->crawler->crawl(
                $url,
                $queryParams,
                $firstSheetId
            );

            $metadata = $this->dataSourceExtractor->extractMetadata(
                $firstResponseHtml,
                $symbol
            );

            if ($metadata === null) {
                throw new FailedExtractException("Could not extract symbol or reporting date from Codal.");
            }

            $symbol = $symbol !== ''
                ? $symbol
                : $this->utilities->normalizeSymbol($metadata['rawSymbol']);

            $periodEndDate = $metadata['periodEndDate'];

            $manifest = $this->manifest->storeManifest($letterSerial, $symbol, $metadata['rawSymbol'], $periodEndDate, $manifestPath);

        }


        /*
         * If the user supplied a symbol override, persist it.
         */
        if (
            $metadata !== null
            && $manifest !== null
        ) {
            $this->manifest->storeManifest($manifest['letterSerial'], $symbol, $manifest['rawSymbol'], $manifest['periodEndDate'], $manifestPath);
        }

        $outputDirectory = $this->manifest->getOutputDirectory(
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
                $html = $this->crawler->requestStatement(
                    $url,
                    $queryParams,
                    $currentSheetId
                );
            }

            $datasource = $this->dataSourceExtractor->extractDatasource($html);

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

            $this->extractorDetector($datasource);
            //Saved: {$fileName}
        }


        //Finished successfully.
        return true;
    }

    private function extractorDetector(array $datasource)
    {
        $title = $datasource["sheets"][0]["title_En"] ?? "unknown";
        $service = $this->financialStatementDetector->detect($title, $datasource);
        $record = $service->extract();
        $DTO = $service->getDTO();
        $this->persist($DTO);
    }

    private function persist(StorableInterface $storableDTO)
    {
        foreach ($this->persistences as $item) {
            $item->persist($storableDTO);
        }
    }
}
