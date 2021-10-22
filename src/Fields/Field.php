<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Resources\Resource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Field implements Arrayable
{
    /**
     * Indicates if the field is sortable.
     *
     * @var bool
     */
    protected bool $sortable = false;

    /**
     * Indicates if the field is searchable.
     *
     * @var bool
     */
    protected bool $searchable = false;

    /**
     * The field attributes.
     *
     * @var array
     */
    protected array $attributes = [];

    /**
     * The format resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $formatResolver = null;

    /**
     * The default value resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $defaultResolver = null;

    /**
     * The validation rules.
     *
     * @var array
     */
    protected array $rules = [
        '*' => [],
        Resource::CREATE => [],
        Resource::UPDATE => [],
    ];

    /**
     * The rules resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $rulesResolver = null;

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'FormInput';

    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        $this->attributes['label'] = $label;
        $this->attributes['name'] = $name ??= Str::snake(strtolower($label));
        $this->attributes['id'] = $name;
    }

    /**
     * Make a new field instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): self
    {
        return new static(...$parameters);
    }

    /**
     * Get the Vue component.
     *
     * @return string
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Get the attributes.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the given attributes.
     *
     * @param  array  $attributes
     * @return $this
     */
    public function setAttributes(array $attributes): self
    {
        $this->attributes = array_replace($this->attributes, $attributes);

        return $this;
    }

    /**
     * Determine if the given attributes exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasAttribute(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Get the given attribute.
     *
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return $this->hasAttribute($key) ? $this->attributes[$key] : $default;
    }

    /**
     * Set the given attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute(string $key, mixed $value): self
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Remove the given attribute.
     *
     * @param  string  $key
     * @return $this
     */
    public function removeAttribute(string $key): self
    {
        unset($this->attributes[$key]);

        return $this;
    }

    /**
     * Remove the given attributes.
     *
     * @param  array  $keys
     * @return $this
     */
    public function removeAttributes(array $keys): self
    {
        foreach ($keys as $key) {
            $this->removeAttribute($key);
        }

        return $this;
    }

    /**
     * Clear all the attributes.
     *
     * @return $this
     */
    public function clearAttributes(): self
    {
        $this->attributes = [];

        return $this;
    }

    /**
     * Set the label attribute.
     *
     * @param  string  $value
     * @return $this
     */
    public function label(string $value): self
    {
        return $this->setAttribute('label', $value);
    }

    /**
     * Set the name attribute.
     *
     * @param  string  $value
     * @return $this
     */
    public function name(string $value): self
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * Set the id attribute.
     *
     * @param  string  $value
     * @return $this
     */
    public function id(string $value): self
    {
        return $this->setAttribute('id', $value);
    }

    /**
     * Set the readonly attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function readonly(bool $value = true): self
    {
        return $this->setAttribute('readonly', $value);
    }

    /**
     * Set the disabled attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function disabled(bool $value = true): self
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Set the required attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function required(bool $value = true): self
    {
        return $this->setAttribute('required', $value);
    }

    /**
     * Set the sortable attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function sortable(bool $value = true): self
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Set the searachable attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function searchable(bool $value = true): self
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Set the default resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function default(Closure $callback): self
    {
        $this->defaultResolver = $callback;

        return $this;
    }

    /**
     * Resolve the default value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveDefault(Request $request, Model $model): mixed
    {
        $value = $model->getAttribute($this->name);

        if (is_null($this->defaultResolver)) {
            return $value;
        }

        return call_user_func_array($this->defaultResolver, [$request, $model, $value]);
    }

    /**
     * Set the format resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function format(Closure $callback): self
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Format the value.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        $value = $this->resolveDefault($request, $model);

        if (is_null($this->formatResolver)) {
            return $value;
        }

        return call_user_func_array($this->formatResolver, [$request, $model, $value]);
    }

    /**
     * Hydrate the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return void
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $model->saving(function (Model $model) use ($value): void {
            $model->setAttribute($this->name, $value);
        });
    }

    /**
     * Set the validation rules.
     *
     * @param  array|Closure  $rule
     * @return $this
     */
    public function rules(array|Closure $rules): self
    {
        if ($rules instanceof Closure) {
            $this->rulesResolver = $rules;
        } elseif (Arr::isAssoc($rules)) {
            $this->rules = array_merge($this->rules, $rules);
        } else {
            $this->rules['*'] = $rules;
        }

        return $this;
    }

    /**
     * Set the given attribute.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Get the given attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return $this->hasAttribute($key);
    }

    /**
     * Remove the given attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset(string $key): void
    {
        $this->removeAttribute($key);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Get the display representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toDisplay(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'formatted_value' => $this->resolveFormat($request, $model),
            'searchable' => $this->searchable,
            'sortable' => $this->sortable,
            'value' => $this->resolveDefault($request, $model),
        ]);
    }

    /**
     * Get the input representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'component' => $this->getComponent(),
            'formatted_value' => $this->resolveFormat($request, $model),
            'value' => $this->resolveDefault($request, $model),
        ]);
    }

    /**
     * Get the validation representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $action
     * @return array
     */
    public function toValidate(Request $request, Model $model, string $action = '*'): array
    {
        $rules = $this->rules;

        $resolved = is_null($this->rulesResolver)
            ? []
            : call_user_func_array($this->rulesResolver, [$request, $model]);

        if (! Arr::isAssoc($resolved)) {
            $resolved = ['*' => $resolved];
        }

        $rules = array_merge_recursive($rules, $resolved);

        return array_unique(
            $action === '*' ? $rules['*'] : array_merge($rules['*'], $rules[$action] ?? [])
        );
    }
}
