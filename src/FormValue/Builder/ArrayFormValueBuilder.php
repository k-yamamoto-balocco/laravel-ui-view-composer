<?php

namespace GitBalocco\LaravelUiViewComposer\FormValue\Builder;

use GitBalocco\LaravelUiViewComposer\Contract\FormValueBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

/**
 * Class ArrayFormValueBuilder
 * 単純な配列を返却するBuilder
 * @package GitBalocco\LaravelUiViewComposer\FormValue\Builder
 */
class ArrayFormValueBuilder implements FormValueBuilder
{
    /** @var array $array */
    private $array;

    /**
     * ArrayFormValueBuilder constructor.
     * @param array $array
     */
    public function __construct(array $array)
    {
        $this->array = $array;
    }

    /**
     * build
     *
     * @return Collection
     */
    public function build(): Collection
    {
        return App::make(Collection::class, ['items' => $this->array]);
    }
}
