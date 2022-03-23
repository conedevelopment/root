<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Actions\Action;

class ActionRequest extends ResourceRequest
{
    /**
     * Get the action bound to the request.
     *
     * @return \Cone\Root\Actions\Action
     */
    public function action(): Action
    {
        return $this->resolved();
    }
}
