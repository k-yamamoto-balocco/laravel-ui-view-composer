<?php

namespace GitBalocco\LaravelUiViewComposer\Contract;

/**
 * Interface FormValueApplier
 * FormValueApplierが実装するべきメソッド
 * @package GitBalocco\LaravelUiViewComposer\Contract
 */
interface FormValueApplier
{
    /**
     * 適用するかどうかの判定を行う。 trueの場合適用。
     * @return bool
     */
    public function shouldApply(): bool;

    /**
     * 適用するFormValueBuilderを返す。
     * @return FormValueBuilder
     */
    public function getBuilder(): FormValueBuilder;
}
