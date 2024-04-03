<?php

namespace Cone\Root\Traits;

use Cone\Root\Models\Event;
use Cone\Root\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasRootEvents
{
    /**
     * Get the events for the model.
     */
    public function rootEvents(): MorphMany
    {
        return $this->morphMany(Event::getProxiedClass(), 'target');
    }

    /**
     * Record a new root event for the model.
     */
    public function recordRootEvent(string $action, ?User $user = null, ?array $payload = null): Event
    {
        $event = $this->rootEvents()->make([
            'action' => $action,
            'payload' => $payload,
        ]);

        $event->user()->associate($user)->save();

        return $event;
    }
}
