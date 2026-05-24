<?php

namespace App\Repositories\FileSystemRepo;

interface MakeJsonFileInterface
{
    public static function storeJson(string $path, mixed $content);
}
