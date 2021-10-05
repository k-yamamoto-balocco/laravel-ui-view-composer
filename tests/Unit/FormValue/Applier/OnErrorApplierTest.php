<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Applier;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueApplier;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use GitBalocco\LaravelUiViewComposer\FormValue\Applier\OnErrorApplier;
use GitBalocco\LaravelUiViewComposer\FormValue\Builder\ErrorFormValueBuilder;
use Illuminate\Http\Request;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\FormValue\Applier\OnErrorApplier
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\FormValue\Applier\OnErrorApplierTest
 */
class OnErrorApplierTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = OnErrorApplier::class;

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $targetClass = new $this->testClassName($stubRequest);
        $this->assertInstanceOf(FormValueApplier::class, $targetClass);

        //テスト対象メソッドの実行
        \Closure::bind(
            function () use ($targetClass, $stubRequest) {
                //assertions of constructor
                $this->assertSame($stubRequest, $targetClass->request);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * @covers ::shouldApply
     */
    public function test_shouldApply()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $stubRequest->shouldReceive('old')->withNoArgs()->twice()->andReturn(['some' => 'values'], []);
        $targetClass = new $this->testClassName($stubRequest);
        //1回目、old() が内容を持つ配列を返すので、Trueと判定される
        $actual1 = $targetClass->shouldApply();
        $this->assertTrue($actual1);
        //2回目、old() が空の配列を返すのでfalseと判定される
        $actual2 = $targetClass->shouldApply();
        $this->assertFalse($actual2);
    }

    /**
     * @covers ::getBuilder
     */
    public function test_getBuilder()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $targetClass = new $this->testClassName($stubRequest);


        //テスト対象メソッドの実行
        $actual = $targetClass->getBuilder();
        //assertions
        $this->assertInstanceOf(FormValueBuilder::class, $actual);
        $this->assertInstanceOf(ErrorFormValueBuilder::class, $actual);
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
