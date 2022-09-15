<?php

declare(strict_types = 1);

namespace Cone\Root\Http\Requests;

use Cone\Root\Widgets\Widget;

class WidgetRequest extends RootRequest
{
    /**
     * Get the widget bound to the request.
     *
     * @return \Cone\Root\Widgets\Widget
     */
    public function widget(): Widget
    {
        return $this->resolved();
    }
}
