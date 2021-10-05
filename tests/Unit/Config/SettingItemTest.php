<?php

namespace GitBalocco\LaravelUiViewComposer\Test\Unit\Config;

use GitBalocco\LaravelUiViewComposer\Config\SettingItem;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;
use Symfony\Component\Finder\Finder;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\Config\SettingItem
 * GitBalocco\LaravelUiViewComposer\Test\Unit\Config\SettingItemTest
 */
class SettingItemTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = SettingItem::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $arg = [
            'composer-path' => 'composer-path-value',
            'composer-namespace' => 'composer-namespace-value',
            'view-path' => 'view-path-value'
        ];
        $targetClass = new $this->testClassName($arg);
        $this->assertInstanceOf(SettingItem::class, $targetClass);
        return $targetClass;
    }

    /**
     * @param mixed $targetClass
     * @covers ::getComposerPath
     * @depends test___construct
     */
    public function test_getComposerPath($targetClass)
    {
        $actual = $targetClass->getComposerPath();
        //assertions
        $this->assertSame('composer-path-value', $actual);
    }

    /**
     * @param mixed $targetClass
     * @covers ::getViewPath
     * @depends test___construct
     */
    public function test_getViewPath($targetClass)
    {
        $actual = $targetClass->getViewPath();
        //assertions
        $this->assertSame('view-path-value', $actual);
    }

    /**
     * @param mixed $targetClass
     * @covers ::getComposerNamespace
     * @depends test___construct
     */
    public function test_getComposerNamespace($targetClass)
    {
        //テスト対象メソッドの実行
        $actual = $targetClass->getComposerNamespace();
        //assertions
        $this->assertSame('composer-namespace-value', $actual);
    }

    /**
     * @param mixed $targetClass
     * @covers ::createFinder
     * @depends test___construct
     */
    public function test_createFinder($targetClass)
    {
        $suffix = 'SuffixOfComposer';

        $stubFinder = \Mockery::mock(Finder::class);
        $stubFinder->shouldReceive('files->in->name')->with(['*SuffixOfComposer.php'])->once();
        App::shouldReceive('make')->with(Finder::class)->once()->andReturn($stubFinder);
        //テスト対象メソッドの実行
        $actual = $targetClass->createFinder($suffix);
        $this->assertSame($stubFinder, $actual);
    }

    /**
     * @covers ::viewComposerNamespace
     * @depends test___construct
     */
    public function test_viewComposerNamespace()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $targetClass->shouldReceive('getComposerNamespace')->once()->andReturn('ComposerNamespace');
        $targetClass->shouldReceive('pathAsArray')->once()->andReturn(['', 'Path', 'As', 'Array']);
        $stubFileInfo = \Mockery::mock(\SplFileInfo::class);

        //テスト対象メソッドの実行
        $actual = $targetClass->viewComposerNamespace($stubFileInfo, 'Suffix');
        $this->assertSame('ComposerNamespace\\Path\\As\\ArraySuffix',$actual);
    }

    /**
     * @covers ::viewPathAsDotNotation
     */
    public function test_viewPathAsDotNotation(){
        $stubFileInfo = \Mockery::mock(\SplFileInfo::class);

        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $targetClass->shouldReceive('getViewPath')->once()->andReturn('ViewPath');
        $targetClass->shouldReceive('pathAsArray')->once()->andReturn(['', 'Path', 'As', 'Array']);

        //テスト対象メソッドの実行
        $actual = $targetClass->viewPathAsDotNotation($stubFileInfo, 'Suffix');
        //assertions
        $this->assertSame('ViewPath.Path.As.Array',$actual);
    }

    /**
     * @covers ::pathAsArray
     */
    public function test_pathAsArray(){
        $stubFileInfo = \Mockery::mock(\SplFileInfo::class);
        $stubFileInfo->shouldReceive('getPath')->once()->andReturn('ComposerPath/Some/DirPath');
        $stubFileInfo->shouldReceive('getBasename')
            ->with('Suffix.php')
            ->once()
            ->andReturn('FileNameWithOutSuffix');

        $targetClass = \Mockery::mock($this->testClassName)->makePartial();
        $targetClass->shouldReceive('getComposerPath')->once()->andReturn('ComposerPath');


        $actual = $targetClass->pathAsArray($stubFileInfo,'Suffix');
        //assertions
        $this->assertIsArray($actual);
        $this->assertSame('',$actual[0]);
        $this->assertSame('Some',$actual[1]);
        $this->assertSame('DirPath',$actual[2]);
        $this->assertSame('FileNameWithOutSuffix',$actual[3]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        \Mockery::close();
    }


}
