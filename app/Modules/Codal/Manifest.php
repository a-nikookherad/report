<?php

namespace App\Modules\Codal;

use Illuminate\Support\Facades\File;

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

    public function loadManifest(string $manifestPath)
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

}
