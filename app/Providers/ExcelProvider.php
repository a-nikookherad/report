<?php

namespace App\Providers;

use App\Tools\Excel\ExcelReader;
use App\Tools\Excel\Interfaces\ExcelReaderInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class ExcelProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->bind(ExcelReaderInterface::class, function () {
            return resolve(ExcelReader::class);
        });
    }

    public function provides(): array
    {
        return [ExcelReaderInterface::class];
    }
}
