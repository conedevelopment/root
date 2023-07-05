<?php

namespace Cone\Root\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Alert extends Component
{
    /**
     * The alert type.
     */
    protected string $type = 'info';

    /**
     * Create a new component instance.
     */
    public function __construct(string $type = 'info')
    {
        $this->type = $type;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.alert', [
            'type' => $this->type,
        ]);
    }
}
