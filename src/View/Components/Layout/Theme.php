<?php

declare(strict_types=1);

namespace Cone\Root\View\Components\Layout;

use Illuminate\Support\Facades\Cookie;
use Illuminate\View\Component;
use Illuminate\View\View;

class Theme extends Component
{
    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.layout.theme', [
            'theme' => Cookie::get('__root_theme', 'system'),
        ]);
    }
}
