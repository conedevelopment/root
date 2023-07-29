<?php

namespace Cone\Root\Form\Fields;

use Closure;
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
     * Add a new field to the collection.
     */
    public function field(string $field, string $label, string $key = null, ...$params): Field
    {
        $instance = new $field($this->form, $label, $key, ...$params);

        $this->push($instance);

        return $instance;
    }

    /**
     * Make a new text field.
     */
    public function text(string $label, string $key = null): Text
    {
        return $this->field(Text::class, $label, $key);
    }

    /**
     * Make a new email field.
     */
    public function email(string $label, string $key = null): Email
    {
        return $this->field(Email::class, $label, $key);
    }

    /**
     * Make a new textarea field.
     */
    public function textarea(string $label, string $key = null): Textarea
    {
        return $this->field(Textarea::class, $label, $key);
    }

    /**
     * Make a new number field.
     */
    public function number(string $label, string $key = null): Number
    {
        return $this->field(Number::class, $label, $key);
    }

    /**
     * Make a new range field.
     */
    public function range(string $label, string $key = null): Range
    {
        return $this->field(Range::class, $label, $key);
    }

    /**
     * Make a new select field.
     */
    public function select(string $label, string $key = null): Select
    {
        return $this->field(Select::class, $label, $key);
    }

    /**
     * Make a new boolean field.
     */
    public function boolean(string $label, string $key = null): Boolean
    {
        return $this->field(Boolean::class, $label, $key);
    }

    /**
     * Make a new checkbox field.
     */
    public function checkbox(string $label, string $key = null): Checkbox
    {
        return $this->field(Checkbox::class, $label, $key);
    }

    /**
     * Make a new date field.
     */
    public function date(string $label, string $key = null): Date
    {
        return $this->field(Date::class, $label, $key);
    }

    /**
     * Make a new radio field.
     */
    public function radio(string $label, string $key = null): Radio
    {
        return $this->field(Radio::class, $label, $key);
    }

    /**
     * Make a new hidden field.
     */
    public function hidden(string $label, string $key = null): Hidden
    {
        return $this->field(Hidden::class, $label, $key);
    }

    /**
     * Make a new file field.
     */
    public function file(string $label, string $key = null, Closure|string $relation = null): File
    {
        return $this->field(File::class, $label, $key, $relation);
    }

    /**
     * Make a new media field.
     */
    public function media(string $label, string $key = null, Closure|string $relation = null): Media
    {
        return $this->field(Media::class, $label, $key, $relation);
    }

    /**
     * Make a new fieldset field.
     */
    public function fieldset(string $label, string $key = null): Fieldset
    {
        return $this->field(Fieldset::class, $label, $key);
    }

    /**
     * Make a new has one field.
     */
    public function hasOne(string $label, string $key = null, Closure|string $relation = null): HasOne
    {
        return $this->field(HasOne::class, $label, $key, $relation);
    }

    /**
     * Make a new has many field.
     */
    public function hasMany(string $label, string $key = null, Closure|string $relation = null): HasMany
    {
        return $this->field(HasMany::class, $label, $key, $relation);
    }

    /**
     * Make a new belongs to field.
     */
    public function belongsTo(string $label, string $key = null, Closure|string $relation = null): BelongsTo
    {
        return $this->field(BelongsTo::class, $label, $key, $relation);
    }

    /**
     * Make a new belongs to many field.
     */
    public function belongsToMany(string $label, string $key = null, Closure|string $relation = null): BelongsToMany
    {
        return $this->field(BelongsToMany::class, $label, $key, $relation);
    }

    /**
     * Make a new morph one field.
     */
    public function morphOne(string $label, string $key = null, Closure|string $relation = null): MorphOne
    {
        return $this->field(MorphOne::class, $label, $key, $relation);
    }

    /**
     * Make a new morph many field.
     */
    public function morphMany(string $label, string $key = null, Closure|string $relation = null): MorphMany
    {
        return $this->field(MorphMany::class, $label, $key, $relation);
    }

    /**
     * Make a new morph to field.
     */
    public function morphTo(string $label, string $key = null, Closure|string $relation = null): MorphTo
    {
        return $this->field(MorphTo::class, $label, $key, $relation);
    }

    /**
     * Make a new morph to many field.
     */
    public function morphToMany(string $label, string $key = null, Closure|string $relation = null): MorphToMany
    {
        return $this->field(MorphToMany::class, $label, $key, $relation);
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
