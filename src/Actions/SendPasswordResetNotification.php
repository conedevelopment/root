<?php

declare(strict_types=1);

namespace Cone\Root\Actions;

use Cone\Root\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class SendPasswordResetNotification extends Action
{
    /**
     * Handle the action.
     */
    public function handle(Request $request, Collection $models): void
    {
        $broker = Password::broker();

        $models->each(static function (User $user) use ($broker): void {
            $user->sendPasswordResetNotification(
                $broker->createToken($user)
            );
        });
    }
}
