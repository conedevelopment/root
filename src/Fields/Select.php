<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Http\Request;

class Select extends Field
{
    /**
     * The selectable options.
     *
     * @var array
     */
    protected array $options = [];

    /**
     * The option resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $optionResolver = null;

    /**
     * Set the options attribute.
     *
     * @param  array|\Closure  $value
     * @return $this
     */
    public function options(array|Closure $value): self
    {
        if (is_array($value)) {
            $this->options = $value;
            $this->optionResolver = null;
        } elseif ($value instanceof Closure) {
            $this->options = [];
            $this->optionResolver = $value;
        }

        return $this;
    }

    /**
     * Resolve the options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function resolveOptions(Request $request): array
    {
        if (empty($this->options) && ! is_null($this->optionResolver)) {
            $this->options = call_user_func_array($this->optionResolver, [$request]);
        }

        return $this->options;
    }
}
