<?php

namespace GitBalocco\LaravelUiViewComposer\FormValue\Builder;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class ErrorFormValueBuilder
 * バリデーションエラー発生時のフォーム値を生成するBuilder
 * old() をCollection化して返却する。
 * @package GitBalocco\LaravelUiViewComposer\FormValue\Builder
 */
class ErrorFormValueBuilder implements FormValueBuilder
{
    /** @var Request $request */
    private $request;

    /**
     * ErrorFormValueBuilder constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * build
     *
     * @return Collection
     */
    public function build(): Collection
    {
        return App::make(Collection::class, ['items' => $this->request->old()]);
    }
}
