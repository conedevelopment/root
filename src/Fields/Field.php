<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Field implements Arrayable
{
    use Authorizable;
    use ResolvesVisibility;

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
        'create' => [],
        'update' => [],
    ];

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Input';

    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        $this->label($label);
        $this->name($name ??= (string) Str::of($label)->lower()->snake());
        $this->id($name);
    }

    /**
     * Make a new field instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->name;
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
    public function setAttributes(array $attributes): static
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
    public function setAttribute(string $key, mixed $value): static
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
    public function removeAttribute(string $key): static
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
    public function removeAttributes(array $keys): static
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
    public function clearAttributes(): static
    {
        $this->attributes = [];

        return $this;
    }

    /**
     * Set the label attribute.
     *
     * @param  string|\Closure  $value
     * @return $this
     */
    public function label(string|Closure $value): static
    {
        return $this->setAttribute('label', $value);
    }

    /**
     * Set the name attribute.
     *
     * @param  string|\Closure  $value
     * @return $this
     */
    public function name(string|Closure $value): static
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * Set the id attribute.
     *
     * @param  string|\Closure  $value
     * @return $this
     */
    public function id(string|Closure $value): static
    {
        return $this->setAttribute('id', $value);
    }

    /**
     * Set the readonly attribute.
     *
     * @param  bool|\Closure  $value
     * @return $this
     */
    public function readonly(bool|Closure $value = true): static
    {
        return $this->setAttribute('readonly', $value);
    }

    /**
     * Set the disabled attribute.
     *
     * @param  bool|\Closure  $value
     * @return $this
     */
    public function disabled(bool|Closure $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Set the required attribute.
     *
     * @param  bool|\Closure  $value
     * @return $this
     */
    public function required(bool|Closure $value = true): static
    {
        return $this->setAttribute('required', $value);
    }

    /**
     * Set the type attribute.
     *
     * @param  string|\Closure  $value
     * @return $this
     */
    public function type(string|Closure $value): static
    {
        return $this->setAttribute('type', $value);
    }

    /**
     * Set the placeholder attribute.
     *
     * @param  string|\Closure  $value
     * @return $this
     */
    public function placeholder(string|Closure $value): static
    {
        return $this->setAttribute('placeholder', $value);
    }

    /**
     * Set the sortable attribute.
     *
     * @param  bool|\Closure  $value
     * @return $this
     */
    public function sortable(bool|Closure $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Determine if the field is sortable.
     *
     * @return bool
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Set the searachable attribute.
     *
     * @param  bool|\Closure  $value
     * @return $this
     */
    public function searchable(bool|Closure $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Determine if the field is searchable.
     *
     * @return bool
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Set the default resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function default(Closure $callback): static
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
        $value = $this->getDefaultValue($request, $model);

        if (is_null($this->defaultResolver)) {
            return $value;
        }

        return call_user_func_array($this->defaultResolver, [$request, $model, $value]);
    }

    /**
     * Get the default value from the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function getDefaultValue(Request $request, Model $model): mixed
    {
        return $model->getAttribute($this->name);
    }

    /**
     * Set the format resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function format(Closure $callback): static
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
     * Persist the request value on the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function persist(Request $request, Model $model): void
    {
        $this->hydrate(
            $request, $model, $this->getValueForHydrate($request, $model)
        );
    }

    /**
     * Get the value for hydrating the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function getValueForHydrate(Request $request, Model $model): mixed
    {
        return $request->input($this->name);
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
     * @param  array|\Closure  $rules
     * @param  string  $context
     * @return $this
     */
    public function rules(array|Closure $rules, string $context = '*'): static
    {
        $this->rules[$context] = $rules;

        return $this;
    }

    /**
     * Set the create validation rules.
     *
     * @param  array|\Closure  $rules
     * @return $this
     */
    public function createRules(array|Closure $rules): static
    {
        return $this->rules($rules, 'create');
    }

    /**
     * Set the update validation rules.
     *
     * @param  array|\Closure  $rules
     * @return $this
     */
    public function updateRules(array|Closure $rules): static
    {
        return $this->rules($rules, 'update');
    }

    /**
     * Resolve the attributes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function resolveAttributes(Request $request, Model $model)
    {
        return array_map(static function (mixed $attribute) use ($request, $model): mixed {
            return $attribute instanceof Closure
                ? call_user_func_array($attribute, [$request, $model])
                : $attribute;
        }, $this->attributes);
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
        return array_merge($this->resolveAttributes($request, $model), [
            'formatted_value' => $this->resolveFormat($request, $model),
            'searchable' => $this->isSearchable(),
            'sortable' => $this->isSortable(),
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
        return array_merge($this->resolveAttributes($request, $model), [
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
     * @return array
     */
    public function toValidate(Request $request, Model $model): array
    {
        $key = match (get_class($request)) {
            CreateRequest::class => 'create',
            UpdateRequest::class => 'update',
            default => '*',
        };

        $rules = array_map(
            static function (array|Closure $rule) use ($request, $model): array {
                return is_array($rule) ? $rule : call_user_func_array($rule, [$request, $model]);
            },
            Arr::only($this->rules, array_unique(['*', $key]))
        );

        return [$this->name => array_unique(Arr::flatten($rules, 1))];
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
}
