<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Builder;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use GitBalocco\LaravelUiViewComposer\FormValue\Builder\ArrayFormValueBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\FormValue\Builder\ArrayFormValueBuilder
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Builder\ArrayFormValueBuilderTest
 */
class ArrayFormValueBuilderTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ArrayFormValueBuilder::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $targetClass = new $this->testClassName(['key1' => 'value1']);
        $this->assertInstanceOf(FormValueBuilder::class, $targetClass);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass) {
                $this->assertSame(['key1' => 'value1'], $targetClass->array);
            },
            $this,
            $targetClass
        )->__invoke();

        return $targetClass;
    }

    /**
     * @covers ::build
     */
    public function test_build()
    {
        $targetClass = new $this->testClassName(['key2' => 'value2']);

        $stubResult = collect(['result' => 'collection']);
        App::shouldReceive('make')
            ->with(Collection::class, ['items' => ['key2' => 'value2']])
            ->once()
            ->andReturn($stubResult);
        $actual = $targetClass->build();
        $this->assertSame($stubResult, $actual);
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
