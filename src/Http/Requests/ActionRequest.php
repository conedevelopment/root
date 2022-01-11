<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Actions\Action;

class ActionRequest extends RootRequest
{
    /**
     * Get the action bound to the request.
     *
     * @return \Cone\Root\Actions\Action
     */
    public function action(): Action
    {
        return $this->route('resolved');
    }
}
