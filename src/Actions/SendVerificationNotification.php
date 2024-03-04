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
        $models->filter(static function (User $user): bool {
            return is_null($user->email_verified_at);
        })->each(static function (User $user): void {
            $user->sendEmailVerificationNotification();
        });
    }
}
