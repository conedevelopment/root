<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Tag extends Field
{
    /**
     * The Vue component.
     */
    protected string $component = 'Tag';

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model): mixed {
                $default = $this->getValue($request, $model);

                return implode(', ', (array) $default);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
