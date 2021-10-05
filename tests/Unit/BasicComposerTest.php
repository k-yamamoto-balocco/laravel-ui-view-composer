<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit;

use GitBalocco\LaravelUiViewComposer\BasicComposer;
use GitBalocco\LaravelUiViewComposer\Contract\ViewComposerInterface;
use GitBalocco\LaravelUiViewComposer\Contract\ViewParameterCreator;
use Illuminate\View\View;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\BasicComposer
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\BasicComposerTest
 */
class BasicComposerTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = BasicComposer::class;

    /**
     * @coversNothing
     */
    public function test___construct()
    {
        $targetClass = $this->createTargetClass([]);
        $this->assertInstanceOf(ViewParameterCreator::class, $targetClass);
        $this->assertInstanceOf(ViewComposerInterface::class, $targetClass);

        return $targetClass;
    }

    public function createTargetClass($array)
    {
        $targetClass = new class($array) extends BasicComposer {
            /**
             *  constructor.
             */
            public function __construct($array)
            {
                $this->array = $array;
            }

            public function createParameter(): array
            {
                return $this->array;
            }
        };
        return $targetClass;
    }

    /**
     * @covers ::compose
     * @dataProvider dataProvider
     * @param $concreteClassDefinition
     * @param $viewData
     * @param $expects
     */
    public function test_compose($concreteClassDefinition, $viewData, $expects)
    {
        $stubView = \Mockery::mock(View::class);
        $stubView->shouldReceive('getData')
            ->withNoArgs()
            ->once()
            ->andReturn($viewData);

        $stubView->shouldReceive('with')
            ->with($expects)
            ->once();

        $targetClass = $this->createTargetClass($concreteClassDefinition);
        $actual = $targetClass->compose($stubView);
        $this->assertNull($actual);
    }

    public function dataProvider()
    {
        return [
            [
                ['key1' => 'con-value1'],
                ['key1' => 'view-value1'],
                ['key1' => 'view-value1'],
            ],
            [
                ['key1' => 'con-value1', 'key2' => 'con-value2'],
                ['key1' => 'view-value1'],
                ['key1' => 'view-value1', 'key2' => 'con-value2'],
            ],
            [
                ['id' => '111', 'name' => 'con-name'],
                ['id' => null, 'url' => 'some-url'],
                ['id' => null, 'name' => 'con-name', 'url' => 'some-url'],
            ],
        ];
    }

    /**
     * @covers ::getView
     * @covers ::setView
     */
    public function test_getView()
    {
        $stubView = \Mockery::mock(View::class)->shouldIgnoreMissing();
        $stubView->shouldReceive('getData')->once()->andReturn([]);
        /** @var mixed $targetClass */
        $targetClass = $this->createTargetClass([]);


        \Closure::bind(
            function () use ($targetClass, $stubView) {
                //テスト対象メソッドの実行 インスタンス作成直後はnull
                $actual = $targetClass->getView();
                //assertions
                $this->assertNull($actual);

                //compose 実行すると、viewが入る。
                $targetClass->compose($stubView);
                $actual = $targetClass->getView();
                $this->assertSame($stubView, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }
}
