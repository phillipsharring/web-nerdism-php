<?php
// The Stripe client, set up from the environment. The secret key never goes
// in the code; see Secrets Out of Code.
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$key = getenv('STRIPE_SECRET_KEY');
if (!is_string($key) || $key === '') {
    throw new RuntimeException('STRIPE_SECRET_KEY is not set.');
}

$options = ['api_key' => $key];
if (getenv('STRIPE_API_BASE')) {
    $options['api_base'] = getenv('STRIPE_API_BASE');
}

return new Stripe\StripeClient($options);
