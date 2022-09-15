<?php

declare(strict_types = 1);

namespace Cone\Root\Widgets;

use Cone\Root\Widgets\Widget;

class Welcome extends Widget
{
    /**
     * The Blade template.
     *
     * @var string
     */
    protected string $template = 'root::widgets.welcome';
}
