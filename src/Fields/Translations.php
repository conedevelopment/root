<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Http\Request;

class Translations extends MorphMany
{
    /**
     * Indicates whether the relation is a sub resource.
     */
    protected bool $asSubResource = true;

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'values',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = 'rootEvents', Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Translations'), $modelAttribute, $relation);

        $this->hiddenOn(['index']);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Select::make(__('Language'), 'language'),
        ];
    }
}
