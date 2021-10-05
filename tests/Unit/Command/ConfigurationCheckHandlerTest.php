<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit\Command;

use GitBalocco\LaravelUiViewComposer\Command\ConfigurationCheckHandler;
use GitBalocco\LaravelUiViewComposer\Config\SettingItem;
use GitBalocco\LaravelUiViewComposer\Config\VcAutoLoaderConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\View\Factory;
use Illuminate\View\ViewFinderInterface;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\Command\ConfigurationCheckHandler
 * GitBalocco\LaravelUiViewComposer\Tests\Command\ConfigurationCheckHandlerTest
 */
class ConfigurationCheckHandlerTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ConfigurationCheckHandler::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $this->setDummyConfig(true, 'Composer', '', []);

        $stubViewFinderInterface = \Mockery::mock(ViewFinderInterface::class);
        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class)->makePartial();
        $viewFactory = \Mockery::mock(Factory::class);
        $viewFactory->shouldReceive('getFinder')->andReturn($stubViewFinderInterface);
        $targetClass = new $this->testClassName($stubConfig, $viewFactory);

        \Closure::bind(
            function () use ($targetClass, $stubConfig, $stubViewFinderInterface) {
                //assertions
                $this->assertSame($stubConfig, $targetClass->config);
                $this->assertSame($stubViewFinderInterface, $targetClass->viewFinder);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    public function setDummyConfig($enable, $suffix, $interface, $settings)
    {
        Config::set('vc-autoloader.enable', $enable);
        Config::set('vc-autoloader.suffix', $suffix);
        Config::set('vc-autoloader.interface', $interface);
        Config::set('vc-autoloader.settings', $settings);
    }

    /**
     * @covers ::detail
     * @dataProvider detailDataProvider
     * @param $isImplements
     * @param $viewFound
     * @param $isOk
     */
    public function test_detail($isImplements, $viewFound, $isOk)
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getConfig->getSuffix')->withNoArgs()->andReturn('Suffix');
        $targetClass->shouldReceive('getConfig->namespaceImplementsInterface')
            ->with('NameSpace')
            ->andReturn($isImplements);
        $targetClass->shouldReceive('foundWithViewFinder')->with('ViewPath')->andReturn($viewFound);


        $stubSettingItem = \Mockery::mock(SettingItem::class);
        $stubFileInfo = \Mockery::mock(\SplFileInfo::class);

        $stubSettingItem->shouldReceive('viewComposerNamespace')
            ->with($stubFileInfo, 'Suffix')
            ->once()
            ->andReturn('NameSpace');
        $stubSettingItem->shouldReceive('viewPathAsDotNotation')
            ->with($stubFileInfo, 'Suffix')
            ->once()
            ->andReturn('ViewPath');

        $actual = $targetClass->detail($stubSettingItem, $stubFileInfo);
        $this->assertIsArray($actual);
        $this->assertSame('NameSpace', $actual[0]);
        $this->assertSame($isImplements, $actual[1]);
        $this->assertSame('ViewPath', $actual[2]);
        $this->assertSame($viewFound, $actual[3]);
        $this->assertSame($isOk, $actual[4]);
    }

    public function detailDataProvider()
    {
        return [
            [true, true, true],
            [true, false, false],
            [false, true, false],
            [false, false, false],
        ];
    }

    /**
     * @covers ::foundWithViewFinder
     */
    public function test_foundWithViewFinder_true()
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getViewFinder->find')->with('argViewPath')->once()->andReturn(null);
        $actual = $targetClass->foundWithViewFinder('argViewPath');
        $this->assertTrue($actual);
    }

    public function test_foundWithViewFinder_false()
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getViewFinder->find')
            ->with('argViewPath')
            ->once()
            ->andThrow(\InvalidArgumentException::class);
        $actual = $targetClass->foundWithViewFinder('argViewPath');
        $this->assertFalse($actual);
    }

    /**
     * @covers ::details
     */
    public function test_details()
    {
        $targetClass = \Mockery::mock($this->testClassName)->shouldAllowMockingProtectedMethods()->makePartial();
        $targetClass->shouldReceive('getConfig->getSuffix')->twice()->andReturn('Suffix');

        //SplFileInfoのスタブ（検出されるファイル）
        $stubSplFileInfo = \Mockery::mock(\SplFileInfo::class);

        //Finderのスタブ（検出したファイル群をイテレータで返却する処理をモック）
        // 1回目呼び出しで3ファイル、2回目呼び出しで2ファイル検出、しているケースの想定
        $stubFinder = \Mockery::mock(\Symfony\Component\Finder\Finder::class);
        $stubFinder->shouldReceive('getIterator')
            ->andReturn(
                new \ArrayIterator([$stubSplFileInfo, $stubSplFileInfo, $stubSplFileInfo]),
                new \ArrayIterator([$stubSplFileInfo, $stubSplFileInfo])
            );

        //SettingItemのスタブ
        $stubSettingItem = \Mockery::mock(SettingItem::class);

        $stubSettingItem->shouldReceive('createFinder')
            ->with('Suffix')
            ->twice() //要素が2なので2回呼ばれる
            ->andReturn($stubFinder);

        $stubSettingItem->shouldReceive('getComposerPath')
            ->withNoArgs()
            ->twice() //要素が2なので2回呼ばれる
            ->andReturn('ComposerPath');

        //設定のロード、設定スタブを2つもつ配列を返却する。
        $targetClass->shouldReceive('getConfig->getValidDirectory')
            ->withNoArgs()
            ->once()
            ->andReturn([$stubSettingItem, $stubSettingItem]);

        //設定が2件、設定1は3ファイル検出、設定2は2ファイル検出、という場合の動作をするはず。
        //したがって、5回呼び出される。
        $targetClass->shouldReceive('detail')
            ->with($stubSettingItem, $stubSplFileInfo)
            ->times(5)
            ->andReturn(['result']);

        $actual = $targetClass->details();
        $actual = iterator_to_array($actual);

        $this->assertIsArray($actual);
        $this->assertCount(2, $actual);
        $this->assertCount(3, $actual[0]['details']);
        $this->assertCount(2, $actual[1]['details']);
    }

    /**
     * @covers ::getConfig
     */
    public function test_getConfig()
    {
        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class)->makePartial();
        $viewFactory = \Mockery::mock(Factory::class);
        $viewFactory->shouldReceive('getFinder')->andReturnNull();
        $targetClass = new $this->testClassName($stubConfig, $viewFactory);
        $actual = $targetClass->getConfig();
        $this->assertSame($stubConfig, $actual);
    }

    /**
     * @covers ::getViewFinder
     */
    public function test_getViewFinder()
    {
        $stubConfig = \Mockery::mock(VcAutoLoaderConfig::class)->makePartial();
        $viewFactory = \Mockery::mock(Factory::class);
        $stubViewFinderInterface = \Mockery::mock(ViewFinderInterface::class);
        $viewFactory->shouldReceive('getFinder')->andReturn($stubViewFinderInterface);
        $targetClass = new $this->testClassName($stubConfig, $viewFactory);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubViewFinderInterface) {
                $actual = $targetClass->getViewFinder();
                //assertions
                $this->assertSame($stubViewFinderInterface, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}

