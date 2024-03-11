<?php

namespace Cone\Root\Actions;

use Cone\Root\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class SendVerificationNotification extends Action
{
    /**
     * Handle the action.
     */
    public function handle(Request $request, Collection $models): void
    {
        $models->reject(static function (User $user): bool {
            return $user->hasVerifiedEmail();
        })->each(static function (User $user): void {
            $user->sendEmailVerificationNotification();
        });
    }
}
