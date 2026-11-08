<?php

namespace App\Modules\Codal\Services;

use Illuminate\Support\Facades\File;
use JetBrains\PhpStorm\ArrayShape;
use function now;
use function storage_path;

class Manifest
{
    private const BASE_STORAGE_PATH = 'instruments/json';

    public function getManifestPath(string $letterSerial): string
    {
        $hash = hash('sha256', $letterSerial);

        return storage_path(
            self::BASE_STORAGE_PATH
            . "/.manifest/{$hash}.json"
        );
    }

    public function loadManifest(string $manifestPath): ?array
    {
        if (!File::exists($manifestPath)) {
            return null;
        }

        $manifest = json_decode(
            File::get($manifestPath),
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

    public function getPendingSheetIds(array $selectedSheetIds, string $directory)
    {
        return array_values(
            array_filter(
                $selectedSheetIds,
                fn(string $id) => !File::exists(
                    "{$directory}/" . SheetIdResolver::STATEMENTS[$id] . '.json'
                )
            )
        );
    }

    #[ArrayShape(['letterSerial' => "mixed", 'symbol' => "string", 'rawSymbol' => "", 'periodEndDate' => "mixed", 'createdAt' => "string"])]
    public function storeManifest(mixed $letterSerial, string $symbol, $rawSymbol, mixed $periodEndDate, ?string $manifestPath): array
    {
        $manifest = [
            'letterSerial' => $letterSerial,
            'symbol' => $symbol,
            'rawSymbol' => $rawSymbol,
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
        return $manifest;
    }

    /**
     * Build output directory.
     *
     * Example:
     * storage/app/instruments/json/fanoghre/1405_06_31
     */
    public function getOutputDirectory(
        string $symbol,
        string $periodEndDate
    ): string
    {
        return storage_path(
            self::BASE_STORAGE_PATH
            . "/{$symbol}/{$periodEndDate}"
        );
    }

}
