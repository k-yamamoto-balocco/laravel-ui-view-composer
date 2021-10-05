<?php

namespace GitBalocco\LaravelUiViewComposer\FormValue\Applier;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueApplier;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;

/**
 * Class OnDefaultApplier
 * デフォルトフォーム内容決定処理用のApplier
 * このApplierはshouldApply()が必ずtrueを返すので、他のApplierよりも先に登録した場合以降のApplierは無視される点に注意。
 * Class OnDefaultApplier
 * @package GitBalocco\LaravelUiViewComposer\FormValue\Applier
 */
class OnDefaultApplier implements FormValueApplier
{
    /** @var FormValueBuilder $builder */
    private $builder;

    /**
     * OnDefault constructor.
     * @param FormValueBuilder $builder
     */
    public function __construct(FormValueBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * shouldApply
     *
     * @return bool
     */
    public function shouldApply(): bool
    {
        return true;
    }

    /**
     * @return FormValueBuilder
     */
    public function getBuilder(): FormValueBuilder
    {
        return $this->builder;
    }
}
