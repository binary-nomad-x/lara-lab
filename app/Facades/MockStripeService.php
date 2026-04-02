<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\Mock\StripeService
 */
class MockStripeService extends Facade {
    protected static function getFacadeAccessor(): string {
        return \App\Services\Mock\StripeService::class;
    }
}
