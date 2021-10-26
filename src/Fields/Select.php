<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Select extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'FormSelect';

    /**
     * The option resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $optionsResolver = null;

    /**
     * Set the options attribute.
     *
     * @param  array|\Closure  $value
     * @return $this
     */
    public function options(array|Closure $value): static
    {
        if (is_array($value)) {
            $value = static function () use ($value): array {
                return $value;
            };
        }

        $this->optionsResolver = $value;

        return $this;
    }

    /**
     * Resolve the options for the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    protected function resolveOptions(Request $request, Model $model): array
    {
        if (is_null($this->optionsResolver)) {
            return [];
        }

        return call_user_func_array($this->optionsResolver, [$request, $model]);
    }

    /**
     * Get the input representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'options' => $this->resolveOptions($request, $model),
        ]);
    }
}
