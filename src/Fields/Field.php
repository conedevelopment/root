<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Field implements Arrayable
{
    use Authorizable;
    use HasAttributes;
    use Makeable;
    use ResolvesVisibility;

    /**
     * Indicates if the field is sortable.
     */
    protected bool|Closure $sortable = false;

    /**
     * Indicates if the field is searchable.
     */
    protected bool|Closure $searchable = false;

    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * The hydrate resolver callback.
     */
    protected ?Closure $hydrateResolver = null;

    /**
     * The validation rules.
     */
    protected array $rules = [
        '*' => [],
        'create' => [],
        'update' => [],
    ];

    /**
     * The Vue component.
     */
    protected string $component = 'Input';

    /**
     * The help text for the field.
     */
    protected ?string $help = null;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $name = null)
    {
        $this->label($label);
        $this->name($name ??= Str::of($label)->lower()->snake()->value());
        $this->id($name);
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->name;
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the Vue component.
     */
    public function getComponent(): string
    {
        return $this->component;
    }

    /**
     * Set the label attribute.
     */
    public function label(string|Closure $value): static
    {
        return $this->setAttribute('label', $value);
    }

    /**
     * Set the name attribute.
     */
    public function name(string|Closure $value): static
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * Set the id attribute.
     */
    public function id(string|Closure $value): static
    {
        return $this->setAttribute('id', $value);
    }

    /**
     * Set the readonly attribute.
     */
    public function readonly(bool|Closure $value = true): static
    {
        return $this->setAttribute('readonly', $value);
    }

    /**
     * Set the disabled attribute.
     */
    public function disabled(bool|Closure $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Set the required attribute.
     */
    public function required(bool|Closure $value = true): static
    {
        return $this->setAttribute('required', $value);
    }

    /**
     * Set the type attribute.
     */
    public function type(string|Closure $value): static
    {
        return $this->setAttribute('type', $value);
    }

    /**
     * Set the placeholder attribute.
     */
    public function placeholder(string|Closure $value): static
    {
        return $this->setAttribute('placeholder', $value);
    }

    /**
     * Set the sortable attribute.
     */
    public function sortable(bool|Closure $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Determine if the field is sortable.
     */
    public function isSortable(Request $request): bool
    {
        if ($this->sortable instanceof Closure) {
            return call_user_func_array($this->sortable, [$request]);
        }

        return $this->sortable;
    }

    /**
     * Set the searachable attribute.
     */
    public function searchable(bool|Closure $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Determine if the field is searchable.
     */
    public function isSearchable(Request $request): bool
    {
        if ($this->searchable instanceof Closure) {
            return call_user_func_array($this->searchable, [$request]);
        }

        return $this->searchable;
    }

    /**
     * Set the help attribute.
     */
    public function help(?string $value = null): static
    {
        $this->help = $value;

        return $this;
    }

    /**
     * Set the value resolver.
     */
    public function value(Closure $callback): static
    {
        $this->valueResolver = $callback;

        return $this;
    }

    /**
     * Resolve the value.
     */
    public function resolveValue(Request $request, Model $model): mixed
    {
        $value = $this->getValue($request, $model);

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$request, $model, $value]);
    }

    /**
     * Get the default value from the model.
     */
    public function getValue(Request $request, Model $model): mixed
    {
        return $model->getAttribute($this->getKey());
    }

    /**
     * Set the format resolver.
     */
    public function format(Closure $callback): static
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Format the value.
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        $value = $this->resolveValue($request, $model);

        if (is_null($this->formatResolver)) {
            return $value;
        }

        return call_user_func_array($this->formatResolver, [$request, $model, $value]);
    }

    /**
     * Persist the request value on the model.
     */
    public function persist(Request $request, Model $model): void
    {
        $model->saving(function (Model $model) use ($request): void {
            $this->resolveHydrate(
                $request, $model, $this->getValueForHydrate($request, $model)
            );
        });
    }

    /**
     * Get the value for hydrating the model.
     */
    public function getValueForHydrate(Request $request, Model $model): mixed
    {
        return $request->input([$this->getKey()]);
    }

    /**
     * Set the hydrate resolver.
     */
    public function hydrate(Closure $callback): static
    {
        $this->hydrateResolver = $callback;

        return $this;
    }

    /**
     * Hydrate the model.
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function () use ($model, $value): void {
                $model->setAttribute($this->getKey(), $value);
            };
        }

        call_user_func_array($this->hydrateResolver, [$request, $model, $value]);
    }

    /**
     * Set the validation rules.
     */
    public function rules(array|Closure $rules, string $context = '*'): static
    {
        $this->rules[$context] = $rules;

        return $this;
    }

    /**
     * Set the create validation rules.
     */
    public function createRules(array|Closure $rules): static
    {
        return $this->rules($rules, 'create');
    }

    /**
     * Set the update validation rules.
     */
    public function updateRules(array|Closure $rules): static
    {
        return $this->rules($rules, 'update');
    }

    /**
     * Resolve the attributes.
     */
    public function resolveAttributes(Request $request, Model $model): array
    {
        return array_reduce(
            array_keys($this->attributes),
            function (array $attributes, string $key) use ($request, $model): mixed {
                return array_merge($attributes, [$key => $this->resolveAttribute($request, $model, $key)]);
            },
            []
        );
    }

    /**
     * Resolve the given attribute.
     */
    public function resolveAttribute(Request $request, Model $model, string $key): mixed
    {
        $value = $this->getAttribute($key);

        return $value instanceof Closure
                ? call_user_func_array($value, [$request, $model])
                : $value;
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return $this->getAttributes();
    }

    /**
     * Get the display representation of the field.
     */
    public function toDisplay(Request $request, Model $model): array
    {
        return array_merge($this->resolveAttributes($request, $model), [
            'formattedValue' => $this->resolveFormat($request, $model),
            'searchable' => $this->isSearchable($request),
            'sortable' => $this->isSortable($request),
            'value' => $this->resolveValue($request, $model),
        ]);
    }

    /**
     * Get the input representation of the field.
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge($this->resolveAttributes($request, $model), [
            'component' => $this->getComponent(),
            'formattedValue' => $this->resolveFormat($request, $model),
            'help' => $this->help,
            'value' => $this->resolveValue($request, $model),
        ]);
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(Request $request, Model $model): array
    {
        $key = $model->exists ? 'update' : 'create';

        $rules = array_map(
            static function (array|Closure $rule) use ($request, $model): array {
                return is_array($rule) ? $rule : call_user_func_array($rule, [$request, $model]);
            },
            Arr::only($this->rules, array_unique(['*', $key]))
        );

        return [$this->getKey() => Arr::flatten($rules, 1)];
    }

    /**
     * Set the given attribute.
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Get the given attribute.
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Determine if the given attribute exists.
     */
    public function __isset(string $key): bool
    {
        return $this->hasAttribute($key);
    }

    /**
     * Remove the given attribute.
     */
    public function __unset(string $key): void
    {
        $this->removeAttribute($key);
    }
}
