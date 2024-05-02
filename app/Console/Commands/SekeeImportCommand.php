<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SekeeImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:import-sekee';

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
            $url = "https://dashboard-api.tgju.org/v1/tv/history?symbol=SEKEE&resolution=1440&from=1590252148&to=1714668208";
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
                    "low" => (integer)$low[$i],
                    "close" => $close[$i],
                    "date_time" => $date,
                ]);
            }
            \App\Models\Sekee::query()
                ->upsert($records, "date_time");

            $this->output->info("everything is done");
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            dd($exception->getMessage());
        }
    }
}