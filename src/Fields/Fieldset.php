<?php

namespace Cone\Root\Fields;

use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Fieldset extends Field
{
    use ResolvesFields;

    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.fieldset';

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        if (! is_null($this->apiUri)) {
            $field->setApiUri(sprintf('%s/%s', $this->apiUri, $field->getUriKey()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)->each(static function (Field $field) use ($request): void {
            $field->persist($request, $field->getValueForHydrate($request));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        $this->resolveFields($request)->each(static function (Field $field) use ($request): void {
            $field->resolveHydrate($request, $field->getValueForHydrate($request));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function invalid(Request $request): bool
    {
        return parent::invalid($request)
            || $this->resolveFields($request)->some(fn (Field $field): bool => $field->invalid($request));
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'fields' => $this->resolveFields($request)->all(),
                ];
            })
        );
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(Request $request, Model $model): array
    {
        return array_merge(
            parent::toValidate($request, $model),
            $this->resolveFields($request)->mapToValidate($request)
        );
    }
}
