<?php

namespace App\Console\Commands;

use App\Models\Bourse\FinancialPeriod;
use App\Models\Bourse\Instrument;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

/**
 * نکته‌ی مهم:
 * این صفحه یک فرم ASP.NET Web Forms است. هر بار که گزارش (ddlTable) عوض می‌شود
 * یک full postback اتفاق می‌افتد و باید hiddenFieldهای (__VIEWSTATE و ...)
 * همان پاسخِ آخر را برای درخواست بعدی استفاده کنیم (زنجیره‌ای از postback).
 *
 * نصب پکیج لازم (اگر از قبل نصب نیست):
 *   composer require symfony/dom-crawler symfony/css-selector
 */
class FetchCodalInterimStatement extends Command
{
    protected $signature = 'codal:fetch-interim {url : آدرس کامل صفحه InterimStatement.aspx به همراه LetterSerial}';

    protected $description = 'دریافت صورت‌های مالی میاندوره‌ای یک نماد از سایت کدال';

    /**
     * مقدار option مربوط به هر گزارش در ddlTable
     */
    protected array $reportTypes = [
        'income_statement' => '1',
        'balance_sheet' => '0',
        'cash_flow' => '9',
        'equity_changes' => '1060',
        // 'comprehensive_income' => '1058',
    ];

    protected string $userAgent = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36';

    public function handle(): int
    {
        $url = $this->argument('url');
        $jar = new CookieJar();

        $this->info('در حال دریافت صفحه اولیه از کدال...');
        $response = $this->httpRequest('GET', $url, [], $jar);
        $html = $response->body();

        $hidden = $this->extractHiddenFields($html);
        $companyInfo = $this->extractCompanyInfo($html);

        $this->line("شرکت: {$companyInfo['name']} ({$companyInfo['symbol']}) - دوره: {$companyInfo['period_label']}");

        $instrument = $this->resolveInstrument($companyInfo);
        $period = $this->resolveFinancialPeriod($companyInfo);

        foreach ($this->reportTypes as $key => $ddlValue) {
            $this->info("در حال دریافت گزارش: {$key} ...");

            $formData = array_merge($hidden, [
                '__EVENTTARGET' => 'ctl00$ddlTable',
                '__EVENTARGUMENT' => '',
                'ctl00$ddlTable' => $ddlValue,
            ]);

            $response = $this->httpRequest('POST', $url, $formData, $jar, $url);
            $reportHtml = $response->body();

            // برای postback بعدی، باید از hidden fieldهای همین پاسخ استفاده کنیم
            $hidden = $this->extractHiddenFields($reportHtml);

            $this->parseAndStoreReport($key, $reportHtml, $instrument, $period);

            // کمی تاخیر برای جلوگیری از درخواست‌های بیش از حد سریع
            usleep(500_000);
        }

        $this->info('پایان.');

        return self::SUCCESS;
    }

    protected function httpRequest(string $method, string $url, array $form, CookieJar $jar, ?string $referer = null): Response
    {
        $headers = [
            'User-Agent' => $this->userAgent,
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'fa-IR,fa;q=0.9,en;q=0.8',
        ];

        if ($referer) {
            $headers['Referer'] = $referer;
            $headers['Origin'] = 'https://www.codal.ir';
        }

        $request = Http::withOptions(['cookies' => $jar])
            ->withHeaders($headers)
            ->timeout(30);

        return $method === 'GET'
            ? $request->get($url)
            : $request->asForm()->post($url, $form);
    }

    /**
     * تمام input هایی hidden فرم را استخراج می‌کند
     * (شامل __VIEWSTATE, __EVENTVALIDATION, hdfThumbPrint, hdfLetterRef و ...)
     */
    protected function extractHiddenFields(string $html): array
    {
        $crawler = new Crawler($html);
        $fields = [];

        $crawler->filter('input[type="hidden"]')->each(function (Crawler $node) use (&$fields) {
            $name = $node->attr('name');
            if ($name) {
                $fields[$name] = $node->attr('value') ?? '';
            }
        });

        return $fields;
    }

    protected function extractCompanyInfo(string $html): array
    {
        $crawler = new Crawler($html);

        $text = function (string $selector) use ($crawler) {
            $node = $crawler->filter($selector);
            return $node->count() ? trim($node->text()) : null;
        };

        return [
            'name' => $text('#ctl00_txbCompanyName'),
            'symbol' => $text('#ctl00_txbSymbol'),
            'isic' => $text('#ctl00_lblISIC'),
            'period_label' => $text('#ctl00_lblPeriod'),         // مثلا «9 ماهه»
            'period_end_date' => $text('#ctl00_lblPeriodEndToDate'), // شمسی
            'year_end_date' => $text('#ctl00_lblYearEndToDate'),     // شمسی
            'is_audited' => $text('#ctl00_lblIsAudited'),
        ];
    }

    protected function resolveInstrument(array $companyInfo): Instrument
    {
        return Instrument::firstOrCreate(
            ['symbol' => $companyInfo['symbol']],
            [
                'name' => $companyInfo['name'],
                'slug' => $companyInfo['symbol'],
                // tse_tmc_code از این صفحه به‌صورت مستقیم در دسترس نیست،
                // اگر جای دیگری دارید همان‌جا ست کنید
            ]
        );
    }

    protected function resolveFinancialPeriod(array $companyInfo): FinancialPeriod
    {
        // period_order از روی متن «9 ماهه» استخراج می‌شود
        preg_match('/(\d+)/', $companyInfo['period_label'] ?? '', $m);

        return FinancialPeriod::firstOrCreate([
            'solar_end_date' => $companyInfo['period_end_date'],
        ], [
            // TODO: محاسبه‌ی solar_start_date بر اساس year_end_date و period_order
            // و تبدیل تاریخ شمسی به میلادی (مثلا با پکیج morilog/jalali)
        ]);
    }

    /**
     * TODO: این متد باید بر اساس ساختار واقعی HTML هر جدول گزارش نوشته شود.
     * فعلاً HTML خام هر گزارش را برای بررسی ذخیره می‌کند.
     */
    protected function parseAndStoreReport(string $reportKey, string $html, Instrument $instrument, FinancialPeriod $period): void
    {
        $path = storage_path("app/codal-debug/{$instrument->symbol}-{$reportKey}.html");
        @mkdir(dirname($path), 0755, true);
        file_put_contents($path, $html);
    }
}
