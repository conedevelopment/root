<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Traits\ResolvesFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Fieldset extends Field
{
    use ResolvesFields;

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.fieldset';

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, mixed $value): void
    {
        $this->resolveFields($request)->each(static function (Field $field) use ($request): void {
            $field->persist($request, $field->getValueForHydrate($request));
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, mixed $value): void
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
    public function toValidate(Request $request): array
    {
        return array_merge(
            parent::toValidate($request),
            $this->resolveFields($request)->mapToValidate($request)
        );
    }
}
