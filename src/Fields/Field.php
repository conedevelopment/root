<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\RenderableFilter;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\ViewErrorBag;
use JsonSerializable;

abstract class Field implements Arrayable, JsonSerializable
{
    use Authorizable;
    use Conditionable;
    use HasAttributes;
    use Makeable;
    use ResolvesVisibility;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.input';

    /**
     * The hydrate resolver callback.
     */
    protected ?Closure $hydrateResolver = null;

    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The default value resolver callback.
     */
    protected ?Closure $defaultValueResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * The errors resolver callback.
     */
    protected ?Closure $errorsResolver = null;

    /**
     * The validation rules.
     */
    protected array $rules = [
        '*' => [],
        'create' => [],
        'update' => [],
    ];

    /**
     * The field label.
     */
    protected string $label;

    /**
     * The associated model attribute.
     */
    protected string $modelAttribute;

    /**
     * The field help text.
     */
    protected ?string $help = null;

    /**
     * The field prefix.
     */
    protected ?string $prefix = null;

    /**
     * The field suffix.
     */
    protected ?string $suffix = null;

    /**
     * The field model.
     */
    protected ?Model $model = null;

    /**
     * Indicates if the field should use the old value.
     */
    protected bool $withOldValue = true;

    /**
     * Indicates if the field has been hydrated.
     */
    protected bool $hydrated = false;

    /**
     * Indicates if the field is sortable.
     */
    protected bool|Closure $sortable = false;

    /**
     * Indicates if the field is searchable.
     */
    protected bool|Closure $searchable = false;

    /**
     * The filter query resolver callback.
     */
    protected ?Closure $searchQueryResolver = null;

    /**
     * Indicates if the field is filterable.
     */
    protected bool|Closure $filterable = false;

    /**
     * The filter query resolver callback.
     */
    protected ?Closure $filterQueryResolver = null;

    /**
     * Determine if the field is computed.
     */
    protected bool $computed = false;

    /**
     * Indicates whether the field is translatable.
     */
    protected bool|Closure $translatable = false;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        $this->computed = $modelAttribute instanceof Closure;

        $this->modelAttribute = $this->computed ? Str::random() : ($modelAttribute ?: Str::of($label)->lower()->snake()->value());

        $this->label($label);
        $this->name($this->modelAttribute);
        $this->id($this->modelAttribute);
        $this->class('form-control');
        $this->value($this->computed ? $modelAttribute : null);
    }

    /**
     * Get the model attribute.
     */
    public function getModelAttribute(): string
    {
        return $this->modelAttribute;
    }

    /**
     * Set the model attribute.
     */
    public function setModelAttribute(string $value): static
    {
        $this->modelAttribute = $value;

        return $this;
    }

    /**
     * Get the request key.
     */
    public function getRequestKey(): string
    {
        return str_replace('->', '.', $this->getModelAttribute());
    }

    /**
     * Get the validation key.
     */
    public function getValidationKey(): string
    {
        return $this->getRequestKey();
    }

    /**
     * Get the blade template.
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Set the label attribute.
     */
    public function label(string $value): static
    {
        $this->label = $value;

        return $this;
    }

    /**
     * Get the field label.
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the "name" HTML attribute attribute.
     */
    public function name(string|Closure $value): static
    {
        $value = $value instanceof Closure ? call_user_func_array($value, [$this]) : $value;

        $value = preg_replace('/(?:\->|\.)(.+?)(?=(?:\->|\.)|$)/', '[$1]', $value);

        return $this->setAttribute('name', $value);
    }

    /**
     * Set the readonly attribute.
     */
    public function readonly(bool|Closure $value = true): static
    {
        return $this->setAttribute('readonly', $value);
    }

    /**
     * Set the "disabled" HTML attribute.
     */
    public function disabled(bool|Closure $value = true): static
    {
        return $this->setAttribute('disabled', $value);
    }

    /**
     * Set the "required" HTML attribute.
     */
    public function required(bool|Closure $value = true): static
    {
        return $this->setAttribute('required', $value);
    }

    /**
     * Set the "type" HTML attribute.
     */
    public function type(string|Closure $value): static
    {
        return $this->setAttribute('type', $value);
    }

    /**
     * Set the "placeholder" HTML attribute.
     */
    public function placeholder(string|Closure $value): static
    {
        return $this->setAttribute('placeholder', $value);
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
     * Set the prefix attribute.
     */
    public function prefix(string $value): static
    {
        $this->prefix = $value;

        return $this;
    }

    /**
     * Set the suffix attribute.
     */
    public function suffix(string $value): static
    {
        $this->suffix = $value;

        return $this;
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
    public function isSortable(): bool
    {
        if ($this->computed) {
            return false;
        }

        return $this->sortable instanceof Closure ? call_user_func($this->sortable) : $this->sortable;
    }

    /**
     * Set the searchable attribute.
     */
    public function searchable(bool|Closure $value = true, ?Closure $callback = null): static
    {
        $this->searchable = $value;

        $this->searchQueryResolver = $callback ?: function (Request $request, Builder $query, mixed $value, string $attribute): Builder {
            return $query->where($query->qualifyColumn($attribute), 'like', "%{$value}%", 'or');
        };

        return $this;
    }

    /**
     * Determine if the field is searchable.
     */
    public function isSearchable(): bool
    {
        if ($this->computed) {
            return false;
        }

        return $this->searchable instanceof Closure ? call_user_func($this->searchable) : $this->searchable;
    }

    /**
     * Resolve the search query.
     */
    public function resolveSearchQuery(Request $request, Builder $query, mixed $value): Builder
    {
        return $this->isSearchable()
            ? call_user_func_array($this->searchQueryResolver, [$request, $query, $value, $this->getModelAttribute()])
            : $query;
    }

    /**
     * Set the translatable attribute.
     */
    public function translatable(bool|Closure $value = true): static
    {
        $this->translatable = $value;

        return $this;
    }

    /**
     * Determine if the field is translatable.
     */
    public function isTranslatable(): bool
    {
        if ($this->computed) {
            return false;
        }

        return $this->translatable instanceof Closure ? call_user_func($this->translatable) : $this->translatable;
    }

    /**
     * Set the filterable attribute.
     */
    public function filterable(bool|Closure $value = true, ?Closure $callback = null): static
    {
        $this->filterable = $value;

        $this->filterQueryResolver = $callback ?: function (Request $request, Builder $query, mixed $value, string $attribute): Builder {
            return $query->where($query->qualifyColumn($attribute), $value);
        };

        return $this;
    }

    /**
     * Determine whether the field is filterable.
     */
    public function isFilterable(): bool
    {
        if ($this->computed) {
            return false;
        }

        return $this->filterable instanceof Closure ? call_user_func($this->filterable) : $this->filterable;
    }

    /**
     * Resolve the filter query.
     */
    public function resolveFilterQuery(Request $request, Builder $query, mixed $value): Builder
    {
        return $this->isFilterable()
            ? call_user_func_array($this->filterQueryResolver, [$request, $query, $value, $this->getModelAttribute()])
            : $query;
    }

    /**
     * Set the default value resolver.
     */
    public function default(mixed $value): static
    {
        if (! $value instanceof Closure) {
            $value = fn (): mixed => $value;
        }

        $this->defaultValueResolver = $value;

        return $this;
    }

    /**
     * Set the value resolver.
     */
    public function value(?Closure $callback = null): static
    {
        $this->valueResolver = $callback;

        return $this;
    }

    /**
     * Resolve the value.
     */
    public function resolveValue(Request $request, Model $model): mixed
    {
        if (! $this->hydrated && $this->withOldValue && $request->session()->hasOldInput($this->getRequestKey())) {
            $this->resolveHydrate($request, $model, $this->getOldValue($request));
        }

        $value = $this->getValue($model);

        if (is_null($value) && ! is_null($this->defaultValueResolver)) {
            $value = call_user_func_array($this->defaultValueResolver, [$request, $model]);
        }

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$request, $model, $value]);
    }

    /**
     * Get the old value from the request.
     */
    public function getOldValue(Request $request): mixed
    {
        return $request->old($this->getRequestKey());
    }

    /**
     * Set the with old value attribute.
     */
    public function withOldValue(bool $value = true): static
    {
        $this->withOldValue = $value;

        return $this;
    }

    /**
     * Set the with old value attribute to false.
     */
    public function withoutOldValue(): static
    {
        return $this->withOldValue(false);
    }

    /**
     * Get the default value from the model.
     */
    public function getValue(Model $model): mixed
    {
        $attribute = $this->getModelAttribute();

        return match (true) {
            str_contains($attribute, '->') => data_get($model, str_replace('->', '.', $attribute)),
            default => $model->getAttribute($this->getModelAttribute()),
        };
    }

    /**
     * Set the format resolver.
     */
    public function format(?Closure $callback = null): static
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Format the value.
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        $value = $this->resolveValue($request, $model);

        if (is_null($this->formatResolver)) {
            return is_array($value) ? json_encode($value) : (string) $value;
        }

        return call_user_func_array($this->formatResolver, [$request, $model, $value]);
    }

    /**
     * Persist the request value on the model.
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $this->resolveHydrate($request, $model, $value);
    }

    /**
     * Get the value for hydrating the model.
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return $request->input($this->getRequestKey());
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
        if ($this->computed) {
            return;
        }

        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, $value): void {
                $model->setAttribute($this->getModelAttribute(), $value);
            };
        }

        call_user_func_array($this->hydrateResolver, [$request, $model, $value]);

        $this->hydrated = true;
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
     * Get the validation rules.
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Set the error resolver callback.
     */
    public function resolveErrorsUsing(Closure $callback): static
    {
        $this->errorsResolver = $callback;

        return $this;
    }

    /**
     * Resolve the validation errors.
     */
    public function resolveErrors(Request $request): MessageBag
    {
        return is_null($this->errorsResolver)
            ? $request->session()->get('errors', new ViewErrorBag)->getBag('default')
            : call_user_func_array($this->errorsResolver, [$request]);
    }

    /**
     * Determine if the field is invalid.
     */
    public function invalid(Request $request): bool
    {
        return $this->resolveErrors($request)->has($this->getValidationKey());
    }

    /**
     * Get the validation error from the request.
     */
    public function error(Request $request): ?string
    {
        return $this->resolveErrors($request)->first($this->getValidationKey()) ?: null;
    }

    /**
     * Convert the element to a JSON serializable format.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Convert the field to an array.
     */
    public function toArray(): array
    {
        return [
            'attribute' => $this->getModelAttribute(),
            'help' => $this->help,
            'label' => $this->label,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'template' => $this->template,
            'searchable' => $this->isSearchable(),
            'sortable' => $this->isSortable(),
        ];
    }

    /**
     * Get the form component data.
     */
    public function toDisplay(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'value' => $this->resolveValue($request, $model),
            'formattedValue' => $this->resolveFormat($request, $model),
        ]);
    }

    /**
     * Get the form component data.
     */
    public function toInput(Request $request, Model $model): array
    {
        if ($this->computed) {
            return [];
        }

        return array_merge($this->toDisplay($request, $model), [
            'attrs' => $this->newAttributeBag()->class([
                'form-control--invalid' => $this->invalid($request),
            ]),
            'error' => $this->error($request),
            'invalid' => $this->invalid($request),
        ]);
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(Request $request, Model $model): array
    {
        $key = $model->exists ? 'update' : 'create';

        $rules = array_map(
            static fn (array|Closure $rule): array => is_array($rule) ? $rule : call_user_func_array($rule, [$request, $model]),
            Arr::only($this->rules, array_unique(['*', $key]))
        );

        return [$this->getValidationKey() => Arr::flatten($rules, 1)];
    }

    /**
     * Get the filter representation of the field.
     */
    public function toFilter(): Filter
    {
        return new class($this) extends RenderableFilter
        {
            protected Field $field;

            public function __construct(Field $field)
            {
                parent::__construct($field->getModelAttribute());

                $this->field = $field;
            }

            public function apply(Request $request, Builder $query, mixed $value): Builder
            {
                return $this->field->resolveFilterQuery($request, $query, $value);
            }

            public function toField(): Field
            {
                return Text::make($this->field->getLabel(), $this->getRequestKey())
                    ->value(fn (Request $request): mixed => $this->getValue($request));
            }
        };
    }

    /**
     * Clone the field.
     */
    public function __clone(): void
    {
        //
    }
}
