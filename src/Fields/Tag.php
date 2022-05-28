<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;

class Tag extends Field
{
    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Tag';

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (RootRequest $request, Model $model): mixed {
                $default = $this->getDefaultValue($request, $model);

                return implode(', ', (array) $default);
            };
        }

        return parent::resolveFormat($request, $model);
    }
}
