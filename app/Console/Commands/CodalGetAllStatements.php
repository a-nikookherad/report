<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Browsershot\Browsershot;

class CodalGetAllStatements extends Command
{
    protected $signature = 'codal:all-statements {letterSerial}';

    public function handle()
    {
        $letterSerial = $this->argument('letterSerial');
        $baseUrl = "https://www.codal.ir/Reports/InterimStatement.aspx?LetterSerial={$letterSerial}";

        $reportTypes = [
            'balance_sheet'     => ['value' => 0,  'name' => 'صورت وضعیت مالی'],
            'income_statement'  => ['value' => 1,  'name' => 'صورت سود و زیان'],
            'equity_changes'    => ['value' => 1060,'name' => 'تغییرات حقوق مالکانه'],
            'cash_flow'         => ['value' => 9,  'name' => 'صورت جریان وجوه نقدی'],
        ];

        foreach ($reportTypes as $key => $report) {
            $this->info("در حال دریافت {$report['name']} ...");

            $html = Browsershot::url($baseUrl)
                ->waitUntilNetworkIdle()
                ->selectOption('#ctl00_ddlTable', $report['value'])  // انتخاب از dropdown
                ->waitUntilNetworkIdle()
                ->bodyHtml();

            // استخراج datasource
            if (preg_match('/var\s+datasource\s*=\s*(\{.*?\});/s', $html, $matches)) {
                $data = json_decode($matches[1], true);
                $path = storage_path("app/codal/{$key}_" . time() . ".json");
                file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

                $this->info("✅ {$report['name']} ذخیره شد");
            } else {
                $this->warn("datasource برای {$report['name']} پیدا نشد");
            }

            sleep(2); // جلوگیری از بلاک شدن
        }
    }
}
