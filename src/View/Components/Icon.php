<?php

declare(strict_types=1);

namespace Cone\Root\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Icon extends Component
{
    /**
     * The icon name.
     */
    protected string $name;

    /**
     * Create a new component instance.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.icon', [
            'icon' => sprintf('root::icons.%s', $this->name),
        ]);
    }
}
