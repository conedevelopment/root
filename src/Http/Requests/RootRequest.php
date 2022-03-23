<?php

namespace Cone\Root\Http\Requests;

use Illuminate\Http\Request;

class RootRequest extends Request
{
    /**
     * Get the resolved component.
     *
     * @return mixed
     */
    public function resolved(): mixed
    {
        return $this->route('resolved');
    }
}
