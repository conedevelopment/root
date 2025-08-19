<?php

declare(strict_types=1);

namespace Cone\Root\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Chart extends Component
{
    /**
     * The icon name.
     */
    protected array $config = [];

    /**
     * Create a new component instance.
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.chart', [
            'config' => $this->config,
        ]);
    }
}
