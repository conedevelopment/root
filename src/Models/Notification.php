<?php

namespace Cone\Root\Models;

use Cone\Root\Interfaces\Models\Notification as Contract;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification implements Contract
{
    use HasFactory;
    use HasUuids;
    use InteractsWithProxy;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'root_notifications';

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }
}
