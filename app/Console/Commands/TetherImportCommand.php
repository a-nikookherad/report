<?php

namespace App\Console\Commands;

use App\Models\Instruments\AED;
use App\Models\Instruments\Tether;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class TetherImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:import-tether';

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
            DB::beginTransaction();
            $aed = AED::query()
                ->get();
            foreach ($aed as $item) {
                $record = [];
                $record["open"] = (int)($item->open * config("financial.usd_aed"));
                $record["high"] = (int)($item->high * config("financial.usd_aed"));
                $record["low"] = (int)($item->low * config("financial.usd_aed"));
                $record["close"] = (int)($item->close * config("financial.usd_aed"));
                $record["date_time"] = $item->date_time;
                $record["tarikh"] = $item->tarikh;
                $record["timestamp"] = $item->timestamp;
                Tether::query()
                    ->updateOrCreate(["date_time" => $record["date_time"]], $record);
            }

            /*            $url = "";
                        $response = Http::get($url)->object();
                        if ($response->s !== "ok") {
                            exit();
                        }
                        //make record
                        $records = [];
                        $open = $response->o;
                        $high = $response->h;
                        $low = $response->l;
                        $close = $response->c;
                        $dateTime = $response->t;
                        for ($i = 0; $i < count($open); $i++) {
                            $date = \Illuminate\Support\Facades\Date::createFromTimestamp($dateTime[$i])->format("Y-m-d H:i:s");
                            array_push($records, [
                                "open" => $open[$i],
                                "high" => $high[$i],
                                "low" => $low[$i],
                                "close" => $close[$i],
                                "timestamp" => $dateTime[$i],
                                "tarikh" => verta($dateTime[$i])->format("Y-m-d H:i:s") ?? null,
                                "date_time" => $date,
                            ]);
                        }
                        \App\Models\Instruments\Tether::query()
                            ->upsert($records, "date_time");*/

            $this->output->info("everything is done");
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
        }
    }
}
