<?php

declare(strict_types=1);

namespace TareqAlqadi\FilamentMapPicker;

use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMapPickerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'map-picker';

    public static string $viewNamespace = 'map-picker';

    public function configurePackage(Package $package): void
    {

        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('tareq-alqadi/filament-map-picker');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        if (file_exists($package->basePath('/../resources/lang'))) {
            $package->hasTranslations();
        }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered(): void
    {
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());
    }

    protected function getAssetPackageName(): ?string
    {
        return 'tareq-alqadi/filament-map-picker';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            AlpineComponent::make('map-picker-component', __DIR__.'/../resources/dist/components/map-picker-component.js'),
            Css::make('filament-map-picker-css', __DIR__.'/../resources/dist/filament-map-picker.css')->loadedOnRequest(),
            Js::make('filament-map-picker-js', __DIR__.'/../resources/dist/filament-map-picker.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
        ];
    }
}
