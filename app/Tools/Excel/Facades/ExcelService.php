<?php

namespace App\Tools\Excel\Facades;

use App\Tools\Excel\ExcelReader;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;


/**
 * @method static ExcelReader purify(string $storagePath, string $extension = null)
 * @method static array rows()
 * @method static array read()
 * @method static array header()
 * @method static array body()
 * @method static array records()
 */
class ExcelService extends Facade
{
    static public function getFacadeAccessor()
    {
        return App::make(ExcelReader::class);
    }
}
