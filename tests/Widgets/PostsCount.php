<?php

namespace Cone\Root\Tests\Widgets;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Widgets\Widget;

class PostsCount extends Widget
{
    protected bool $async = true;

    protected string $template = 'widgets.posts-count';

    public function data(RootRequest $request): array
    {
        return [];
    }
}
