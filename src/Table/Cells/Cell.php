<?php

namespace Cone\Root\Table\Cells;

use Cone\Root\Table\Columns\Column;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesModelValue;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

abstract class Cell implements Renderable
{
    use Makeable;
    use ResolvesModelValue;

    /**
     * The blade template.
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
     * Resolve the model.
     */
    public function resolveModel(): Model
    {
        return $this->model;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->column->getKey();
    }

    /**
     * The view data.
     */
    public function data(Request $request): array
    {
        return [
            'formattedValue' => $this->resolveFormat(),
        ];
    }

    /**
     * Render the cell.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }
}
