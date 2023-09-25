<?php

namespace Cone\Root\View\Components\Layout;

use Illuminate\View\Component;
use Illuminate\View\View;

class SidebarGroup extends Component
{
    /**
     * The group title.
     */
    protected string $title;

    /**
     * The group items.
     */
    protected array $items;

    /**
     * Create a new controller instance.
     */
    public function __construct(string $title, array $items)
    {
        $this->title = $title;
        $this->items = $items;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.layout.sidebar-group', [
            'title' => $this->title,
            'items' => $this->items,
        ]);
    }
}
