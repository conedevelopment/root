<?php

namespace Cone\Root\Http\Requests;

use Cone\Root\Extracts\Extract;

class ExtractRequest extends RootRequest
{
    /**
     * Get the extract bound to the request.
     *
     * @return \Cone\Root\Extracts\Extract
     */
    public function extract(): Extract
    {
        return $this->route('resolved');
    }
}
