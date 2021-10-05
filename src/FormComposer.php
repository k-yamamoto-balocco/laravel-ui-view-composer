<?php

namespace GitBalocco\LaravelUiViewComposer;

use GitBalocco\CommonStructures\StackableArray;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueApplier;
use GitBalocco\LaravelUiViewComposer\Contract\ViewComposerInterface;
use GitBalocco\LaravelUiViewComposer\Contract\ViewParameterCreator;
use GitBalocco\LaravelUiViewComposer\Exception\InvalidFormValuesApplierException;
use GitBalocco\LaravelUiViewComposer\FormValue\Applier\OnErrorApplier;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

/**
 * FormComposer
 * Form入力値を取り扱うViewComposerの基底クラス。
 * このクラスを継承し、 init() メソッドにApplierの登録処理を実装する。
 *
 * 更にform入力値以外の変数をアサインしたい場合、ViewParameterCreatorインターフェースを実装して
 * アサインする内容を返却する createParameter() メソッドを実装する。
 *
 * @package App\Http\View\Composers
 */
abstract class FormComposer implements ViewComposerInterface
{
    /** @var View|null $view */
    private $view;
    /** @var Request $request */
    private $request;
    /** @var string $formValueParameterName viewにアサインする際の変数名 */
    private $formValueParameterName = 'formValues';
    /** @var StackableArray $formValuesAppliers 適用するformValues生成ルール */
    private $formValuesAppliers;

    /**
     * FormComposer constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        //formValuesAppliersの初期化
        $formValuesAppliers = new StackableArray();
        $formValuesAppliers->addValue(new OnErrorApplier($request));
        $this->formValuesAppliers = $formValuesAppliers;
    }

    /**
     * compose
     *
     * @param View $view
     */
    final public function compose(View $view): void
    {
        //プロパティにセット
        $this->setView($view);
        //初期化(viewのセットより後で行う。initの中でviewを利用できる。)
        $this->init($this->getRequest(), $view);
        //parametersを作成
        $parameters = $this->parameters($view->getData());
        //formValuesを追加
        $parameters[$this->formValueParameterName] = $this->formValues();
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
     * init
     *
     * @param Request $request
     * @param View $view
     * @codeCoverageIgnore
     */
    abstract protected function init(Request $request, View $view): void;

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param array $assigned
     * @return array
     */
    private function parameters(array $assigned): array
    {
        if (!is_a($this, ViewParameterCreator::class)) {
            //ViewParameterCreatorを実装していない場合、viewに渡されたものをそのまま利用
            return $assigned;
        }
        //ViewParameterCreatorを実装している場合、マージする（コントローラーで割り当てられている変数優先）
        return array_merge($this->createParameter(), $assigned);
    }

    /**
     * formValuesの値を決定するロジック。
     * formValuesAppliersに入れた順番に条件判定shouldApply()を行い、はじめにtrueとなったApplierを採用。
     *
     * @return Collection
     */
    private function formValues(): Collection
    {
        /** @var FormValueApplier $applier */
        foreach ($this->formValuesAppliers as $applier) {
            //条件該当したらリターン
            if ($applier->shouldApply()) {
                return $applier->getBuilder()->build();
            }
        }
        //いずれの条件にも該当しない場合、空のcollectionを返す
        return App::make(Collection::class, ['items' => []]);
    }

    /**
     * setFormValueParameterName
     * viewにアサインされる際の変数名を変更したい場合に利用する。
     *
     * @param string $formValueParameterName
     * @return FormComposer
     */
    public function setFormValueParameterName(string $formValueParameterName): FormComposer
    {
        $this->formValueParameterName = $formValueParameterName;
        return $this;
    }

    /**
     * @return View|null
     */
    protected function getView(): ?View
    {
        return $this->view;
    }

    /**
     * addFormValuesApplier
     * Applierを追加する。
     *
     * @param FormValueApplier $applier
     * @return $this
     */
    protected function addFormValuesApplier(FormValueApplier $applier): FormComposer
    {
        $this->formValuesAppliers->addValue($applier);
        return $this;
    }

    /**
     * setFormValuesAppliers
     * 要素としてApplierを持つStackableArrayをセットする。
     * 初期状態でセット済のOnErrorApplierを利用したくない場合に利用。
     *
     * @param StackableArray $formValuesAppliers
     * @return FormComposer
     */
    protected function setFormValuesAppliers(StackableArray $formValuesAppliers): FormComposer
    {
        foreach ($formValuesAppliers as $applier) {
            if (!is_a($applier, FormValueApplier::class)) {
                throw new InvalidFormValuesApplierException();
            }
        }
        $this->formValuesAppliers = $formValuesAppliers;
        return $this;
    }
}
