<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit\Config;

use GitBalocco\LaravelUiViewComposer\Config\SettingItem;
use GitBalocco\LaravelUiViewComposer\Config\VcAutoLoaderConfig;
use Illuminate\Support\Facades\Config;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\Config\VcAutoLoaderConfig
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\Config\VcAutoLoaderConfigTest
 */
class VcAutoLoaderConfigTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = VcAutoLoaderConfig::class;

    /**
     * @covers ::isEnable
     */
    public function test_isEnable()
    {
        $targetClass = new $this->testClassName();
        Config::set('vc-autoloader.enable', '1');
        $actual = $targetClass->isEnable();
        $this->assertTrue($actual);

        Config::set('vc-autoloader.enable', null);
        $actual = $targetClass->isEnable();
        $this->assertFalse($actual);
    }

    /**
     * @covers ::getSuffix
     */
    public function test_getSuffix()
    {
        $targetClass = new $this->testClassName();
        Config::set('vc-autoloader.suffix', 'suffix.ext');
        $actual = $targetClass->getSuffix();
        $this->assertSame('suffix.ext', $actual);
    }

    /**
     * @covers ::getInterface
     */
    public function test_getInterface()
    {
        $targetClass = new $this->testClassName();
        Config::set('vc-autoloader.interface', 'interface-name');
        $actual = $targetClass->getInterface();
        $this->assertSame('interface-name', $actual);
    }

    /**
     * @covers ::getValidDirectory
     * @covers ::isValidSettings
     */
    public function test_getValidDirectory_NoValidSettings()
    {
        $targetClass = new $this->testClassName();
        $settings = [
            [
                'composer-path' => '',
                'composer-namespace' => '',
                'view-path' => ''
            ],
            [
                'composer-path' => 'aaaa',
                'composer-namespace' => '',
                'view-path' => ''
            ],
            [
                'composer-path' => 'aaaa',
                'composer-namespace' => '',
                'view-path' => 'bbbb'
            ],

        ];
        Config::set('vc-autoloader.settings', $settings);
        $actual = $targetClass->getValidDirectory();
        $this->assertIsIterable($actual);
        $this->assertSame([], iterator_to_array($actual));
    }

    /**
     * @covers ::getValidDirectory
     * @covers ::isValidSettings
     */
    public function test_getValidDirectory_HasValidSettings()
    {
        $targetClass = new $this->testClassName();
        $settings = [
            [
                'composer-path' => __DIR__,
                'composer-namespace' => '',
                'view-path' => 'viewPath'
            ]
        ];
        Config::set('vc-autoloader.settings', $settings);
        $actual = $targetClass->getValidDirectory();
        $this->assertIsIterable($actual);

        $asArray = iterator_to_array($actual);
        $this->assertInstanceOf(SettingItem::class, $asArray[0]);
    }

    /**
     * @covers ::interfaceExists
     */
    public function test_interfaceExists_ReturnsTrue1()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        //適当に存在するインターフェース名を返す
        $targetClass->shouldReceive('getInterface')->andReturn(\ArrayAccess::class);
        $actual = $targetClass->interfaceExists();
        $this->assertTrue($actual);
    }

    /**
     * @covers ::interfaceExists
     */
    public function test_interfaceExists_ReturnsTrue2()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        //適当に存在するクラス名を返す
        $targetClass->shouldReceive('getInterface')->andReturn(\stdClass::class);
        $actual = $targetClass->interfaceExists();
        $this->assertTrue($actual);
    }

    /**
     * @covers ::interfaceExists
     */
    public function test_interfaceExists_ReturnsFalse()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        //クラスとして存在しない文字列を返すよう指定
        $targetClass->shouldReceive('getInterface')->andReturn('invalid class name');
        $actual = $targetClass->interfaceExists();
        $this->assertFalse($actual);
    }

    /**
     * configのinterfaceが空でなく、引数のクラス名がinterfaceを実装/継承 している場合true
     * @covers ::namespaceImplementsInterface
     */
    public function test_namespaceImplementsInterface_ReturnsTrue1(){
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();

        //実装or継承すべきクラス名としてstdClassを返却するよう指示
        $targetClass->shouldReceive('getInterface')->andReturn(\stdClass::class);
        //stdClassを継承した無名クラスを作成
        $object=new class() extends \stdClass{};

        $actual = $targetClass->namespaceImplementsInterface(get_class($object));

        //assertions
        $this->assertTrue($actual);
    }

    /**
     * configのinterfaceが空の場合、trueが返却される
     * @covers ::namespaceImplementsInterface
     */
    public function test_namespaceImplementsInterface_ReturnsTrue2(){
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();

        //実装or継承すべきクラス名として 空文字列 を返却するよう指示
        $targetClass->shouldReceive('getInterface')->andReturn('');
        //無名クラスを作成
        $object=new class() {};

        $actual = $targetClass->namespaceImplementsInterface(get_class($object));

        //assertions
        $this->assertTrue($actual);
    }

    /**
     * @covers ::namespaceImplementsInterface
     */
    public function test_namespaceImplementsInterface_ReturnsFalse(){
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();

        //実装or継承すべきクラス名としてstdClassを返却するよう指示
        $targetClass->shouldReceive('getInterface')->andReturn(\stdClass::class);
        //無名クラスを作成
        $object=new class() {};

        $actual = $targetClass->namespaceImplementsInterface(get_class($object));

        //assertions
        $this->assertFalse($actual);
    }


    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
