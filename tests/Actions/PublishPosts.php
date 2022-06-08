<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\Action;
use Cone\Root\Fields\Text;
use Cone\Root\Http\Requests\ActionRequest;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Support\Collection;

class PublishPosts extends Action
{
    public function handle(ActionRequest $request, Collection $models): void
    {
        //
    }

    public function fields(RootRequest $request): array
    {
        return [
            Text::make('Title'),
        ];
    }
}
