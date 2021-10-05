<?php

namespace GitBalocco\LaravelUiViewComposer\Contract;

use Illuminate\View\View;

/**
 * Interface ViewComposerInterface
 * ViewComposerが実装するべきメソッド。
 * Laravelの暗黙のルールをinterface化しただけのもの。
 * @package GitBalocco\LaravelUiViewComposer\Contract
 */
interface ViewComposerInterface
{
    /**
     * @param View $view
     */
    public function compose(View $view): void;
}
