<?php

namespace App\Console\Commands;

use App\Models\Instruments\AED;
use App\Models\Instruments\Gold;
use App\Models\Instruments\Ons;
use App\Models\Instruments\Tether;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;

class CalculateGold24Command extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:calculate-instrument';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            Artisan::call("report:import-aed");
            Artisan::call("report:import-ons");

            $lastRecord = Tether::query()
                ->orderBy("date_time", "desc")
                ->first();
            if ($lastRecord) {
                $startDateTime = $lastRecord->date_time;
            } else {
                $lastRecord = AED::query()
                    ->orderBy("date_time")
                    ->first();
                $startDateTime = $lastRecord->date_time;
            }
            $startDate = Date::createFromDate($startDateTime);
            $days = Date::createFromDate($startDateTime)->diffInDays(now());
            for ($i = 0; $i < ($days - 1); $i++) {
                $ons = Ons::query()
                    ->whereDate("date_time", $startDate->addDay()->format("Y-m-d"))
                    ->firstOr(function () use ($startDate) {
                        $reserve = clone $startDate;
                        return Ons::query()
                            ->whereBetween("date_time", [
                                $reserve->subDays(5)->format("Y-m-d"),
                                $reserve->addDays(4)->format("Y-m-d"),
                            ])
                            ->orderBy("date_time", "desc")
                            ->first();
                    });
                $date = clone $startDate;
                $aed = AED::query()
                    ->whereBetween("date_time", [$date->subDays(5)->format("Y-m-d"), $date->addDays(5)->format("Y-m-d")])
                    ->orderBy("date_time", "desc")
                    ->first();
                if (!$aed) {
                    continue;
                }
                $realDollar = [];
                $realDollar["open"] = (int)(($aed->open / 10) * config("financial.usd_aed"));
                $realDollar["high"] = (int)(($aed->high / 10) * config("financial.usd_aed"));
                $realDollar["low"] = (int)(($aed->low / 10) * config("financial.usd_aed"));
                $realDollar["close"] = (int)(($aed->close / 10) * config("financial.usd_aed"));
                $realDollar["date_time"] = $startDate->format("Y-m-d");
                $realDollar["tarikh"] = verta($startDate)->format("Y-m-d");
                $realDollar["timestamp"] = $startDate->timestamp;
                Tether::query()
                    ->updateOrCreate(["date_time" => $realDollar["date_time"]], $realDollar);


                $record = $realDollar;
                $record["open"] = $realDollar["open"] * ($ons->open / 31.1035);
                $record["high"] = $realDollar["high"] * ($ons->high / 31.1035);
                $record["low"] = $realDollar["low"] * ($ons->low / 31.1035);
                $record["close"] = $realDollar["close"] * ($ons->close / 31.1035);
                Gold::query()
                    ->updateOrCreate(["date_time" => $record["date_time"]], $record);
            }
            /*foreach ($onss as $ons) {
                $date = Carbon::createFromTimestamp($ons->timestamp);
                $aed = AED::query()
                    ->whereBetween("date_time", [$date->format("Y-m-d"), $date->addDay()->format("Y-m-d")])
                    ->orderBy("date_time")
                    ->firstOr(function () use ($date) {
                        return AED::query()
                            ->whereBetween("date_time", [$date->subDay()->format("Y-m-d"), $date->addDays(2)->format("Y-m-d")])
                            ->orderBy("date_time")
                            ->first();
                    });
                if (!$aed) {
                    continue;
                }
                $realDollar = [];
                $realDollar["open"] = (int)(($aed->open / 10) * config("financial.usd_aed"));
                $realDollar["high"] = (int)(($aed->high / 10) * config("financial.usd_aed"));
                $realDollar["low"] = (int)(($aed->low / 10) * config("financial.usd_aed"));
                $realDollar["close"] = (int)(($aed->close / 10) * config("financial.usd_aed"));
                $realDollar["date_time"] = $aed->date_time;
                $realDollar["tarikh"] = $aed->tarikh;
                $realDollar["timestamp"] = $aed->timestamp;
                Tether::query()
                    ->updateOrCreate(["date_time" => $realDollar["date_time"]], $realDollar);


                $record = $realDollar;
                $record["open"] = $realDollar["open"] * ($ons->open / 31.1035);
                $record["high"] = $realDollar["high"] * ($ons->high / 31.1035);
                $record["low"] = $realDollar["low"] * ($ons->low / 31.1035);
                $record["close"] = $realDollar["close"] * ($ons->close / 31.1035);
                Gold::query()
                    ->updateOrCreate(["date_time" => $record["date_time"]], $record);
            }*/

            $this->output->info("everything is done");
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
