<?php

declare(strict_types=1);

namespace Cone\Root\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Copyable extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(protected string $text, protected string $value)
    {
        //
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.copyable', [
            'text' => $this->text,
            'value' => $this->value,
        ]);
    }
}
