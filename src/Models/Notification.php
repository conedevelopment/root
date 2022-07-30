<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\NotificationFactory;
use Cone\Root\Interfaces\Models\Notification as Contract;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\HasUuid;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification implements Contract
{
    use Filterable;
    use HasFactory;
    use HasUuid;
    use InteractsWithProxy;

    /**
     * Get the proxied interface.
     *
     * @return string
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory(): Factory
    {
        return NotificationFactory::new();
    }
}
