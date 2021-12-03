<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Media extends MorphToMany
{
    /**
     * Indicates if the options should be lazily populated.
     *
     * @var bool
     */
    protected bool $async = true;

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Media';

    /**
     * Resolve the options for the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        return [];
    }
}
