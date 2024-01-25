<?php

namespace Cone\Root\Tests;

use Cone\Root\Actions\Action;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SendPasswordResetNotification extends Action
{
    /**
     * Handle the action.
     */
    public function handle(Request $request, Collection $models): void
    {
        //
    }
}
