<?php

namespace Cone\Root\Widgets;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;

class Welcome extends Widget
{
    /**
     * The Blade template.
     *
     * @var string
     */
    protected string $template = 'root::widgets.welcome';

    /**
     * Get the data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function data(Request $request): array
    {
        return [];
    }
}
