<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Boolean extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.boolean';

    /**
     * Create a new file field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('checkbox');
        $this->class(['form-switch__control']);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): bool
    {
        return $request->boolean($this->getRequestKey());
    }

    /**
     * Set the "checked" HTML attribute.
     */
    public function checked(bool $value = true): static
    {
        $this->setAttribute('checked', $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(Request $request, Model $model): bool
    {
        $value = filter_var(parent::resolveValue($request, $model), FILTER_VALIDATE_BOOL);

        $this->checked($value);

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = static function (Request $request, Model $model, ?bool $value): string {
                return sprintf(
                    '<span class="status %s">%s</span>',
                    $value ? 'status--success' : 'status--danger',
                    $value ? __('Yes') : __('No')
                );
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
