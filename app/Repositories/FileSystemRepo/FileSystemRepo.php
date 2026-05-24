<?php

namespace App\Repositories\FileSystemRepo;

use App\Tools\Excel\Facades\Excel;
use Illuminate\Support\Str;

class FileSystemRepo implements MakeJsonFileInterface, MakeXlsxFileInterface
{

    public static function storeFile(string $path, mixed $content)
    {
        self::checkPathExist($path);
        // TODO: Implement store() method.
    }

    public static function storeJson(string $path, mixed $content, string $fileName = null)
    {
        self::checkPathExist($path);

        $fileName = $fileName ?? Str::createUuidsNormally() . ".json";

        if (!file_exists($fileName)) {
            file_put_contents($fileName, json_encode($content));
        }
    }

    public static function storeXlsx(string $path, mixed $content, string $fileName = null)
    {
        self::checkPathExist($path);

        $fileName = $fileName ?? Str::createUuidsNormally() . ".xlsx";

        if (!file_exists($fileName)) {
            Excel::write($fileName, $content);
        }
    }

    private static function checkPathExist(string $path)
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
    }
}
