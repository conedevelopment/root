<?php

namespace Cone\Root\View\Components\Table;

use Cone\Root\Widgets\Widget as Handler;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Widget extends Component
{
    /**
     * The widget instance.
     */
    protected Handler $widget;

    /**
     * Create a new component instance.
     */
    public function __construct(Handler $widget)
    {
        $this->widget = $widget;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.widgets.widget', [
            //
        ]);
    }
}
