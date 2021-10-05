<?php

namespace GitBalocco\LaravelUiViewComposer\FormValue\Builder;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class EloquentFormValueBuilder
 * Eloquentモデルを使って更新対象のデータを初期値として取得するBuilder
 * 利便性のために暫定実装したが、Presentation層からInfrastructure層のクラスを直接使っているのであまり良くない。
 * 本来はApplication層のUseCase経由でDtoを取得するべきである。
 * @package GitBalocco\LaravelUiViewComposer\FormValue\Builder
 */
class EloquentFormValueBuilder implements FormValueBuilder
{
    /** @var mixed $identity */
    private $identity;

    /** @var Model $model */
    private $model;


    /**
     * EloquentFormValueBuilder constructor.
     * @param $identity
     * @param Model $model
     */
    public function __construct($identity, Model $model)
    {
        $this->identity = $identity;
        $this->model = $model;
    }

    /**
     * @return Collection
     * @throws ModelNotFoundException
     */
    public function build(): Collection
    {
        $record = $this->model->findOrFail($this->identity);
        return App::make(Collection::class, ['items' => $record->toArray()]);
    }
}
