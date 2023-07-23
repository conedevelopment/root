<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesModelValue;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\View\ComponentAttributeBag;

abstract class Field implements Renderable
{
    use Authorizable;
    use HasAttributes;
    use Makeable;
    use ResolvesModelValue;

    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.input';

    /**
     * The hydrate resolver callback.
     */
    protected ?Closure $hydrateResolver = null;

    /**
     * The form instance.
     */
    protected Form $form;

    /**
     * The validation rules.
     */
    protected array $rules = [
        '*' => [],
        'create' => [],
        'update' => [],
    ];

    /**
     * The label.
     */
    protected string $label;

    /**
     * The help text.
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
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $name = null)
    {
        $this->label($label);
        $this->name($name ??= Str::of($label)->lower()->snake()->value());
        $this->id($name);

        $this->form = $form;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->getAttribute('name');
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
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
     * Set the "name" HTML attribute.
     */
    public function name(string|Closure $value): static
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * Set the "id" HTML attribute.
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
     * Resolve the model.
     */
    public function resolveModel(): Model
    {
        return $this->form->resolveModel();
    }

    /**
     * Persist the request value on the model.
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->resolveModel()->saving(function () use ($request, $value): void {
            $this->resolveHydrate($request, $value);
        });
    }

    /**
     * Get the value for hydrating the model.
     */
    public function getValueForHydrate(Request $request): mixed
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
    public function resolveHydrate(Request $request, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function () use ($value): void {
                $this->resolveModel()->setAttribute($this->getKey(), $value);
            };
        }

        call_user_func_array($this->hydrateResolver, [$request, $this->resolveModel(), $value]);
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
     * Make a new attribute bag.
     */
    public function newAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->resolveAttributes());
    }

    /**
     * Get the data for the view.
     */
    public function data(Request $request): array
    {
        return [
            'attrs' => $this->newAttributeBag(),
            'error' => $this->form->errors($request)->first($this->getKey()),
            'help' => $this->help,
            'invalid' => $this->form->errors($request)->has($this->getKey()),
            'label' => $this->label,
            'prefix' => $this->prefix,
            'suffix' => $this->suffix,
            'value' => $this->resolveValue(),
        ];
    }

    /**
     * Render the field.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(Request $request): array
    {
        $model = $this->resolveModel();

        $key = $model->exists ? 'update' : 'create';

        $rules = array_map(
            static function (array|Closure $rule) use ($request, $model): array {
                return is_array($rule) ? $rule : call_user_func_array($rule, [$request, $model]);
            },
            Arr::only($this->rules, array_unique(['*', $key]))
        );

        return [$this->getKey() => Arr::flatten($rules, 1)];
    }
}