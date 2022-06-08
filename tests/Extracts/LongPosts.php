<?php

namespace Cone\Root\Tests\Extracts;

use Cone\Root\Extracts\Extract;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Tests\Actions\PublishPosts;
use Cone\Root\Tests\Filters\Published;
use Cone\Root\Tests\Widgets\PostsCount;

class LongPosts extends Extract
{
    public function filters(RootRequest $request): array
    {
        return array_merge(parent::filters($request), [
            Published::make()->multiple(),
        ]);
    }

    public function actions(RootRequest $request): array
    {
        return [
            PublishPosts::make(),
        ];
    }

    public function fields(RootRequest $request): array
    {
        return [
            Text::make('Title')->sortable()->searchable(),
        ];
    }

    public function widgets(RootRequest $request): array
    {
        return [
            PostsCount::make(),
        ];
    }
}
