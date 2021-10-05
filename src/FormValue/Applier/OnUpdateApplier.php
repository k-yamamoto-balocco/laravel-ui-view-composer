<?php

namespace GitBalocco\LaravelUiViewComposer\FormValue\Applier;

use GitBalocco\LaravelUiUtils\Http\Contract\IdentityHandler;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueApplier;
use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;

/**
 * Class OnUpdateApplier
 * 更新処理時初期化用のApplier
 * IdentityHandlerが更新対象データのIDを検出した場合に適用される。
 * @package GitBalocco\LaravelUiViewComposer\FormValue\Applier
 */
class OnUpdateApplier implements FormValueApplier
{
    /** @var IdentityHandler $identityHandler */
    private $identityHandler;
    /** @var FormValueBuilder $builder */
    private $builder;

    /**
     * OnUpdate constructor.
     * @param FormValueBuilder $builder
     * @param IdentityHandler $identityHandler
     */
    public function __construct(FormValueBuilder $builder, IdentityHandler $identityHandler)
    {
        $this->builder = $builder;
        $this->identityHandler = $identityHandler;
    }

    /**
     * shouldApply
     *
     * @return bool
     */
    public function shouldApply(): bool
    {
        return (bool)$this->identityHandler->retrieveIdentity();
    }

    /**
     * @return FormValueBuilder
     */
    public function getBuilder(): FormValueBuilder
    {
        return $this->builder;
    }
}
