<?php

namespace Cone\Root\Table\Cells;

use Cone\Root\Support\Element;
use Cone\Root\Table\Columns\Column;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesModelValue;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\Conditionable;
use Stringable;

abstract class Cell extends Element
{
    use Conditionable;
    use Makeable;
    use ResolvesModelValue;

    /**
     * The Blade template.
     */
    protected string $template;

    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * The column instance.
     */
    protected Column $column;

    /**
     * Create a new cell instance.
     */
    public function __construct(Column $column, Model $model)
    {
        $this->column = $column;
        $this->model = $model;
    }

    /**
     * Get the model.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get the model attribute.
     */
    public function getModelAttribute(): string
    {
        return $this->column->getModelAttribute();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'formattedValue' => $this->resolveFormat($request),
                ];
            })
        );
    }
}
