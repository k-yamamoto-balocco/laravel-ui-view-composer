<?php

namespace GitBalocco\LaravelUiViewComposer\Tests\Unit;

use GitBalocco\CommonStructures\StackableArray;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueApplier;
use GitBalocco\LaravelUiViewComposer\Contract\ViewComposerInterface;
use GitBalocco\LaravelUiViewComposer\Contract\ViewParameterCreator;
use GitBalocco\LaravelUiViewComposer\Exception\InvalidFormValuesApplierException;
use GitBalocco\LaravelUiViewComposer\FormComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Orchestra\Testbench\TestCase;

/**
 * @coversDefaultClass \GitBalocco\LaravelUiViewComposer\FormComposer
 * GitBalocco\LaravelUiViewComposer\Tests\Unit\FormComposerTest
 */
class FormComposerTest extends TestCase
{
    /** @var $testClassName as test target class name */
    protected $testClassName = FormComposer::class;

    /**
     * @covers ::setFormValuesAppliers
     */
    public function test_setFormValuesAppliers_RaiseException()
    {
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $argArray = new StackableArray();
        $argArray->addValue([]);

        $this->expectException(InvalidFormValuesApplierException::class);
        $targetClass->setFormValuesAppliers($argArray);
    }

    /**
     * @covers ::setFormValuesAppliers
     */
    public function test_setFormValuesAppliers()
    {
        $argArray = new StackableArray();
        $stubFormValueApplier = \Mockery::mock(FormValueApplier::class);
        $argArray->addValue($stubFormValueApplier);

        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        $actual = $targetClass->setFormValuesAppliers($argArray);
        $this->assertInstanceOf(FormComposer::class, $actual);
    }

    /**
     * @covers ::addFormValuesApplier
     */
    public function test_addFormValuesApplier()
    {

        $stubFormValueApplier = \Mockery::mock(FormValueApplier::class)->shouldIgnoreMissing();
        $stubArray = \Mockery::mock(StackableArray::class)->makePartial()->shouldIgnoreMissing();
        $stubArray->shouldReceive('addValue')->with($stubFormValueApplier)->once();
        //??????????????????????????????????????????
        $targetClass = \Mockery::mock($this->testClassName)->makePartial()->shouldAllowMockingProtectedMethods();
        //??????????????????????????????????????????
        $targetClass->setFormValuesAppliers($stubArray);
        //??????????????????????????????????????????????????????addValue() ????????????????????????????????????
        $actual = $targetClass->addFormValuesApplier($stubFormValueApplier);
        $this->assertInstanceOf(FormComposer::class, $actual);
    }

    /**
     * @covers ::setFormValueParameterName
     */
    public function test_setFormValueParameterName()
    {
        /** @var mixed $targetClass */
        $targetClass = $this->createEmptyInitConcreteObject(\Mockery::mock(Request::class));

        //????????????????????????????????????
        $actual = $targetClass->setFormValueParameterName('any-parameter-name');
        $this->assertInstanceOf(FormComposer::class, $actual);
        \Closure::bind(
            function () use ($targetClass) {
                //assertions
                $this->assertSame('any-parameter-name', $targetClass->formValueParameterName);
            },
            $this,
            FormComposer::class
        )->__invoke();
    }

    /**
     * ???????????????????????????????????????
     */
    public function createEmptyInitConcreteObject($stubRequest)
    {
        return new class($stubRequest) extends FormComposer {
            protected function init(Request $request, View $view): void
            {
                //do nothing
            }
        };
    }

    /**
     * @covers ::setView
     */
    public function test_setView()
    {
        $stubView = \Mockery::mock(View::class);
        /** @var mixed $targetClass */
        $targetClass = $this->createEmptyInitConcreteObject(\Mockery::mock(Request::class));

        \Closure::bind(
            function () use ($targetClass, $stubView) {
                //????????????????????????????????????
                $targetClass->setView($stubView);
                //assertions
                $this->assertSame($stubView, $targetClass->view);
            },
            $this,
            FormComposer::class
        )->__invoke();
    }

    /**
     * @covers ::getView
     */
    public function test_getView()
    {
        $stubView = \Mockery::mock(View::class)->shouldIgnoreMissing();
        $stubView->shouldReceive('getData')->andReturn([]);

        /** @var mixed $targetClass */
        $targetClass = $this->createEmptyInitConcreteObject(\Mockery::mock(Request::class));

        \Closure::bind(
            function () use ($targetClass, $stubView) {
                //????????????????????????????????????1.?????????????????????????????????NULL
                $actual1 = $targetClass->getView();
                $this->assertNull($actual1);

                //????????????????????????????????????2.setView???????????????????????????
                //assertions
                $targetClass->setView($stubView);
                $actual2 = $targetClass->getView();
                $this->assertSame($stubView, $actual2);
            },
            $this,
            FormComposer::class
        )->__invoke();
    }

    /**
     * @covers ::getRequest
     */
    public function test_getRequest()
    {
        $stubRequest = \Mockery::mock(Request::class);
        /** @var mixed $targetClass */
        $targetClass = $this->createEmptyInitConcreteObject($stubRequest);

        //????????????????????????????????????
        \Closure::bind(
            function () use ($targetClass, $stubRequest) {
                $actual = $targetClass->getRequest();
                //assertions
                $this->assertSame($stubRequest, $actual);
            },
            $this,
            $targetClass
        )->__invoke();
    }

    /**
     * ViewParameterCreator?????????????????????????????????????????????????????????????????????????????????
     * ???????????????????????????????????????
     * @covers ::parameters
     */
    public function test_parameters_NotViewParameterCreator()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $targetClass = $this->createEmptyInitConcreteObject($stubRequest);
        //????????????????????????????????????
        \Closure::bind(
        /** @var mixed $targetClass */
            function () use ($targetClass) {
                $actual = $targetClass->parameters(['result-of' => 'view->getData()']);
                //assertions ???????????????????????????
                $this->assertSame(['result-of' => 'view->getData()'], $actual);
            },
            $this,
            FormComposer::class
        )->__invoke();
    }

    /**
     * @covers ::parameters
     */
    public function test_parameters_ImplementsViewParameterCreator()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $targetClass = new class($stubRequest) extends FormComposer implements ViewParameterCreator {
            protected function init(Request $request, View $view): void
            {
            }

            public function createParameter(): array
            {
                return ['parameters' => 'values', 'id' => 999];
            }
        };

        //????????????????????????????????????
        \Closure::bind(
        /** @var mixed $targetClass */
            function () use ($targetClass) {
                $actual = $targetClass->parameters(['result-of' => 'view->getData()', 'id' => null]);

                //assertions View???????????????????????????????????????????????????
                $this->assertIsArray($actual);
                $this->assertArrayHasKey('id', $actual);
                //assertions View?????????????????????????????????id?????????NULL????????????????????????
                $this->assertSame(null, $actual['id']);
                $this->assertArrayHasKey('parameters', $actual);
                $this->assertSame('values', $actual['parameters']);
                $this->assertArrayHasKey('result-of', $actual);
                $this->assertSame('view->getData()', $actual['result-of']);
            },
            $this,
            FormComposer::class
        )->__invoke();
    }

    /**
     * @covers ::formValues
     */
    public function test_formValues_EmptyCollection()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $targetClass = $this->createEmptyInitConcreteObject($stubRequest);

        \Closure::bind(
        /** @var mixed $targetClass */
            function () use ($targetClass) {
                //???????????????????????????????????????
                $targetClass->setFormValuesAppliers(new StackableArray());
                //assertions
                $actual = $targetClass->formValues();
                $this->assertInstanceOf(Collection::class, $actual);
                $this->assertSame(0, $actual->count());
            },
            $this,
            FormComposer::class
        )->__invoke();
    }

    /**
     * @covers ::formValues
     */
    public function test_formValues_default()
    {
        $stubRequest = \Mockery::mock(Request::class);
        $stubApplier = \Mockery::mock(FormValueApplier::class);

        //shouldApply() ???false,true,false ??????????????????????????????2?????????true??????????????????????????????2???????????????????????????????????????
        $stubApplier->shouldReceive('shouldApply')->withNoArgs()->times(2)->andReturn(false, true, false);
        //shouldApply() ???true????????????????????????getBuilder->build() ????????????????????????
        $stubApplier->shouldReceive('getBuilder->build')->withNoArgs()->once()->andReturn(collect(['result']));

        $targetClass = $this->createEmptyInitConcreteObject($stubRequest);

        \Closure::bind(
        /** @var mixed $targetClass */
            function () use ($targetClass, $stubApplier) {
                //???????????????????????????????????????
                $array = new StackableArray();
                //applier???3?????????
                $array->addValue($stubApplier);
                $array->addValue($stubApplier);
                $array->addValue($stubApplier);
                //set
                $targetClass->setFormValuesAppliers($array);
                //assertions
                $actual = $targetClass->formValues();
                $this->assertInstanceOf(Collection::class, $actual);
                $this->assertSame(['result'], $actual->toArray());
            },
            $this,
            FormComposer::class
        )->__invoke();
    }

    /**
     * @covers ::compose
     */
    public function test_compose()
    {
        $stubRequest = \Mockery::mock(Request::class)->shouldIgnoreMissing();
        $stubView = \Mockery::mock(View::class);
        $stubView->shouldReceive('getData')->withNoArgs()->once()->andReturn([]);
        $stubView->shouldReceive('with')->once();

        $targetClass = \Mockery::mock($this->testClassName, [$stubRequest])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $targetClass->shouldReceive('init')->once()->with($stubRequest, $stubView);


        //????????????????????????????????????
        $actual = $targetClass->compose($stubView);
        $this->assertNull($actual);
    }

    /**
     * @covers ::__construct
     */
    public function test___construct()
    {
        $stubRequest = \Mockery::mock(Request::class)->shouldIgnoreMissing();
        $targetClass = $this->createEmptyInitConcreteObject($stubRequest);
        $this->assertInstanceOf(FormComposer::class, $targetClass);
        $this->assertInstanceOf(ViewComposerInterface::class, $targetClass);
    }

    protected function tearDown(): void
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        \Mockery::close();
    }
}
