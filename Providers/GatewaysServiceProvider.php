<?php

namespace Modules\Gateways\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Modules\Gateways\Entities\Setting;

class GatewaysServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected string $moduleName = 'Gateways';

    /**
     * @var string $moduleNameLower
     */
    protected string $moduleNameLower = 'Gateways';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        require_once base_path('/Modules/Gateways/Library/Helper.php');
        require_once base_path('/Modules/Gateways/Library/CryptoCCavenue.php');

        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'Database/Migrations'));

        $info = include(module_path($this->moduleName, 'Addon/info.php'));
        if ($info['is_published']) {
            foreach ($info['migrations'] as $increment => $migration) {
                if ($migration['value'] == 0) {
                    $count = Setting::whereIn('key_name', $migration['key_names'])->where('settings_type', $migration['settings_type'])->count();
                    if ($count < count($migration['key_names'])) {
                        $sql = File::get(base_path('Modules/Gateways/Database/Updates/' . $migration['key']));
                        DB::unprepared($sql);
                    }
                    $info['migrations'][$increment] = ['key' => $migration['key'], 'value' => 1, 'key_names' => $migration['key_names'], 'settings_type' => $migration['settings_type']];
                    $str = "<?php return " . var_export($info, true) . ";";
                    file_put_contents(base_path('Modules/Gateways/Addon/info.php'), $str);
                }
            }

            if (!$info['class_files_updated']) {
                $module_payment_trait = base_path('Modules/Gateways/Traits/Payment.php');
                if (File::exists('Modules/PaymentModule/Traits/Payment.php')) {
                    $system_payment_trait = base_path('Modules/PaymentModule/Traits/Payment.php');
                    $text_to_be_set = 'namespace Modules\PaymentModule\Traits;';
                } else {
                    $system_payment_trait = base_path('app/Traits/Payment.php');
                    $text_to_be_set = 'namespace App\Traits;';
                }
                copy($module_payment_trait, $system_payment_trait);

                $file_content = file($system_payment_trait);
                if (isset($file_content[3 - 1])) {
                    $file_content[3 - 1] = rtrim($text_to_be_set) . "\n";
                    file_put_contents($system_payment_trait, implode('', $file_content));
                }

                $info['class_files_updated'] = 1;
                $str = "<?php return " . var_export($info, true) . ";";
                file_put_contents(base_path('Modules/Gateways/Addon/info.php'), $str);
            }
        }

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'Config/config.php') => config_path($this->moduleNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/config.php'), $this->moduleNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);

        $sourcePath = module_path($this->moduleName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations(): void
    {
        $langPath = resource_path('lang/modules/' . $this->moduleNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->moduleNameLower);
            $this->loadJsonTranslationsFrom($langPath);
        } else {
            $this->loadTranslationsFrom(module_path($this->moduleName, 'Resources/lang'), $this->moduleNameLower);
            $this->loadJsonTranslationsFrom(module_path($this->moduleName, 'Resources/lang'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (Config::get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
