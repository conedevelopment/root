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
use Illuminate\Support\Str;

class Notification extends DatabaseNotification implements Contract
{
    use Filterable;
    use HasFactory;
    use HasUuid;
    use InteractsWithProxy;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'content',
        'formatted_created_at',
        'formatted_type',
    ];

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

    /**
     * Get the formatted type attribute.
     *
     * @return string|null
     */
    public function getFormattedTypeAttribute(): ?string
    {
        if (is_null($this->type)) {
            return null;
        }

        return __(Str::headline(class_basename($this->type)));
    }

    /**
     * Get the content attribute.
     *
     * @return string|null
     */
    public function getContentAttribute(): ?string
    {
        return $this->message['content'] ?? null;
    }

    /**
     * Get the formatted created at attribute.
     *
     * @return string|null
     */
    public function getFormattedCreatedAtAttribute(): ?string
    {
        if (is_null($this->created_at)) {
            return null;
        }

        return $this->created_at->diffForHumans();
    }
}
