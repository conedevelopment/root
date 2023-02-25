<?php

namespace Cone\Root\Resources;

use Cone\Root\Fields\BelongsTo;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;

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

    /**
     * {@inheritdoc}
     */
    public function fields(RootRequest $request): array
    {
        return array_merge(parent::fields($request), [
            Text::make(__('Event'), 'event'),
            BelongsTo::make(__('User'), 'user')->display('name'),
            Text::make(__('Target Type'), 'target_type'),
            Text::make(__('Target ID'), 'target_id'),
            Date::make(__('Created At'), 'created_at')->withTime(),
        ]);
    }
}
