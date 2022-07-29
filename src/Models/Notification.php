<?php

namespace Cone\Root\Models;

use Cone\Root\Interfaces\Models\Notification as Contract;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification implements Contract
{
    use Filterable;
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
}
