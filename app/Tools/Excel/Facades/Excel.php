<?php

namespace App\Tools\Excel\Facades;

use App\Tools\Excel\ExcelService;
use Illuminate\Support\Facades\Facade;


/**
 * @method static ExcelService read(string $path)
 * @method static array rows()
 * @method static array header()
 * @method static array body()
 * @method static array records()
 * @method static boolean write(string $path,array $data)
 */
class Excel extends Facade
{
    static public function getFacadeAccessor()
    {
        return ExcelService::class;
    }
}
