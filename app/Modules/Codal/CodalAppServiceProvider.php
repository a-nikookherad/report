<?php

namespace App\Modules\Codal;

use App\Modules\Codal\Contracts\DataSourceExtractorInterface;
use App\Modules\Codal\PersistenceServices\FileSystemPersist;
use App\Modules\Codal\Services\DataSourceExtractorV1;
use Illuminate\Support\ServiceProvider;

class CodalAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FileSystemPersist::class);
        $this->app->tag([
            FileSystemPersist::class
        ], "persistences");

        $this->app->bind(DataSourceExtractorInterface::class, DataSourceExtractorV1::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
