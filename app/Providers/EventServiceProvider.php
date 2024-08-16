<?php

namespace App\Providers;

use App\Events\ForgotPasswordRequested;
use App\Events\UserInvited;
use App\Events\UserRegistered;
use App\Listeners\SendForgotPasswordToken;
use App\Listeners\SendInvitationEmail;
use App\Listeners\SendWelcomeEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserRegistered::class => [
            SendWelcomeEmail::class,
        ],
        UserInvited::class => [
            SendInvitationEmail::class,
        ],
        ForgotPasswordRequested::class => [
            SendForgotPasswordToken::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
