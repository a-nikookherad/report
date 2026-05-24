<?php

namespace App\Repositories\FileSystemRepo;

interface MakeXlsxFileInterface
{
    public static function storeXlsx(string $path, mixed $content);
}
