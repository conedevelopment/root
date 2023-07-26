<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Fields\Field;
use Cone\Root\Form\Form;
use Cone\Root\Interfaces\Routable;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Fields
{
    use ForwardsCalls;

    /**
     * The form instance.
     */
    public readonly Form $form;

    /**
     * The fields collection.
     */
    protected Collection $fields;

    /**
     * Create a new fields instance.
     */
    public function __construct(Form $form, array $fields = [])
    {
        $this->form = $form;
        $this->fields = new Collection($fields);
    }

    /**
     * Make a new text field.
     */
    public function text(string $label, string $name = null): Text
    {
        return $this->field(Text::class, $label, $name);
    }

    /**
     * Make a new email field.
     */
    public function email(string $label, string $name = null): Email
    {
        return $this->field(Email::class, $label, $name);
    }

    /**
     * Make a new textarea field.
     */
    public function textarea(string $label, string $name = null): Textarea
    {
        return $this->field(Textarea::class, $label, $name);
    }

    /**
     * Make a new number field.
     */
    public function number(string $label, string $name = null): Number
    {
        return $this->field(Number::class, $label, $name);
    }

    /**
     * Make a new range field.
     */
    public function range(string $label, string $name = null): Range
    {
        return $this->field(Range::class, $label, $name);
    }

    /**
     * Make a new select field.
     */
    public function select(string $label, string $name = null): Select
    {
        return $this->field(Select::class, $label, $name);
    }

    /**
     * Make a new boolean field.
     */
    public function boolean(string $label, string $name = null): Boolean
    {
        return $this->field(Boolean::class, $label, $name);
    }

    /**
     * Make a new checkbox field.
     */
    public function checkbox(string $label, string $name = null): Checkbox
    {
        return $this->field(Checkbox::class, $label, $name);
    }

    /**
     * Make a new date field.
     */
    public function date(string $label, string $name = null): Date
    {
        return $this->field(Date::class, $label, $name);
    }

    /**
     * Make a new radio field.
     */
    public function radio(string $label, string $name = null): Radio
    {
        return $this->field(Radio::class, $label, $name);
    }

    /**
     * Make a new hidden field.
     */
    public function hidden(string $label, string $name = null): Hidden
    {
        return $this->field(Hidden::class, $label, $name);
    }

    /**
     * Make a new has many field.
     */
    public function hasMany(string $label, string $name = null, Closure|string $relation = null): HasMany
    {
        return $this->field(HasMany::class, $label, $name, $relation);
    }

    /**
     * Make a new file field.
     */
    public function file(string $label, string $name = null, Closure|string $relation = null): File
    {
        return $this->field(File::class, $label, $name, $relation);
    }

    /**
     * Create a new method.
     */
    public function field(string $field, ...$params): Field
    {
        $instance = new $field($this->form, ...$params);

        $this->push($instance);

        return $instance;
    }

    /**
     * Persist the request value on the model.
     */
    public function persist(Request $request): void
    {
        $this->fields->each(static function (Field $field) use ($request): void {
            $field->persist(
                $request, $field->getValueForHydrate($request)
            );
        });
    }

    /**
     * Map the fields to validate.
     */
    public function mapToValidate(Request $request): array
    {
        return $this->fields->reduce(static function (array $rules, Field $field) use ($request): array {
            return array_merge_recursive($rules, $field->toValidate($request));
        }, []);
    }

    /**
     * Register the field routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->prefix('fields')->group(function (Router $router): void {
            $this->fields->each(static function (Field $field) use ($router): void {
                if ($field instanceof Routable) {
                    $field->registerRoutes($router);
                }
            });
        });
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->fields, $method, $parameters);
    }
}
