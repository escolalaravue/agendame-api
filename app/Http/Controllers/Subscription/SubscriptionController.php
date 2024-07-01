<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscriptions\SubscriptionStoreRequest;
use App\Models\Plan;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __invoke(SubscriptionStoreRequest $request)
    {
        $input = $request->validated();

        $plan = Plan::query()->findOrFail($input['plan_id']);
        $stripePriceId = $plan->stripe_price_monthly_id;
        if ($input['frequency'] === 'yearly') {
            $stripePriceId = $plan->stripe_price_yearly_id;
        }

        $user = auth()->user();
        $team = \App\Models\Team::query()->where('token', $input['team_token'])->firstOrFail();

        $team->createOrGetStripeCustomer([
            'name' => $user->first_name,
            'email' => $user->email,
        ]);

        $subscription = $team->newSubscription($plan->name, $stripePriceId)
            ->checkout([
                'success_url' => 'http://localhost:3000/stripe/success',
                'cancel_url' => 'http://localhost:3000/stripe/error',
            ]);

        return [
            'stripe_checkout_url' => $subscription->url
        ];
    }
}
