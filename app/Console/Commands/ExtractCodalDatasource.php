<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\DomCrawler\Crawler;

class ExtractCodalDatasource extends Command
{
    protected $signature = 'codal:datasource
                            {letter_serial : آدرس صفحه کدال}
                            {type=0 : نوع گزارش}
                            {--save : ذخیره فایل JSON}';

    protected $description = 'استخراج متغیر datasource از گزارش کدال';

    public function handle()
    {
        $letterSerial = $this->argument('letter_serial');
        $reportType = $this->argument('type');

        $this->info("در حال دریافت صفحه کدال...");

        $client = new Client([
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            ]
        ]);

        try {
            $response = $client->get("https://www.codal.ir/Reports/InterimStatement.aspx?LetterSerial=EPrfVlRNU8rYEIYM1740RA%3d%3d");
//            $url = "https://www.codal.ir/Reports/InterimStatement.aspx?LetterSerial={$letterSerial}";
//            $response = $client->get($url);
            dd(1,$response->getBody()->getContents());
            $html = $response->getBody()->getContents();
            $dataSource = $this->getDataSourceWithRegex($html, "income_statement");
//            if (empty($html)) {
//                $this->error("HTML خالی برگشت!");
//            } else {
//                $this->info("✅ HTML با موفقیت دریافت شد. طول محتوا: " . strlen($html) . " کاراکتر");
//                dd($html);   // یا substr($html, 0, 2000) برای جلوگیری از خروجی خیلی بزرگ
//            }
//            $this->info("✅ صفحه با موفقیت دریافت شد | طول: " . strlen($html) . " کاراکتر");

//            $registerCapital = $this->getRegisterCapital($html);

exit();
            // استخراج مقادیر مخفی مهم
            preg_match('/name="__VIEWSTATE" id="__VIEWSTATE" value="([^"]+)"/', $html, $vs);
            preg_match('/name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="([^"]+)"/', $html, $ev);

            $viewState = $vs[1] ?? '';
            $eventValidation = $ev[1] ?? '';
            $postData = [
                '__EVENTTARGET' => 'ctl00$ddlTable',
                '__EVENTARGUMENT' => '',
                '__VIEWSTATE' => $viewState,
                '__EVENTVALIDATION' => $eventValidation,
                'ctl00$ddlTable' => $reportType,        // مهم‌ترین قسمت
            ];

            $response = $client->post($url, [
                'form_params' => $postData
            ]);

            $html = $response->getBody()->getContents();
            if (empty($html)) {
                $this->error("HTML خالی برگشت!");
            } else {
                $this->info("✅ HTML با موفقیت دریافت شد. طول محتوا: " . strlen($html) . " کاراکتر");
                dd($html);   // یا substr($html, 0, 2000) برای جلوگیری از خروجی خیلی بزرگ
            }

            $dataSource = $this->getDataSourceWithRegex($html, "cash_flow");

        } catch (\Throwable $e) {
//            $this->error("خطا: " . $e->getMessage());
            dd($e->getMessage());
        }
    }

    private function getRegisterCapital(string $html)
    {
        $crawler = new Crawler($html);

        // روش ۱: جستجو بر اساس متن لیبل
        $capitalTag = $crawler->filter('span#ctl00_lblListedCapital')->first();
//        dd($capitalTag);
        if ($capitalTag->count() > 0) {
            $value = trim($capitalTag->text());
            $capital = (int)str_replace(',', '', $value);

//            $this->info("✅ سرمایه ثبت شده پیدا شد: " . number_format($capital) . " ریال");
        }

        if (!isset($cleanValue)) {
            // روش ۲: جستجوی عمومی (اگر id بالا کار نکرد)
            $text = $crawler->filter('body')->text();
            if (preg_match('/سرمایه ثبت شده[:\s]*([\d,]+)/u', $text, $matche)) {
                $capital = str_replace(',', '', $matche[1]);
//                $this->info("✅ سرمایه ثبت شده (با regex): " . number_format((int)$capital) . " ریال");
            }
        }
        return $capital;
    }

    protected function getDataSourceWithRegex(string $html, string $reportType)
    {
//        if (preg_match('/var\s+datasource\s*=\s*(\{.*?\});/s', $html, $matches)) {
        if (preg_match('/var\s+datasource\s*=\s*(\{.*?\});/s', $html, $matches)) {
            $jsonString = $matches[1];

            // تمیز کردن و تبدیل به آرایه
            $dataSource = json_decode($jsonString, true);
            $filename = storage_path("app/codal/datasource_{$reportType}_" . date('Ymd_His') . '.json');
            file_put_contents($filename, json_encode($dataSource, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $dataSource;
        }
    }
}
