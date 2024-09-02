<?php

namespace Cone\Root\Settings;

use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Group
{
    use AsForm;
    use ResolvesFields;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the request.
     */
    public function handleFormRequest(Request $request, Model $model): void
    {
        //
    }
}
