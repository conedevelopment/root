<?php

namespace Cone\Root\View\Components\layout;

use Illuminate\View\Component;
use Illuminate\View\View;

class Header extends Component
{
    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.layout.header');
    }
}
