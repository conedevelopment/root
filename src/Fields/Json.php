<?php

namespace Cone\Root\Fields;

use Cone\Root\Models\FieldsetModel;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Json extends Field
{
    use ResolvesFields;

    /**
     * The Vue component.
     */
    protected string $component = 'Fieldset';

    /**
     * Handle the resolving event on the field instance.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $data = parent::toInput($request, $model);

        $data['value'] = (array) $data['value'];

        $json = FieldsetModel::make()
                    ->setRelation('parent', $model)
                    ->forceFill($data['value']);

        $fields = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToForm($request, $json)
                    ->toArray();

        return array_replace_recursive($data, [
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
