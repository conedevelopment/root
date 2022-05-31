<?php

namespace Cone\Root\Tests;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;

class PostsCount extends Widget
{
    protected string $template = 'root::widgets.welcome';

    public function data(Request $request): array
    {
        return [];
    }
}
