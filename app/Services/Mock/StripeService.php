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
    public static function generateTransaction(array $overrides = []): array {
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

}
