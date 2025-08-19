<?php

declare(strict_types=1);

namespace Cone\Root\View\Components\Layout;

use Illuminate\Support\Facades\URL;
use Illuminate\View\Component;
use Illuminate\View\View;

class Notifications extends Component
{
    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.layout.notifications', [
            'url' => URL::route('root.api.notifications.index'),
        ]);
    }
}
