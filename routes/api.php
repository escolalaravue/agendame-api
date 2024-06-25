<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Plan\PlanController;
use App\Http\Controllers\Team\TeamController;
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

    Route::get('subscription', function() {
        $user = auth()->user();
        $team = \App\Models\Team::find(3);
        $team->createOrGetStripeCustomer([
            'name' => $user->first_name,
            'email' => $user->email,
        ]);

//        dd($team->subscriptions()->active()->get());
        $subscription = $team->newSubscription('default', 'price_1MgIzmC769vQvnJaHpnejqBa')
            ->checkout([
                'success_url' => 'http://localhost:3000/stripe/success',
                'cancel_url' => 'http://localhost:3000/stripe/error',
            ]);
        dd($subscription->url);
//        dd($user);
    });

    // Rotas que precisam de team
    Route::middleware(['team'])->group(function () {
    });
});

Route::middleware(['auth:sanctum', 'team'])->get('test', function() {
    return 'ok';
});





