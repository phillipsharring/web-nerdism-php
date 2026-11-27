<?php
declare(strict_types=1);

namespace Shop;

use Stripe\StripeClient;

/** Starts a payment on Stripe Checkout: Stripe's page, Stripe's servers, Stripe's card form. */
final class Payments
{
    public function __construct(private StripeClient $stripe, private string $siteUrl)
    {
    }

    /**
     * Creates a Checkout Session for the order and returns [its id, the URL
     * of Stripe's payment page]. Every amount comes from the order, which
     * came from our database.
     *
     * @param array<string, mixed> $order from Orders::find()
     * @return array{0: string, 1: string}
     */
    public function startCheckout(array $order): array
    {
        $lines = [];
        foreach ($order['items'] as $item) {
            $lines[] = [
                'price_data' => [
                    'currency' => $order['currency'],
                    'unit_amount' => (int) $item['unit_cents'],
                    'product_data' => ['name' => $item['name']],
                ],
                'quantity' => (int) $item['quantity'],
            ];
        }

        $back = "{$this->siteUrl}/order.php?id={$order['public_id']}";
        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lines,
            'customer_email' => $order['email'],
            'client_reference_id' => $order['public_id'], // how the webhook finds the order
            'success_url' => $back . '&paid={CHECKOUT_SESSION_ID}',
            'cancel_url' => $back,
        ]);

        return [$session->id, $session->url];
    }
}
