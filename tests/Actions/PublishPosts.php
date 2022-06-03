<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\Action;
use Cone\Root\Fields\Text;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PublishPosts extends Action
{
    public function handle(Request $request, Collection $models): void
    {
        //
    }

    public function fields(Request $request): array
    {
        return [
            Text::make('Title'),
        ];
    }
}
