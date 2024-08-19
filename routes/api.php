<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Plan\PlanController;
use App\Http\Controllers\Subscription\SubscriptionController;
use App\Http\Controllers\Team\TeamController;
use App\Http\Controllers\Team\TeamInvitationController;
use App\Http\Controllers\Team\TeamMemberController;
use App\Http\Controllers\User\MeController;
use Illuminate\Support\Facades\Route;

Route::post('login', LoginController::class);
Route::post('logout', LogoutController::class);
Route::post('register', RegisterController::class);
Route::post('verify-email', VerifyEmailController::class);
Route::post('forgot-password', ForgotPasswordController::class);
Route::post('reset-password', ResetPasswordController::class);
Route::get('plans', PlanController::class);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('me', [MeController::class, 'show']);
    Route::get('teams', [TeamController::class, 'index']);
    Route::post('teams', [TeamController::class, 'store']);
    Route::put('teams/{team:token}', [TeamController::class, 'update']);
    Route::delete('teams/{team:token}', [TeamController::class, 'destroy']);

    Route::post('subscription', SubscriptionController::class);

    // Rotas que precisam de team
    Route::middleware(['team'])->group(function () {
        Route::get('team-members', [TeamMemberController::class, 'index']);
        Route::put('team-members/{user}', [TeamMemberController::class, 'update']);
        Route::delete('team-members/{user}', [TeamMemberController::class, 'kick']);

        Route::get('team-invitations', [TeamInvitationController::class, 'index']);
        Route::post('team-invitations', [TeamInvitationController::class, 'store']);
        Route::get('team-invitations/{teamInvitation:token}', [TeamInvitationController::class, 'show'])
            ->withoutMiddleware(['team', 'auth:sanctum']);
        Route::delete('team-invitations/{teamInvitation}', [TeamInvitationController::class, 'destroy']);
    });
});

Route::middleware(['auth:sanctum', 'team'])->get('test', function() {
    return 'ok';
});





