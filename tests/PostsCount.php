<?php

namespace Cone\Root\Tests;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;

class PostsCount extends Widget
{
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
