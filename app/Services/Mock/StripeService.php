<?php

namespace App\Services\Mock;

use Illuminate\Support\Str;

class StripeService {

    public function __construct() {
    }

    /**
     * Generates a realistic StripeService Transaction (Charge/PaymentIntent) mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generateTransaction(array $overrides = []): array {
        // Generate realistic random data
        $amount = $overrides['amount'] ?? rand(500, 50000); // Amount in cents ($5.00 - $500.00)
        $currency = $overrides['currency'] ?? 'usd';
        $status = $overrides['status'] ?? 'succeeded';
        $id = 'ch_' . Str::random(24); // StripeService ID format
        $customerId = 'cus_' . Str::random(14);

        // Realistic Card Brands & Last4
        $cards = [
            ['brand' => 'visa', 'last4' => '4242', 'exp_month' => 12, 'exp_year' => 2028],
            ['brand' => 'mastercard', 'last4' => '5555', 'exp_month' => 9, 'exp_year' => 2027],
            ['brand' => 'amex', 'last4' => '3782', 'exp_month' => 4, 'exp_year' => 2026],
        ];
        $card = $cards[array_rand($cards)];

        // Base Structure mimicking StripeService's API Response (V1)
        $transaction = [
            'id' => $id,
            'object' => 'charge',
            'amount' => $amount,
            'amount_captured' => $status === 'succeeded' ? $amount : 0,
            'amount_refunded' => 0,
            'application' => null,
            'application_fee' => null,
            'application_fee_amount' => null,
            'balance_transaction' => 'txn_' . Str::random(24),
            'billing_details' => [
                'address' => [
                    'city' => fake()->city(),
                    'country' => fake()->countryCode(),
                    'line1' => fake()->streetAddress(),
                    'line2' => null,
                    'postal_code' => fake()->postcode(),
                    'state' => fake()->word(),
                ],
                'email' => fake()->safeEmail(),
                'name' => fake()->name(),
                'phone' => null,
            ],
            'calculated_statement_descriptor' => 'DEVFLOW SAAS',
            'captured' => $status === 'succeeded',
            'created' => now()->timestamp,
            'currency' => $currency,
            'customer' => $customerId,
            'description' => $overrides['description'] ?? 'Subscription update for DevFlow Pro',
            'destination' => null,
            'dispute' => null,
            'disputed' => false,
            'failure_balance_transaction' => null,
            'failure_code' => null,
            'failure_message' => null,
            'fraud_details' => [],
            'invoice' => 'in_' . Str::random(24),
            'livemode' => false,
            'metadata' => [
                'order_id' => 'ord_' . Str::random(10),
                'user_id' => (string)($overrides['user_id'] ?? 1),
            ],
            'on_behalf_of' => null,
            'order' => null,
            'outcome' => [
                'network_status' => 'approved_by_network',
                'reason' => null,
                'risk_level' => 'normal',
                'risk_score' => rand(10, 40),
                'seller_message' => 'Payment complete.',
                'type' => 'authorized',
            ],
            'paid' => $status === 'succeeded',
            'payment_intent' => 'pi_' . Str::random(24),
            'payment_method' => 'pm_' . Str::random(24),
            'payment_method_details' => [
                'card' => [
                    'brand' => $card['brand'],
                    'checks' => [
                        'address_line1_check' => 'pass',
                        'address_postal_code_check' => 'pass',
                        'cvc_check' => 'pass',
                    ],
                    'country' => 'US',
                    'exp_month' => $card['exp_month'],
                    'exp_year' => $card['exp_year'],
                    'fingerprint' => Str::random(),
                    'funding' => 'credit',
                    'installments' => null,
                    'last4' => $card['last4'],
                    'mandate' => null,
                    'network' => 'visa',
                    'three_d_secure' => null,
                    'wallet' => null,
                ],
                'type' => 'card',
            ],
            'receipt_email' => fake()->safeEmail(),
            'receipt_number' => null,
            'receipt_url' => 'https://pay.stripe.com/receipts/payment/' . Str::random(20),
            'refunded' => false,
            'refunds' => [
                'object' => 'list',
                'data' => [],
                'has_more' => false,
                'total_count' => 0,
                'url' => '/v1/charges/' . $id . '/refunds',
            ],
            'review' => null,
            'shipping' => null,
            'source' => null,
            'source_transfer' => null,
            'statement_descriptor' => 'DEVFLOW SAAS',
            'statement_descriptor_suffix' => null,
            'status' => $status,
            'transfer_data' => null,
            'transfer_group' => null,
        ];

        // Merge overrides if any specific field needs changing
        return array_merge_recursive($transaction, $overrides);

    }

    /**
     * Generates a realistic Stripe Invoice mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generateInvoice(array $overrides = []): array {
        $invoiceId = 'in_' . Str::random(24);
        $customerId = 'cus_' . Str::random(14);

        $invoice = [
            'id' => $invoiceId,
            'object' => 'invoice',
            'account_country' => 'US',
            'account_name' => 'DevFlow Inc.',
            'amount_due' => $overrides['amount_due'] ?? rand(500, 50000),
            'amount_paid' => $overrides['amount_paid'] ?? 0,
            'amount_remaining' => $overrides['amount_remaining'] ?? rand(500, 50000),
            'application_fee_amount' => null,
            'attempt_count' => 0,
            'attempted' => false,
            'auto_advance' => true,
            'billing_reason' => 'subscription_cycle',
            'charge' => null,
            'collection_method' => 'charge_automatically',
            'created' => now()->timestamp,
            'currency' => $overrides['currency'] ?? 'usd',
            'customer' => $customerId,
            'customer_email' => fake()->safeEmail(),
            'customer_name' => fake()->name(),
            'customer_tax_exempt' => 'none',
            'default_payment_method' => 'pm_' . Str::random(24),
            'description' => $overrides['description'] ?? 'Monthly subscription for DevFlow Pro',
            'discount' => null,
            'due_date' => null,
            'ending_balance' => null,
            'footer' => 'Thank you for your business!',
            'hosted_invoice_url' => 'https://pay.stripe.com/invoice/' . Str::random(20),
            'invoice_pdf' => 'https://pay.stripe.com/invoice/' . Str::random(20) . '/pdf',
            'last_finalization_error' => null,
            'lines' => [
                'object' => 'list',
                'data' => [
                    [
                        'id' => 'il_' . Str::random(24),
                        'object' => 'line_item',
                        'amount' => $overrides['amount_due'] ?? rand(500, 50000),
                        'currency' => $overrides['currency'] ?? 'usd',
                        'description' => 'Subscription for DevFlow Pro',
                        'period' => [
                            'start' => now()->timestamp,
                            'end' => now()->addMonth()->timestamp,
                        ],
                        'plan' => [
                            'id' => 'plan_' . Str::random(24),
                            'object' => 'plan',
                            'active' => true,
                            'amount' => $overrides['amount_due'] ?? rand(500, 50000),
                            'currency' => $overrides['currency'] ?? 'usd',
                            'interval' => 'month',
                            'interval_count' => 1,
                            'nickname' => 'Pro Plan',
                            'product' => 'prod_' . Str::random(24),
                        ],
                        'proration' => false,
                        'quantity' => 1,
                        'subscription' => 'sub_' . Str::random(24),
                        'type' => 'subscription',
                    ],
                ],
                'has_more' => false,
                'total_count' => 1,
                'url' => '/v1/invoices/' . $invoiceId . '/lines',
            ],
            'livemode' => false,
            'metadata' => [],
            'next_payment_attempt' => now()->addDays(7)->timestamp,
            'number' => 'INV-' . strtoupper(Str::random(8)),
            'paid' => false,
            'payment_intent' => 'pi_' . Str::random(24),
            'period_end' => now()->timestamp,
            'period_start' => now()->subMonth()->timestamp,
            'receipt_number' => null,
            'starting_balance' => 0,
            'status' => $overrides['status'] ?? 'draft',
            'status_transitions' => [
                'finalized_at' => null,
                'marked_uncollectible_at' => null,
                'paid_at' => null,
                'voided_at' => null,
            ],
            'subscription' => 'sub_' . Str::random(24),
            'subtotal' => $overrides['amount_due'] ?? rand(500, 50000),
            'tax' => null,
            'total' => $overrides['amount_due'] ?? rand(500, 50000),
            'webhooks_delivered_at' => null,
        ];

        return array_merge_recursive($invoice, $overrides);
    }

    /**
     * Generates a realistic Stripe Subscription mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generateSubscription(array $overrides = []): array {
        $subscriptionId = 'sub_' . Str::random(24);
        $customerId = 'cus_' . Str::random(14);

        $subscription = [
            'id' => $subscriptionId,
            'object' => 'subscription',
            'application_fee_percent' => null,
            'automatic_tax' => [
                'enabled' => false,
            ],
            'billing_cycle_anchor' => now()->timestamp,
            'billing_thresholds' => null,
            'cancel_at' => null,
            'cancel_at_period_end' => false,
            'canceled_at' => null,
            'collection_method' => 'charge_automatically',
            'created' => now()->timestamp,
            'current_period_end' => now()->addMonth()->timestamp,
            'current_period_start' => now()->timestamp,
            'customer' => $customerId,
            'days_until_due' => null,
            'default_payment_method' => 'pm_' . Str::random(24),
            'default_source' => null,
            'default_tax_rates' => [],
            'discount' => null,
            'ended_at' => null,
            'items' => [
                'object' => 'list',
                'data' => [
                    [
                        'id' => 'si_' . Str::random(24),
                        'object' => 'subscription_item',
                        'billing_thresholds' => null,
                        'created' => now()->timestamp,
                        'metadata' => [],
                        'plan' => [
                            'id' => 'plan_' . Str::random(24),
                            'object' => 'plan',
                            'active' => true,
                            'amount' => $overrides['amount'] ?? rand(500, 50000),
                            'currency' => $overrides['currency'] ?? 'usd',
                            'interval' => 'month',
                            'interval_count' => 1,
                            'nickname' => 'Pro Plan',
                            'product' => 'prod_' . Str::random(24),
                        ],
                        'price' => [
                            'id' => 'price_' . Str::random(24),
                            'object' => 'price',
                            'active' => true,
                            'billing_scheme' => 'per_unit',
                            'created' => now()->timestamp,
                            'currency' => $overrides['currency'] ?? 'usd',
                            'livemode' => false,
                            'lookup_key' => null,
                            'metadata' => [],
                            'nickname' => 'Pro Plan',
                            'product' => 'prod_' . Str::random(24),
                            'recurring' => [
                                'aggregate_usage' => null,
                                'interval' => 'month',
                                'interval_count' => 1,
                                'trial_period_days' => null,
                                'usage_type' => 'licensed',
                            ],
                            'tiers_mode' => null,
                            'transform_quantity' => null,
                            'type' => 'recurring',
                            'unit_amount' => $overrides['amount'] ?? rand(500, 50000),
                        ],
                        'quantity' => 1,
                        'subscription' => $subscriptionId,
                        'tax_rates' => [],
                    ],
                ],
                'has_more' => false,
                'total_count' => 1,
                'url' => '/v1/subscription_items?subscription=' . $subscriptionId,
            ],
            'latest_invoice' => 'in_' . Str::random(24),
            'livemode' => false,
            'metadata' => [],
            'next_pending_invoice_item_invoice' => null,
            'pause_collection' => null,
            'pending_invoice_item_interval' => null,
            'pending_setup_intent' => null,
            'pending_update' => null,
            'plan' => [
                'id' => 'plan_' . Str::random(24),
                'object' => 'plan',
                'active' => true,
                'amount' => $overrides['amount'] ?? rand(500, 50000),
                'currency' => $overrides['currency'] ?? 'usd',
                'interval' => 'month',
                'interval_count' => 1,
                'nickname' => 'Pro Plan',
                'product' => 'prod_' . Str::random(24),
            ],
            'quantity' => 1,
            'schedule' => null,
            'start_date' => now()->timestamp,
            'status' => $overrides['status'] ?? 'active',
            'test_clock' => null,
            'transfer_data' => null,
            'trial_end' => null,
            'trial_start' => null,
        ];

        return array_merge_recursive($subscription, $overrides);
    }

    /**
     * Generates a realistic Stripe Promotion Code mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generatePromotionCode(array $overrides = []): array {
        $promotionCodeId = 'promo_' . Str::random(24);

        $promotionCode = [
            'id' => $promotionCodeId,
            'object' => 'promotion_code',
            'active' => true,
            'code' => strtoupper(Str::random(8)),
            'coupon' => [
                'id' => 'coupon_' . Str::random(24),
                'object' => 'coupon',
                'amount_off' => null,
                'created' => now()->timestamp,
                'currency' => $overrides['currency'] ?? 'usd',
                'duration' => 'repeating',
                'duration_in_months' => 3,
                'livemode' => false,
                'max_redemptions' => null,
                'metadata' => [],
                'name' => 'Spring Sale',
                'percent_off' => 20,
                'redeem_by' => now()->addMonths(6)->timestamp,
                'times_redeemed' => 0,
                'valid' => true,
            ],
            'created' => now()->timestamp,
            'customer' => null,
            'expires_at' => now()->addMonths(6)->timestamp,
            'livemode' => false,
            'max_redemptions' => null,
            'metadata' => [],
            'restrictions' => [
                'first_time_transaction' => false,
                'minimum_amount' => null,
                'minimum_amount_currency' => null,
            ],
            'times_redeemed' => 0,
        ];

        return array_merge_recursive($promotionCode, $overrides);
    }

    /**
     * Generates a realistic Stripe Pricing Plan mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generatePricingPlan(array $overrides = []): array {
        $planId = 'plan_' . Str::random(24);

        $plan = [
            'id' => $planId,
            'object' => 'plan',
            'active' => true,
            'amount' => $overrides['amount'] ?? rand(500, 50000),
            'currency' => $overrides['currency'] ?? 'usd',
            'interval' => $overrides['interval'] ?? 'month',
            'interval_count' => $overrides['interval_count'] ?? 1,
            'nickname' => $overrides['nickname'] ?? 'Pro Plan',
            'product' => 'prod_' . Str::random(24),
            'trial_period_days' => $overrides['trial_period_days'] ?? null,
            'usage_type' => $overrides['usage_type'] ?? 'licensed',
            'billing_scheme' => $overrides['billing_scheme'] ?? 'per_unit',
            'created' => now()->timestamp,
            'livemode' => false,
            'metadata' => [],
            'tiers_mode' => null,
            'transform_quantity' => null,
        ];

        return array_merge_recursive($plan, $overrides);
    }

    /**
     * Generates a realistic Stripe Customer Portal Session mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generateCustomerPortalSession(array $overrides = []): array {
        $sessionId = 'cs_' . Str::random(24);
        $customerId = 'cus_' . Str::random(14);

        $session = [
            'id' => $sessionId,
            'object' => 'customer_portal_session',
            'configuration' => 'bpc_' . Str::random(24),
            'customer' => $customerId,
            'flow' => 'login_and_access_portal',
            'locale' => 'en',
            'on_behalf_of' => null,
            'return_url' => 'https://your-app.com/dashboard',
            'url' => 'https://billing.stripe.com/session/' . Str::random(20),
            'created' => now()->timestamp,
            'expires_at' => now()->addHours(1)->timestamp,
            'livemode' => false,
            'metadata' => [],
        ];

        return array_merge_recursive($session, $overrides);
    }

    /**
     * Generates a realistic Stripe Webhook Event mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generateWebhookEvent(array $overrides = []): array {
        $eventId = 'evt_' . Str::random(24);

        $event = [
            'id' => $eventId,
            'object' => 'event',
            'api_version' => '2022-11-15',
            'created' => now()->timestamp,
            'data' => [
                'object' => $overrides['data_object'] ?? [
                        'id' => 'sub_' . Str::random(24),
                        'object' => 'subscription',
                        'status' => 'active',
                        'customer' => 'cus_' . Str::random(14),
                    ],
            ],
            'livemode' => false,
            'pending_webhooks' => 0,
            'request' => [
                'id' => 'req_' . Str::random(24),
                'idempotency_key' => null,
            ],
            'type' => $overrides['type'] ?? 'customer.subscription.created',
        ];

        return array_merge_recursive($event, $overrides);
    }

    /**
     * Generates a realistic Stripe Revenue Recovery Report mock object.
     *
     * @param array $overrides Custom values to override defaults
     * @return array
     */
    public function generateRevenueRecoveryReport(array $overrides = []): array {
        $reportId = 'rep_' . Str::random(24);

        $report = [
            'id' => $reportId,
            'object' => 'report',
            'title' => 'Revenue Recovery Analytics',
            'description' => 'Insights into recovered revenue using Smart Retries.',
            'start_date' => now()->subMonths(3)->toDateString(),
            'end_date' => now()->toDateString(),
            'metrics' => [
                'total_recovered_amount' => $overrides['total_recovered_amount'] ?? rand(1000, 50000),
                'total_attempts' => $overrides['total_attempts'] ?? rand(100, 500),
                'success_rate' => $overrides['success_rate'] ?? rand(70, 95) / 100,
            ],
            'created' => now()->timestamp,
            'livemode' => false,
            'metadata' => [],
        ];

        return array_merge_recursive($report, $overrides);
    }

}
