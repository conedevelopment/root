<?php

namespace Cone\Root\View\Components\Layout;

use Cone\Root\Root;
use Illuminate\View\Component;
use Illuminate\View\View;

class Footer extends Component
{
    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.layout.footer', [
            'version' => Root::VERSION,
        ]);
    }
}
