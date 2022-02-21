<?php

namespace Cone\Root\Tests;

use Cone\Root\Extracts\Extract;
use Cone\Root\Fields\Text;
use Illuminate\Http\Request;

class LongPosts extends Extract
{
    public function fields(Request $request): array
    {
        return [
            Text::make('Title'),
        ];
    }

    public function filters(Request $request): array
    {
        return [
            Type::make(),
        ];
    }

    public function actions(Request $request): array
    {
        return [
            PublishPosts::make(),
        ];
    }

    public function widgets(Request $request): array
    {
        return [
            PostsCount::make(),
        ];
    }
}
