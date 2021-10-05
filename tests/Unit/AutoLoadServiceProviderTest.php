<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit;

use GitBalocco\LaravelUiViewComposer\AutoLoadServiceProvider;
use GitBalocco\LaravelUiViewComposer\Config\SettingItem;
use GitBalocco\LaravelUiViewComposer\Config\VcAutoLoaderConfig;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\AutoLoadServiceProvider
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\AutoLoadServiceProviderTest
 */
class AutoLoadServiceProviderTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = AutoLoadServiceProvider::class;

    /**
     * @covers ::boot
     */
    public function test_boot_Disabled()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class);
        $stubConfig->shouldReceive('isEnable')->once()->andReturnFalse();
        App::shouldReceive('make')->with(VcAutoLoaderConfig::class)->once()->andReturn($stubConfig);
        App::makePartial();
        $targetClass->boot();
    }

    /**
     * @covers ::boot
     */
    public function test_boot_Enabled()
    {
        $stubSettingItem = \Mockery::mock(SettingItem::class);

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('registerViewComposer')->with($stubSettingItem)->twice();

        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class);
        $stubConfig->shouldReceive('isEnable')->once()->andReturnTrue();
        $stubConfig->shouldReceive('getValidDirectory')->once()->andReturn([$stubSettingItem, $stubSettingItem]);
        App::shouldReceive('make')->with(VcAutoLoaderConfig::class)->once()->andReturn($stubConfig);
        App::makePartial();

        $actual = $targetClass->boot();

        //assertions
        $this->assertNull($actual);
    }

    /**
     * @covers ::registerViewComposer
     */
    public function test_registerViewComposer()
    {
        $stubFileInfo = \Mockery::mock(\SplFileInfo::class);

        $stubFinder = \Mockery::mock(Finder::class);
        $stubFinder->shouldReceive('getIterator')->once()->andReturn(
            new \ArrayIterator([$stubFileInfo, $stubFileInfo])
        );


        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class);
        $stubConfig->shouldReceive('getSuffix')->once()->andReturn('Suffix');

        $stubSettingItem = \Mockery::mock(SettingItem::class);
        $stubSettingItem->shouldReceive('createFinder')->with('Suffix')->once()->andReturn($stubFinder);

        App::shouldReceive('make')->with(VcAutoLoaderConfig::class)->once()->andReturn($stubConfig);
        App::makePartial();

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $targetClass->shouldReceive('registerByFileInfo')->with($stubFileInfo, $stubSettingItem)->twice();

        //テスト対象メソッドの実行
        $targetClass->registerViewComposer($stubSettingItem);
    }

    /**
     * @covers ::registerByFileInfo
     */
    public function test_registerByFileInfo_DoesntRegister()
    {
        $stubFileInfo = \Mockery::mock(\SplFileInfo::class)->shouldIgnoreMissing()->shouldAllowMockingProtectedMethods();
        $stubSettingItem = \Mockery::mock(SettingItem::class)->shouldIgnoreMissing()->shouldAllowMockingProtectedMethods();
        $stubSettingItem->shouldReceive('viewComposerNamespace')->andReturn('ComposerNamespace');

        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class)->shouldIgnoreMissing();
        $stubConfig->shouldReceive('getSuffix')->andReturn('Suffix');
        //Falseを返却するので、登録が実施されない
        $stubConfig->shouldReceive('namespaceImplementsInterface')->once()->andReturnFalse();

        App::shouldReceive('make')->with(VcAutoLoaderConfig::class)->once()->andReturn($stubConfig);
        App::makePartial();

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();

        $targetClass->registerByFileInfo($stubFileInfo, $stubSettingItem);
    }

    public function test_registerByFileInfo_DoesRegister(){
        $stubFileInfo = \Mockery::mock(\SplFileInfo::class)->shouldIgnoreMissing()->shouldAllowMockingProtectedMethods();
        $stubSettingItem = \Mockery::mock(SettingItem::class)->shouldIgnoreMissing()->shouldAllowMockingProtectedMethods();
        $stubSettingItem->shouldReceive('viewComposerNamespace')->andReturn('ComposerNamespace');

        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class)->shouldIgnoreMissing();
        $stubConfig->shouldReceive('getSuffix')->andReturn('Suffix');
        //Trueを返却するので、登録が実施される
        $stubConfig->shouldReceive('namespaceImplementsInterface')->once()->andReturnTrue();

        App::shouldReceive('make')->with(VcAutoLoaderConfig::class)->once()->andReturn($stubConfig);
        App::makePartial();

        View::shouldReceive('composer')->once();

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();

        $targetClass->registerByFileInfo($stubFileInfo, $stubSettingItem);

    }


    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
