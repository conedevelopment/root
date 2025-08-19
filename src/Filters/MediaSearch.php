<?php

declare(strict_types=1);

namespace Cone\Root\Filters;

use Cone\Root\Fields\Fields;

class MediaSearch extends Search
{
    /**
     * The searchable attributes
     */
    protected array $attributes = [
        'file_name',
    ];

    /**
     * Create a new filter instance.
     */
    public function __construct(array $attributes = ['file_name'])
    {
        $this->attributes = $attributes;

        parent::__construct(new Fields);
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableAttributes(): array
    {
        return array_fill_keys($this->attributes, null);
    }
}
