<?php

namespace Cone\Root\View\Components\Layout;

use Cone\Root\Root;
use Illuminate\Http\Request;
use Illuminate\View\Component;
use Illuminate\View\View;

class Breadcrumbs extends Component
{
    /**
     * The HTTP request.
     */
    protected Request $request;

    /**
     * Create a new component instance.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the view / view contents that represent the component.
     */
    public function render(): View
    {
        return $this->view('root::components.layout.breadcrumbs', [
            'breadcrumbs' => Root::instance()->breadcrumbs->resolve($this->request),
        ]);
    }
}
