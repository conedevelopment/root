<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
     * Register the given fields.
     */
    public function register(array|Field $fields): static
    {
        foreach (Arr::wrap($fields) as $field) {
            $this->fields->push($field);
        }

        return $this;
    }

    /**
     * Add a new field to the collection.
     */
    public function field(string $field, string $label, string $modelAttribute = null, ...$params): Field
    {
        $instance = new $field($this->form, $label, $modelAttribute, ...$params);

        $this->register($instance);

        return $instance;
    }

    /**
     * Make a new text field.
     */
    public function text(string $label, string $modelAttribute = null): Text
    {
        return $this->field(Text::class, $label, $modelAttribute);
    }

    /**
     * Make a new email field.
     */
    public function email(string $label, string $modelAttribute = null): Email
    {
        return $this->field(Email::class, $label, $modelAttribute);
    }

    /**
     * Make a new textarea field.
     */
    public function textarea(string $label, string $modelAttribute = null): Textarea
    {
        return $this->field(Textarea::class, $label, $modelAttribute);
    }

    /**
     * Make a new number field.
     */
    public function number(string $label, string $modelAttribute = null): Number
    {
        return $this->field(Number::class, $label, $modelAttribute);
    }

    /**
     * Make a new range field.
     */
    public function range(string $label, string $modelAttribute = null): Range
    {
        return $this->field(Range::class, $label, $modelAttribute);
    }

    /**
     * Make a new select field.
     */
    public function select(string $label, string $modelAttribute = null): Select
    {
        return $this->field(Select::class, $label, $modelAttribute);
    }

    /**
     * Make a new boolean field.
     */
    public function boolean(string $label, string $modelAttribute = null): Boolean
    {
        return $this->field(Boolean::class, $label, $modelAttribute);
    }

    /**
     * Make a new checkbox field.
     */
    public function checkbox(string $label, string $modelAttribute = null): Checkbox
    {
        return $this->field(Checkbox::class, $label, $modelAttribute);
    }

    /**
     * Make a new tag field.
     */
    public function tag(string $label, string $modelAttribute = null): Tag
    {
        return $this->field(Tag::class, $label, $modelAttribute);
    }

    /**
     * Make a new date field.
     */
    public function date(string $label, string $modelAttribute = null): Date
    {
        return $this->field(Date::class, $label, $modelAttribute);
    }

    /**
     * Make a new radio field.
     */
    public function radio(string $label, string $modelAttribute = null): Radio
    {
        return $this->field(Radio::class, $label, $modelAttribute);
    }

    /**
     * Make a new hidden field.
     */
    public function hidden(string $label, string $modelAttribute = null): Hidden
    {
        return $this->field(Hidden::class, $label, $modelAttribute);
    }

    /**
     * Make a new editor field.
     */
    public function editor(string $label, string $modelAttribute = null): Editor
    {
        return $this->field(Editor::class, $label, $modelAttribute);
    }

    /**
     * Make a new file field.
     */
    public function file(string $label, string $modelAttribute = null, Closure|string $relation = null): File
    {
        return $this->field(File::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new media field.
     */
    public function media(string $label, string $modelAttribute = null, Closure|string $relation = null): Media
    {
        return $this->field(Media::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new fieldset field.
     */
    public function fieldset(string $label, string $modelAttribute = null): Fieldset
    {
        return $this->field(Fieldset::class, $label, $modelAttribute);
    }

    /**
     * Make a new repeater field.
     */
    public function repeater(string $label, string $modelAttribute = null): Repeater
    {
        return $this->field(Repeater::class, $label, $modelAttribute);
    }

    /**
     * Make a new repeater field.
     */
    public function dropdown(string $label, string $modelAttribute = null): Dropdown
    {
        return $this->field(Dropdown::class, $label, $modelAttribute);
    }

    /**
     * Make a new has one field.
     */
    public function hasOne(string $label, string $modelAttribute = null, Closure|string $relation = null): HasOne
    {
        return $this->field(HasOne::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new has many field.
     */
    public function hasMany(string $label, string $modelAttribute = null, Closure|string $relation = null): HasMany
    {
        return $this->field(HasMany::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new belongs to field.
     */
    public function belongsTo(string $label, string $modelAttribute = null, Closure|string $relation = null): BelongsTo
    {
        return $this->field(BelongsTo::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new belongs to many field.
     */
    public function belongsToMany(string $label, string $modelAttribute = null, Closure|string $relation = null): BelongsToMany
    {
        return $this->field(BelongsToMany::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new morph one field.
     */
    public function morphOne(string $label, string $modelAttribute = null, Closure|string $relation = null): MorphOne
    {
        return $this->field(MorphOne::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new morph many field.
     */
    public function morphMany(string $label, string $modelAttribute = null, Closure|string $relation = null): MorphMany
    {
        return $this->field(MorphMany::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new morph to field.
     */
    public function morphTo(string $label, string $modelAttribute = null, Closure|string $relation = null): MorphTo
    {
        return $this->field(MorphTo::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new morph to many field.
     */
    public function morphToMany(string $label, string $modelAttribute = null, Closure|string $relation = null): MorphToMany
    {
        return $this->field(MorphToMany::class, $label, $modelAttribute, $relation);
    }

    /**
     * Make a new meta field.
     */
    public function meta(string $label, string $modelAttribute = null, Closure|string $relation = null): Meta
    {
        return $this->field(Meta::class, $label, $modelAttribute, $relation);
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
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->fields, $method, $parameters);
    }
}
