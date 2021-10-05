<?php

namespace GitBalocco\LaravelUiViewComposer\Test\Feature\Command;

use GitBalocco\LaravelUiViewComposer\Command\ConfigurationCheck;
use Illuminate\Support\Facades\Config;


/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\Command\ConfigurationCheck
 * GitBalocco\LaravelUiViewComposer\Test\Feature\Command\ConfigurationCheckTest
 */
class ConfigurationCheckTest extends Base
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ConfigurationCheck::class;

    /**
     * @covers ::handle
     * @covers ::init
     * @covers ::commonConfig
     * @covers ::details
     */
    public function test_handle()
    {
        Config::set('vc-autoloader.enable', true);
        Config::set('vc-autoloader.suffix', 'Composer');
        Config::set('vc-autoloader.interface', '');
        Config::set('vc-autoloader.settings', []);

        $this->artisan('laravel-ui-view-composer:config-check')
            ->expectsOutput('==== common setting ====')
            ->expectsOutput('enable:true')
            ->expectsOutput('suffix:Composer')
            ->assertExitCode(0);
    }
}
