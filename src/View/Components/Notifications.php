<?php

namespace Cone\Root\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Notifications extends Component
{
    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.notifications', [
            //
        ]);
    }
}
