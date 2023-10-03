<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Interfaces\Form;
use Cone\Root\Support\Element;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesModelValue;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;

abstract class Field extends Element implements Responsable
{
    use Conditionable;
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
     * The API URI.
     */
    protected ?string $apiUri = null;

    /**
     * The form instance.
     */
    protected ?Form $form = null;

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
     * Set the form instance.
     */
    public function setForm(Form $form): static
    {
        $this->form = $form;

        return $this;
    }

    /**
     * Get the model.
     */
    public function getModel(): Model
    {
        return $this->model ?: new class() extends Model
        {
        };
    }

    /**
     * Set the model.
     */
    public function setModel(Model $model): static
    {
        $this->model = $model;

        return $this;
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
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return str_replace('.', '-', $this->getRequestKey());
    }

    /**
     * Set the API URI.
     */
    public function setApiUri(string $apiUri): static
    {
        $this->apiUri = $apiUri;

        return $this;
    }

    /**
     * Get the API URI.
     */
    public function getApiUri(): ?string
    {
        return $this->apiUri;
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
    public function resolveValue(Request $request): mixed
    {
        $value = $this->withOldValue && $request->session()->hasOldInput($this->getRequestKey())
            ? $this->getOldValue($request)
            : $this->getValue();

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$request, $this->getModel(), $value]);
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
    public function persist(Request $request, mixed $value): void
    {
        $this->getModel()->saving(function () use ($request, $value): void {
            $this->resolveHydrate($request, $value);
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
    public function resolveHydrate(Request $request, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function () use ($value): void {
                $this->getModel()->setAttribute($this->getModelAttribute(), $value);
            };
        }

        call_user_func_array($this->hydrateResolver, [$request, $this->getModel(), $value]);
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
     * Determine if the field is invalid.
     */
    public function invalid(Request $request): bool
    {
        return $this->form?->errors($request)?->has($this->getValidationKey()) ?: false;
    }

    /**
     * Get the validation error from the request.
     */
    public function error(Request $request): ?string
    {
        return $this->form?->errors($request)?->first($this->getValidationKey()) ?: null;
    }

    /**
     * Convert the field to an array.
     */
    public function toArray(): array
    {
        return App::call(function (Request $request): array {
            return [
                'attribute' => $this->getModelAttribute(),
                'attrs' => $this->newAttributeBag()->class([
                    'form-control--invalid' => $this->invalid($request),
                ]),
                'error' => $this->error($request),
                'help' => $this->help,
                'invalid' => $this->invalid($request),
                'label' => $this->label,
                'prefix' => $this->prefix,
                'suffix' => $this->suffix,
                'value' => $this->resolveValue($request),
            ];
        });
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(Request $request): array
    {
        $model = $this->getModel();

        $key = $model->exists ? 'update' : 'create';

        $rules = array_map(
            static function (array|Closure $rule) use ($request, $model): array {
                return is_array($rule) ? $rule : call_user_func_array($rule, [$request, $model]);
            },
            Arr::only($this->rules, array_unique(['*', $key]))
        );

        return [$this->getValidationKey() => Arr::flatten($rules, 1)];
    }

    /**
     * Create an HTTP response that represents the object.
     */
    public function toResponse($request): JsonResponse
    {
        return new JsonResponse($this->toArray());
    }
}
