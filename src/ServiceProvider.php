<?php

namespace GitBalocco\LaravelUiViewComposer;

use GitBalocco\LaravelUiViewComposer\Command\ConfigurationCheck;
use Illuminate\Support\ServiceProvider as BaseProvider;

/**
 * Class ServiceProvider
 * @package GitBalocco\LaravelUiViewComposer
 */
class ServiceProvider extends BaseProvider
{
    public function boot()
    {
        //コマンドの登録
        if ($this->app->runningInConsole()) {
            $this->commands($this->commandsToRegister());
        }

        //リソースのコピー
        $this->publishes($this->itemsToPublish());
    }

    /**
     * @return string[]
     */
    protected function commandsToRegister()
    {
        return [
            ConfigurationCheck::class
        ];
    }

    /**
     * @return array
     */
    protected function itemsToPublish(): array
    {
        $result = [];

        $source = realpath(__DIR__ . '/../resources/vc-autoloader-dist.php');
        $dist = config_path('vc-autoloader.php');
        if ($source && $dist) {
            $result[$source] = $dist;
        }
        return $result;
    }
}
