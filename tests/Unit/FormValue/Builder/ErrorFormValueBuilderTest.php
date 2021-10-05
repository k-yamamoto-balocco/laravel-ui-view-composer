<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Builder;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use GitBalocco\LaravelUiViewComposer\FormValue\Builder\ErrorFormValueBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\FormValue\Builder\ErrorFormValueBuilder
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Builder\ErrorFormValueBuilderTest
 */
class ErrorFormValueBuilderTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = ErrorFormValueBuilder::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $targetClass = new $this->testClassName($stubRequest);
        $this->assertInstanceOf(FormValueBuilder::class, $targetClass);
        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubRequest) {
                //assertions of constructor
                $this->assertSame($stubRequest, $targetClass->request);
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
        $stubRequest = \Mockery::mock(Request::class);
        $stubRequest->shouldReceive('old')
            ->withNoArgs()
            ->once()
            ->andReturn(['key' => 'value']);

        $targetClass = new $this->testClassName($stubRequest);
        $actual = $targetClass->build();
        $this->assertInstanceOf(Collection::class, $actual);
        $this->assertSame(['key' => 'value'], $actual->toArray());
    }
}
