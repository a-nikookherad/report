<?php

namespace App\Providers;

use App\Models\Bourse\Activity;
use App\Models\Bourse\BalanceSheet;
use App\Models\Bourse\CashFlow;
use App\Models\Bourse\History;
use App\Models\Bourse\IncomeStatement;
use App\Observers\ActivityObserver;
use App\Observers\BalanceSheetObserver;
use App\Observers\CashFlowObserver;
use App\Observers\IncomeStatementObserver;
use App\Observers\PriceObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        History::observe(PriceObserver::class);
        Activity::observe(ActivityObserver::class);
        IncomeStatement::observe(IncomeStatementObserver::class);
        BalanceSheet::observe(BalanceSheetObserver::class);
        CashFlow::observe(CashFlowObserver::class);
    }
}
