<?php

namespace GitBalocco\LaravelUiViewComposer\Contract;

use Illuminate\Support\Collection;

/**
 * Interface FormValueBuilder
 * FormValuesBuilder が実装するべきメソッド
 * @package GitBalocco\LaravelUiViewComposer\Contract
 */
interface FormValueBuilder
{
    /**
     * @return Collection
     */
    public function build(): Collection;
}
