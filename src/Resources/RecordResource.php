<?php

namespace Cone\Root\Resources;

class RecordResource extends Resource
{
    /**
     * The icon for the resource.
     */
    protected string $icon = 'event-note';

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'user',
        'target',
    ];
}
