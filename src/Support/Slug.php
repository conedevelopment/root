<?php

namespace Cone\Root\Support;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stringable;

class Slug implements Stringable
{
    /**
     * The model instance.
     */
    public readonly Model $model;

    /**
     * The attributes that the slug is created from.
     */
    public readonly array $from;

    /**
     * The attribute that the slug is saved into.
     */
    public readonly string $to;

    /**
     * The slug separator.
     */
    public readonly string $separator;

    /**
     * Indicates if the slug should be unique.
     */
    public readonly bool $unique;

    /**
     * Indicates if generate always a fresh slug.
     */
    public readonly bool $fresh;

    /**
     * The slug resolver.
     */
    protected ?Closure $resolver = null;

    /**
     * Create a new slug instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Set the "from" property.
     */
    public function from(array|string $attributes): static
    {
        $this->from = (array) $attributes;

        return $this;
    }

    /**
     * Set the "to" property
     */
    public function to(string $attribute): static
    {
        $this->to = $attribute;

        return $this;
    }

    /**
     * Set the "unique" property.
     */
    public function unique(bool $value = true): static
    {
        $this->unique = $value;

        return $this;
    }

    /**
     * Set the "fresh" property.
     */
    public function fresh(bool $value = true): static
    {
        $this->fresh = $value;

        return $this;
    }

    /**
     * Set the "resolver" property.
     */
    public function generateUsing(Closure $callback): static
    {
        $this->resolver = $callback;

        return $this;
    }

    /**
     * Generate the slug.
     */
    public function generate(): string
    {
        $this->from ??= ['id'];
        $this->to ??= 'slug';
        $this->separator ??= '-';
        $this->unique ??= false;
        $this->fresh ??= false;

        if (! is_null($this->model->getAttribute($this->to)) && ! $this->fresh) {
            return $this->model->getAttribute($this->to);
        }

        $value = Str::of(implode($this->separator, $this->model->only($this->from)))
            ->slug($this->separator)
            ->value();

        if (! is_null($this->resolver)) {
            return call_user_func_array($this->resolver, [$this, $value]);
        }

        if (! $this->unique) {
            return $value;
        }

        $match = $this->model
            ->newQuery()
            ->when(
                in_array(SoftDeletes::class, class_uses_recursive($this->model)),
                static function (Builder $query): Builder {
                    return $query->withTrashed();
                }
            )
            ->whereRaw(sprintf(
                "`%s` regexp '^%s(%s[\\\\d]+)?$'",
                $this->to,
                preg_quote($value),
                preg_quote($this->separator)
            ))
            ->orderByDesc($this->to)
            ->limit(1)
            ->value($this->to);

        $value = is_null($match) ? $value : preg_replace_callback(
            sprintf('/%s([\d]+)?$/', preg_quote($this->separator)),
            static function (array $match): string {
                return str_replace($match[1], (string)(((int) $match[1]) + 1), $match[0]);
            },
            $match
        );

        return $value === $match ? sprintf('%s%s1', $value, $this->separator) : $value;
    }

    /**
     * Get the string representation of the slug.
     */
    public function __toString(): string
    {
        return $this->generate();
    }
}
