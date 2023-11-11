<?php

namespace Cone\Root\Filters;

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
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableAttributes(): array
    {
        return array_fill_keys($this->attributes, null);
    }
}
