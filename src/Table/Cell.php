<?php

namespace Cone\Root\Table;

use Cone\Root\Interfaces\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;

class Cell implements Renderable
{
    /**
     * The blade template.
     */
    protected string $template = 'root::table.cell';

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
    public function __construct(Model $model, Column $column)
    {
        $this->model = $model;
        $this->column = $column;
    }

    /**
     * Get the blade template.
     */
    public function template(): string
    {
        return $this->template;
    }

    /**
     * The view data.
     */
    public function data(Request $request): array
    {
        return [
            'formattedValue' => $this->column->resolveFormat($this->model),
        ];
    }

    /**
     * Render the cell.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template(),
            App::call([$this, 'data'])
        );
    }
}
