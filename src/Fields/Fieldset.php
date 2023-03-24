<?php

namespace Cone\Root\Fields;

use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Fieldset extends Field
{
    use ResolvesFields;

    /**
     * The Vue component.
     */
    protected string $component = 'Fieldset';

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model): void
    {
        $this->resolveHydrate($request, $model, $this->getValueForHydrate($request, $model));

        $this->resolveFields($request)
            ->available($request, $model)
            ->each(static function (Field $field) use ($request, $model): void {
                $field->persist($request, $model);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)
            ->available($request, $model)
            ->each(static function (Field $field) use ($request, $model, $value): void {
                $field->resolveHydrate($request, $model, $value[$field->getKey()] ?? null);
            });
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $fields = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToForm($request, $model)
                    ->toArray();

        return array_replace_recursive(parent::toInput($request, $model), [
            'fields' => $fields,
            'formatted_value' => array_column($fields, 'formatted_value', 'name'),
            'value' => array_column($fields, 'value', 'name'),
        ]);
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(Request $request, Model $model): array
    {
        $rules = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToValidate($request, $model);

        return array_merge(
            parent::toValidate($request, $model),
            Collection::make($rules)
                    ->mapWithKeys(function (array $rules, string $key): array {
                        return [sprintf('%s.%s', $this->getKey(), $key) => $rules];
                    })
                    ->toArray(),
        );
    }
}
