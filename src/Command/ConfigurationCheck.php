<?php

namespace GitBalocco\LaravelUiViewComposer\Command;

use GitBalocco\LaravelUiViewComposer\Config\SettingItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class ConfigurationCheck extends Command
{
    /** @var string $signature */
    protected $signature = 'laravel-ui-view-composer:config-check';
    /** @var string $description */
    protected $description = '設定ファイルの検証を行う';

    /** @var ConfigurationCheckHandler $handler */
    private $handler;

    public function handle()
    {
        $this->init();
        $this->commonConfig();
        $this->details();
    }

    private function init()
    {
        $this->handler = App::make(ConfigurationCheckHandler::class);
    }

    private function commonConfig()
    {
        $this->info('==== common setting ====');
        $this->line('enable:' . ($this->handler->getConfig()->isEnable() ? 'true' : 'false'));
        $this->line('suffix:' . $this->handler->getConfig()->getSuffix());
        $this->line('interface:' . $this->handler->getConfig()->getInterface());

        if ($this->handler->getConfig()->interfaceExists()) {
            $this->line('[OK] Interface/class has found.');
        } else {
            $this->warn('[NG] ' . $this->handler->getConfig()->getInterface() . ' doesnt exist.');
        }
    }

    /**
     * @return void
     */
    private function details()
    {
        $this->info('==== detail ====');
        ;

        $i = 1;
        /** @var SettingItem $settingItem */
        foreach ($this->handler->details() as $array) {
            $detail = [];
            $this->info('[Setting No.' . $i . '] ' . $array['composerPath']);
            foreach ($array['details'] as $arrayDetail) {
                $detail[] = $this->detail($arrayDetail);
            }
            $this->table(['Found ViewComposers', 'implements', 'view name', 'view exists', 'status'], $detail);

            $i++;
        }
    }

    /**
     * @param array $detail
     * @return array
     */
    private function detail(array $detail): array
    {
        $detail[1] = $detail[1] ? 'YES' : 'NO';
        $detail[3] = $detail[3] ? 'YES' : 'NO';
        $detail[4] = $detail[4] ? 'OK' : 'NG';
        return $detail;
    }
}
