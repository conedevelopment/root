<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\NotificationFactory;
use Cone\Root\Interfaces\Models\Notification as Contract;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;

class Notification extends DatabaseNotification implements Contract
{
    use Filterable;
    use HasFactory;
    use HasUuids;
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
        'title',
    ];

    /**
     * Get the proxied interface.
     */
    public static function getProxiedInterface(): string
    {
        return Contract::class;
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return NotificationFactory::new();
    }

    /**
     * Create a new Eloquent query for the given Root request.
     */
    public static function rootQuery(Request $request): Builder
    {
        return $request->user()->notifications()->getQuery();
    }

    /**
     * Get the formatted type attribute.
     */
    protected function formattedType(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): ?string {
                return ! isset($attributes['type']) ? null : __(Str::headline(class_basename($attributes['type'])));
            }
        );
    }

    /**
     * Get the title attribute.
     */
    protected function title(): Attribute
    {
        return new Attribute(
            get: function (mixed $value, array $attributes): ?string {
                return $attributes['data']['title'] ?? $this->formattedType;
            }
        );
    }

    /**
     * Get the content attribute.
     */
    protected function content(): Attribute
    {
        return new Attribute(
            get: static function (mixed $value, array $attributes): ?string {
                return $attributes['data']['content'] ?? null;
            }
        );
    }

    /**
     * Get the formatted created at attribute.
     */
    protected function formattedCreatedAt(): Attribute
    {
        return new Attribute(
            get: function (): ?string {
                return is_null($this->created_at) ? null : $this->created_at->diffForHumans();
            }
        );
    }
}
