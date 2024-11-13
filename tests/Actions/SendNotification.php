<?php

namespace Cone\Root\Tests\Actions;

use Cone\Root\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SendNotification extends Action
{
    public function handle(Request $request, Collection $models): void
    {
        //
    }
}
