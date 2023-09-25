<?php

namespace Cone\Root\View\Components\Layout;

use Cone\Root\Support\Facades\Navigation;
use Illuminate\View\Component;
use Illuminate\View\View;

class Sidebar extends Component
{
    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.layout.sidebar', [
            'groups' => Navigation::location('sidebar')->groups(),
        ]);
    }
}
