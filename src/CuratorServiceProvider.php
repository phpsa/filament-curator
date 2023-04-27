<?php

namespace Awcodes\Curator;

use Awcodes\Curator\Commands\UpgradeCommand;
use Awcodes\Curator\Observers\MediaObserver;
use Filament\Facades\Filament;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\AssetManager;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CuratorServiceProvider extends PackageServiceProvider
{
    public static string $name = 'curator';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasRoute('web')
            ->hasViews()
            ->hasTranslations()
            ->hasMigration('create_media_table')
            ->hasCommands([
                UpgradeCommand::class
            ])
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishMigrations()
                    ->askToRunMigrations();
            });
    }

    public function packageRegistered(): void
    {
        parent::packageRegistered();

        $this->app->singleton('curator', fn (): Curator => new Curator());

        $this->app->resolving(AssetManager::class, function () {
           FilamentAsset::register([
               AlpineComponent::make('curator', __DIR__ . '/../resources/dist/curator.js'),
               AlpineComponent::make('curation', __DIR__ . '/../resources/dist/curation.js'),
               Css::make('plugin-curator-styles', __DIR__ . '/../resources/dist/curator.css'),
           ], static::$name);
        });
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        app('curator')->getMediaModel()::observe(MediaObserver::class);

        Livewire::component('curator-panel', Components\Modals\CuratorPanel::class);
        Livewire::component('curator-curation', Components\Modals\CuratorCuration::class);

        Blade::component('curator-glider', View\Components\Glider::class);
        Blade::component('curator-curation', View\Components\Curation::class);

        filament()->getCurrentContext()->resources([
            Resources\MediaResource::class,
        ]);
    }
}
