<?php

namespace GitBalocco\LaravelUiViewComposer;

use GitBalocco\LaravelUiViewComposer\Contract\ViewComposerInterface;
use GitBalocco\LaravelUiViewComposer\Contract\ViewParameterCreator;
use Illuminate\View\View;

/**
 * Class BasicComposer
 * 単純なViewComposerの基底クラス。
 * このクラスを継承したViewComposerを作成し、createParameter()にViewにアサインしたい内容を配列で返却する処理を定義する。
 * @package GitBalocco\LaravelPresentations\Http\View\Composer
 */
abstract class BasicComposer implements ViewParameterCreator, ViewComposerInterface
{
    /** @var View|null $view */
    private $view;

    /**
     * @param View $view
     */
    final public function compose(View $view): void
    {
        //プロパティにセット
        $this->setView($view);
        //コントローラーでアサイン済の変数を取得
        $assigned = $view->getData();
        //コントローラーで割り当てられている場合、そのパラメータが優先（array_mergeはあと勝ち）
        $parameters = array_merge($this->createParameter(), $assigned);
        //viewに渡す
        $view->with($parameters);
    }

    /**
     * @param View $view
     */
    private function setView(View $view): void
    {
        $this->view = $view;
    }

    /**
     * ViewComposerからviewにアサインしたい内容を配列で返却する。
     * @return array
     */
    abstract public function createParameter(): array;

    /**
     * @return View|null
     */
    protected function getView(): ?View
    {
        return $this->view;
    }
}
