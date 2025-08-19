<?php

declare(strict_types=1);

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
     * Indicates whether the alert is closable.
     */
    protected bool $closable = false;

    /**
     * Create a new component instance.
     */
    public function __construct(string $type = 'info', bool $closable = false)
    {
        $this->type = $type;
        $this->closable = $closable;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.alert', [
            'type' => $this->type,
            'closable' => $this->closable,
            'class' => match ($this->type) {
                'error' => 'alert--danger',
                default => 'alert--'.$this->type,
            },
        ]);
    }
}
