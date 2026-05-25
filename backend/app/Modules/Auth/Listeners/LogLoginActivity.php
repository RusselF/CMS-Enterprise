<?php

namespace App\Modules\Auth\Listeners;

use App\Modules\Auth\Events\UserLoggedIn;

class LogLoginActivity
{
    public function handle(UserLoggedIn $event): void
    {
        activity()
            ->performedOn($event->user)
            ->causedBy($event->user)
            ->withProperties([
                'ip_address' => $event->ipAddress,
            ])
            ->log('logged_in');
    }
}
