<?php

namespace CampusFind\LostAndFound\Providers;

use CampusFind\LostAndFound\Contracts\FoundItem;
use CampusFind\LostAndFound\Contracts\FoundItemImage;
use CampusFind\LostAndFound\Contracts\LostFoundCategory;
use CampusFind\LostAndFound\Contracts\LostFoundClaim;
use CampusFind\LostAndFound\Contracts\LostReport;
use CampusFind\LostAndFound\Contracts\PublicLostAndFoundReadContract;
use CampusFind\LostAndFound\Models\FoundItem as FoundItemModel;
use CampusFind\LostAndFound\Models\FoundItemImage as FoundItemImageModel;
use CampusFind\LostAndFound\Models\LostFoundCategory as LostFoundCategoryModel;
use CampusFind\LostAndFound\Models\LostFoundClaim as LostFoundClaimModel;
use CampusFind\LostAndFound\Models\LostReport as LostReportModel;
use CampusFind\LostAndFound\Services\PublicLostAndFoundService;
use Illuminate\Support\ServiceProvider;

class LostAndFoundServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConcordModule();

        $this->app->bind(LostFoundCategory::class, LostFoundCategoryModel::class);
        $this->app->bind(FoundItem::class, FoundItemModel::class);
        $this->app->bind(FoundItemImage::class, FoundItemImageModel::class);
        $this->app->bind(LostReport::class, LostReportModel::class);
        $this->app->bind(LostFoundClaim::class, LostFoundClaimModel::class);

        $this->registerConfig();

        $this->app->singleton(
            PublicLostAndFoundReadContract::class,
            PublicLostAndFoundService::class,
        );
    }

    /**
     * Register Concord module for models and contracts.
     */
    protected function registerConcordModule(): void
    {
        if ($this->app->bound('concord')) {
            $this->app->make('concord')->registerModule(ModuleServiceProvider::class);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'lost_found');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'lost_found');

        $this->loadRoutesFrom(__DIR__.'/../Routes/student-routes.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/employee-routes.php');

        if (file_exists(__DIR__.'/../Routes/breadcrumbs.php')) {
            require __DIR__.'/../Routes/breadcrumbs.php';
        }
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/filesystems.php',
            'filesystems.disks',
        );

        $this->mergeConfigFrom(
            __DIR__.'/../Config/acl.php',
            'acl',
        );

        $this->mergeConfigFrom(
            __DIR__.'/../Config/lost_found.php',
            'lost_found',
        );

        if (file_exists(__DIR__.'/../Config/menu.php')) {
            $this->mergeConfigFrom(
                __DIR__.'/../Config/menu.php',
                'menu.admin'
            );
        }
    }
}
