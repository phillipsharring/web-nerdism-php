<?php
declare(strict_types=1);

namespace Shop;

use RuntimeException;

/**
 * Checks that a webhook really came from Stripe. Stripe signs each one with a
 * secret only you and Stripe know: an HMAC-SHA256 of "timestamp.body", sent in
 * the Stripe-Signature header as t=...,v1=.... (The stripe-php library's
 * Webhook::constructEvent() does the same check.)
 */
final class StripeSignature
{
    public function __construct(private string $secret, private int $toleranceSeconds = 300)
    {
    }

    /** @return array<string, mixed> the event, decoded, if the signature is good */
    public function verify(string $payload, string $header): array
    {
        if ($this->secret === '') {
            throw new RuntimeException('No webhook secret set.');
        }

        $timestamp = null;
        $signatures = [];
        foreach (explode(',', $header) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, '');
            if ($key === 't' && ctype_digit($value)) {
                $timestamp = (int) $value;
            } elseif ($key === 'v1') {
                $signatures[] = $value;
            }
        }
        if ($timestamp === null || $signatures === []) {
            throw new RuntimeException('No signature.');
        }

        $expected = hash_hmac('sha256', "$timestamp.$payload", $this->secret);
        $matches = array_filter($signatures, fn (string $s) => hash_equals($expected, $s));
        if ($matches === []) {
            throw new RuntimeException('Bad signature.');
        }
        // An old, real signature is still refused, so a captured webhook can't be replayed later.
        if (abs(time() - $timestamp) > $this->toleranceSeconds) {
            throw new RuntimeException('Signature too old.');
        }

        return json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    }
}
