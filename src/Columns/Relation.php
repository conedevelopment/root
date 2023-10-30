<?php

namespace Cone\Root\Columns;

class Relation extends Column
{
    /**
     * Get the searchable relation attributes.
     */
    protected array $searchableRelationAttributes = ['id'];

    /**
     * Get the sortable relation attribute.
     */
    protected string $sortableRelationAttribute = 'id';

    /**
     * Set the searchable relation attributes.
     */
    public function searchIn(string|array $attributes): static
    {
        $this->searchableRelationAttributes = (array) $attributes;

        return $this;
    }

    /**
     * Set the sortable relation attribute.
     */
    public function sortBy(string $attribute): static
    {
        $this->sortableRelationAttribute = $attribute;

        return $this;
    }

    /**
     * Get the serachable relation attributes.
     */
    public function getSearchableRelationAttributes(): array
    {
        return $this->searchableRelationAttributes;
    }

    /**
     * Get the sortable relation attribute.
     */
    public function getSortableRelationAttribute(): string
    {
        return $this->sortableRelationAttribute;
    }
}
