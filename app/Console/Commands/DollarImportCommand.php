<?php

namespace App\Console\Commands;

use App\Models\Instruments\Dollar;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DollarImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:import-dollar';

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
            $lastRecord = Dollar::query()
                ->orderBy("date_time", "desc")
                ->first();
            if ($lastRecord) {
                $startDateTime = $lastRecord->timestamp;
            } else {
                $startDateTime = Date::createFromDate("1979-12-26")->timestamp;
            }
            $endDateTime = Date::now()->timestamp;
            $url = "https://dashboard-api.tgju.org/v1/tv/history?symbol=PRICE_DOLLAR_RL&resolution=1440&from={$startDateTime}&to={$endDateTime}";
            $response = Http::get($url);
            if ($response->failed() || $response->object()->s !== "ok") {
                exit();
            }
            //make record
            $response = $response->object();
            $open = $response->o;
            $high = $response->h;
            $low = $response->l;
            $close = $response->c;
            $dateTime = $response->t;
            for ($i = 0; $i < count($open); $i++) {
                $date = Date::createFromTimestamp($dateTime[$i])->format("Y-m-d H:i:s");
                $record = [
                    "open" => $open[$i],
                    "high" => $high[$i],
                    "low" => $low[$i],
                    "close" => $close[$i],
                    "timestamp" => $dateTime[$i],
                    "tarikh" => verta($dateTime[$i])->format("Y-m-d H:i:s") ?? null,
                    "date_time" => $date,
                ];
                Dollar::query()
                    ->updateOrCreate(["date_time" => $record["date_time"]], $record);
            }

            $this->output->info("everything is done");
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
        }
    }
}
