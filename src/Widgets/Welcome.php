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
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return [];
    }
}
