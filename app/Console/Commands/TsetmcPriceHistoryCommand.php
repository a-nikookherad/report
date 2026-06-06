<?php

namespace App\Console\Commands;

use App\Models\Bourse\BalanceSheet;
use App\Models\Bourse\InstrumentPrice;
use App\Tools\PersianDate\PersianDate;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TsetmcPriceHistoryCommand extends Command
{
    protected $signature = 'tsetmc:prices
                            {insCode : کد Instrument TSETMC}
                            {instrumentId : آیدی نماد در جدول instruments}
                            {--days=? : تعداد روزهای اخیر}
                            {--date=? : تاریخ مشخص}';

    protected $description = 'دریافت داده‌های قیمت از TSETMC و آماده‌سازی برای جدول instrument_prices';

    protected $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = new Client([
            'timeout' => 20,
            'verify' => false,
            'headers' => [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]);
    }

    public function handle()
    {
        $insCode = $this->argument('insCode');
        $instrumentId = (int)$this->argument('instrumentId');
        $days = (int)$this->option('days');
        if (empty($days)) {
            $date = (int)$this->option('date');
        } else {
            $date = Carbon::now()->subDays($days)->format("Ymd");
        }
        $this->info("🔄 در حال دریافت داده برای insCode: {$insCode} | instrument_id: {$instrumentId}");

        try {
            // ۱. گرفتن تعداد سهام فعلی (zTitad)
            $infoUrl = "https://cdn.tsetmc.com/api/Instrument/GetInstrumentInfo/{$insCode}";
            $infoRes = $this->client->get($infoUrl);
            $info = json_decode($infoRes->getBody(), true);

            $shareCount = (int)($info['instrumentInfo']['zTitad'] ?? 0);
            $symbolName = $info['instrumentInfo']['lVal30'] ?? 'نامشخص';

            $balanceSheet = BalanceSheet::query()
                ->whereHas("instrument", function (Builder $query) use ($insCode, $shareCount) {
                    return $query->where("tse_tmc_code", $insCode);
                })
                ->first();
            if (!empty($balanceSheet)) {
                $balanceSheetShareCount = $balanceSheet->capital / 1000;
                $shareCount = max($shareCount, $balanceSheetShareCount);
            }

            $this->info("📌 نام نماد: {$symbolName}");
            $this->info("📌 تعداد سهام: " . number_format($shareCount));

            // ۲. گرفتن تاریخچه قیمت روزانه
//            $priceUrl = "https://cdn.tsetmc.com/api/ClosingPrice/GetClosingPriceDailyList/{$insCode}/{$days}";
            $priceUrl = "https://cdn.tsetmc.com/api/ClosingPrice/GetClosingPriceHistory/{$insCode}/{$date}";
            $priceRes = $this->client->get($priceUrl);
            $prices = json_decode($priceRes->getBody(), true);

            $saved = 0;
            foreach ($prices["closingPriceHistory"] as $item) {
                $date = Carbon::createFromFormat('Ymd', $date)->startOfDay()->format("Y-m-d H:i:s");
                dd([
                    'instrument_id' => $instrumentId,
                    'open' => (int)($item['priceFirst'] ?? 0),
                    'high' => (int)($item['priceMax'] ?? 0),
                    'low' => (int)($item['priceMin'] ?? 0),
                    'close' => (int)($item['pClosing'] ?? 0),
                    'share_count' => $shareCount,                    // فعلاً مقدار فعلی
                    'tarikh' => PersianDate::gregorianToSolar($date),
                    'date_time' => $date,
                    'timestamp' => Carbon::parse($date)->timestamp,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $data = [
                    'instrument_id' => $instrumentId,
                    'open' => (int)($item['priceFirst'] ?? 0),
                    'high' => (int)($item['priceMax'] ?? 0),
                    'low' => (int)($item['priceMin'] ?? 0),
                    'close' => (int)($item['pClosing'] ?? 0),
                    'share_count' => $shareCount,                    // فعلاً مقدار فعلی
                    'tarikh' => PersianDate::gregorianToSolar($date),
                    'date_time' => $date,
                    'timestamp' => $date->timestamp,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                dd($data);
                // ذخیره مستقیم در دیتابیس
                InstrumentPrice::query()
                    ->updateOrCreate(
                        [
                            'instrument_id' => $instrumentId,
                            'date_time' => $date,
                        ],
                        $data
                    );

                $saved++;
            }

            $this->info("✅ {$saved} رکورد با موفقیت ذخیره/به‌روزرسانی شد.");
            $this->info("📊 تعداد سهام استفاده شده: " . number_format($shareCount));

        } catch (RequestException $e) {
            $this->error('❌ خطا در دریافت داده: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->error('❌ خطای غیرمنتظره: ' . $e->getMessage());
        }
    }
}
