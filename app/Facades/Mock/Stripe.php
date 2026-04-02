<?php

namespace App\Facades\Mock;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Mock\StripeService
 */
class Stripe extends Facade {
    protected static function getFacadeAccessor(): string {
        return \App\Services\Mock\StripeService::class;
    }
}
