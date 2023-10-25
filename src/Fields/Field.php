<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesModelValue;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\ViewErrorBag;
use JsonSerializable;

abstract class Field implements Arrayable, JsonSerializable
{
    use Conditionable;
    use HasAttributes;
    use Makeable;
    use ResolvesModelValue;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.input';

    /**
     * The hydrate resolver callback.
     */
    protected ?Closure $hydrateResolver = null;

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
     * Create a new field instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        $this->modelAttribute = $modelAttribute ?: Str::of($label)->lower()->snake()->value();

        $this->label($label);
        $this->name($this->modelAttribute);
        $this->id($this->modelAttribute);
        $this->setAttribute('class', 'form-control');
    }

    /**
     * Get the template.
     */
    public function getTemplate(): string
    {
        return $this->template;
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
     * Set the label attribute.
     */
    public function label(string $value): static
    {
        $this->label = $value;

        return $this;
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
    public function help(string $value = null): static
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
     * Resolve the value.
     */
    public function resolveValue(Request $request, Model $model): mixed
    {
        if (! $this->hydrated && $this->withOldValue && $request->session()->hasOldInput($this->getRequestKey())) {
            $this->resolveHydrate($request, $model, $this->getOldValue($request));
        }

        $value = $this->getValue($model);

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
    public function withoutOldValue(): mixed
    {
        return $this->withOldValue(false);
    }

    /**
     * Persist the request value on the model.
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $model->saving(function (Model $model) use ($request, $value): void {
            $this->resolveHydrate($request, $model, $value);
        });
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
            ? $request->session()->get('errors', new ViewErrorBag())->getBag('default')
            : call_user_func_array($this->errorsResolver, [$request]);
    }

    /**
     * Determine if the field is invalid.
     */
    public function invalid(Request $request): bool
    {
        return $this->resolveErrors($request)->has($this->getValidationKey()) ?: false;
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
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }

    /**
     * Convert the field to an array.
     */
    public function toArray(): array
    {
        return [
            'attrs' => $this->newAttributeBag(),
            'attribute' => $this->getModelAttribute(),
            'help' => $this->help,
            'label' => $this->label,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'template' => $this->getTemplate(),
        ];
    }

    /**
     * Get the form component data.
     */
    public function toFormComponent(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'attrs' => $this->newAttributeBag()->class([
                'form-control--invalid' => $this->invalid($request),
            ]),
            'error' => $this->error($request),
            'invalid' => $this->invalid($request),
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

        return [$this->getValidationKey() => Arr::flatten($rules, 1)];
    }
}
