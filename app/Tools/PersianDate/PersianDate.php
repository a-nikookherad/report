<?php

namespace App\Tools\PersianDate;

use Hekmatinasser\Verta\Facades\Verta;

class PersianDate implements GregorianToSolarInterface, SolarToGregorianInterface
{

    public static function gregorianToSolar($gregorianDate, string $format = "Y-m-d")
    {
        return Verta::parse($gregorianDate)->format($format);
    }

    public static function solarToGregorian($solarDate, string $format = "Y-m-d")
    {
        return Verta::parse($solarDate)->datetime()->format($format);
    }
}
