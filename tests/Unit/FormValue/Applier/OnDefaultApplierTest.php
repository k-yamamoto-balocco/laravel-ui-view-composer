<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Applier;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueApplier;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use GitBalocco\LaravelUiViewComposer\FormValue\Applier\OnDefaultApplier;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\FormValue\Applier\OnDefaultApplier
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Applier\OnDefaultApplierTest
 */
class OnDefaultApplierTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = OnDefaultApplier::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $stubFormValueBuilder = \Mockery::mock(FormValueBuilder::class);
        $targetClass = new $this->testClassName($stubFormValueBuilder);
        $this->assertInstanceOf(FormValueApplier::class, $targetClass);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubFormValueBuilder) {
                //assertions of constructor
                $this->assertSame($stubFormValueBuilder, $targetClass->builder);
            },
            $this,
            $targetClass
        )->__invoke();

        return [$targetClass, $stubFormValueBuilder];
    }

    /**
     * @param array $depends
     * @covers ::shouldApply
     * @depends test___construct
     */
    public function test_shouldApply(array $depends)
    {
        $targetClass = $depends[0];
        //テスト対象メソッドの実行
        $actual = $targetClass->shouldApply();
        //OnDefaultApplier は常にtrueを返す
        $this->assertTrue($actual);
    }

    /**
     * @param array $depends
     * @covers ::getBuilder
     * @depends test___construct
     */
    public function test_getBuilder(array $depends)
    {
        $targetClass = $depends[0];
        $mockBuilder = $depends[1];

        $actual = $targetClass->getBuilder();
        $this->assertSame($mockBuilder, $actual);
        $this->assertInstanceOf(FormValueBuilder::class, $actual);
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
