<?php

namespace App\Modules\Codal\PersistenceServices;

use Illuminate\Support\Facades\File;

class FileSystemPersist implements PersistenceInterface
{

    public function persist(StorableInterface $storableDTO)
    {
        $record = $storableDTO->toArray();
        $json = json_encode($record, JSON_PRETTY_PRINT
            | JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES);
        $filePath = storage_path("app/InstrumentBackups/test.json");

        File::ensureDirectoryExists(
            dirname($filePath)
        );

        File::put($filePath, $json);
    }
}
