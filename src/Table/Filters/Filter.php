<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Form\Fields\Field;
use Cone\Root\Table\Table;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Stringable;

abstract class Filter implements Stringable
{
    use Authorizable;
    use Makeable;

    /**
     * The Blade template.
     */
    protected ?string $template = 'root::table.filters.select';

    /**
     * The table instance.
     */
    protected Table $table;

    /**
     * Create a new filter instance.
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Apply the filter on the query.
     */
    abstract public function apply(Request $request, Builder $query, mixed $value): Builder;

    /**
     * Convert the filter to a form field.
     */
    abstract public function toField(FilterForm $form): Field;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->value();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * The default value of the filter.
     */
    public function default(Request $request): mixed
    {
        return $request->query($this->getKey());
    }

    /**
     * Determine if the filter is active.
     */
    public function active(Request $request): bool
    {
        return ! empty($request->query($this->getKey()));
    }

    /**
     * Determine if the filter is functional.
     */
    public function functional(): bool
    {
        return is_null($this->template);
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return [];
    }

    /**
     * Render the filter.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Convert the filter to a string.
     */
    public function __toString(): string
    {
        return $this->render()->render();
    }
}
