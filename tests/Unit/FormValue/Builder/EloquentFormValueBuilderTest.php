<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Builder;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use GitBalocco\LaravelUiViewComposer\FormValue\Builder\EloquentFormValueBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\FormValue\Builder\EloquentFormValueBuilder
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Builder\EloquentFormValueBuilderTest
 */
class EloquentFormValueBuilderTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = EloquentFormValueBuilder::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $model = \Mockery::mock(Model::class);
        $targetClass = new $this->testClassName('999', $model);
        $this->assertInstanceOf(FormValueBuilder::class, $targetClass);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $model) {
                $this->assertSame($model, $targetClass->model);
                $this->assertSame('999', $targetClass->identity);
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
        $stubResult = \Mockery::mock(\stdClass::class);
        $stubResult->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn(['result' => 'array']);

        $model = \Mockery::mock(Model::class);

        $model->shouldReceive('findOrFail')
            ->with('123456')
            ->once()
            ->andReturn($stubResult);

        $targetClass = new $this->testClassName('123456', $model);
        $actual = $targetClass->build();
        $this->assertInstanceOf(Collection::class, $actual);
        $this->assertSame(['result' => 'array'], $actual->toArray());
    }
}
