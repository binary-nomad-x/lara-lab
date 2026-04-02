<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \App\Services\MockStripeService
 */
class MockStripeService extends Facade {
    protected static function getFacadeAccessor(): string {
        return \App\Services\MockStripeService::class;
    }
}
