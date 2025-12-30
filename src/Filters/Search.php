<?php

declare(strict_types=1);

namespace Cone\Root\Filters;

use Cone\Root\Fields\Field;
use Cone\Root\Fields\Fields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Search extends RenderableFilter
{
    /**
     * The searchable fields.
     */
    protected Fields $fields;

    /**
     * Create a new filter instance.
     */
    public function __construct(Fields $fields)
    {
        $this->fields = $fields;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        if (empty($value)) {
            return $query;
        }

        return $query->where(function (Builder $query) use ($request, $value): void {
            $this->fields->each(function (Field $field) use ($request, $query, $value): void {
                $field->resolveSearchQuery($request, $query, $value);
            });
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toField(): SearchField
    {
        return SearchField::make($this->getName(), $this->getRequestKey())
            ->value(fn (Request $request): ?string => $this->getValue($request))
            ->setAttributes([
                'x-bind:readonly' => 'processing',
                'x-on:change.prevent.stop' => '',
                'x-on:input' => '($event) => $event.target?.form?.dispatchEvent(new Event(\'change\'))',
            ])
            ->placeholder(sprintf('%s...', $this->getName()));
    }
}
