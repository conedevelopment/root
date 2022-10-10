<?php

namespace Cone\Root\Models;

use Cone\Root\Database\Factories\NotificationFactory;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Interfaces\Models\Notification as Contract;
use Cone\Root\Traits\Filterable;
use Cone\Root\Traits\HasUuid;
use Cone\Root\Traits\InteractsWithProxy;
use Illuminate\Database\Eloquent\Builder;
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
        'title',
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
     * Create a new Eloquent query for the given Root request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function rootQuery(RootRequest $request): Builder
    {
        return $request->user()->notifications()->getQuery();
    }

    /**
     * Get the formatted type attribute.
     *
     * @return string|null
     */
    public function getFormattedTypeAttribute(): ?string
    {
        return is_null($this->type) ? null : __(Str::headline(class_basename($this->type)));
    }

    /**
     * Get the title attribute.
     *
     * @return string|null
     */
    public function getTitleAttribute(): ?string
    {
        return $this->data['title'] ?? $this->formattedType;
    }

    /**
     * Get the content attribute.
     *
     * @return string|null
     */
    public function getContentAttribute(): ?string
    {
        return $this->data['content'] ?? null;
    }

    /**
     * Get the formatted created at attribute.
     *
     * @return string|null
     */
    public function getFormattedCreatedAtAttribute(): ?string
    {
        return is_null($this->created_at) ? null : $this->created_at->diffForHumans();
    }
}
