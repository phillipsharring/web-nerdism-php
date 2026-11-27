# Don't Touch Cards

Lesson: https://phillipharrington.com/php/dont-touch-cards

A mug shop that takes payment through Stripe Checkout, and a naive one with a
card form of its own. Install the Stripe library, then start the shop and
stripe-mock (a pretend Stripe API, so you don't need an account):

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose up
```

Open http://localhost:8000 and press Buy it: you're sent to a Stripe
address. (stripe-mock's address is made up and won't open. Run with your own
Stripe test key, `STRIPE_SECRET_KEY=sk_test_... STRIPE_API_BASE= docker compose up`,
and it's a real Checkout page.)

Then the naive one, with Stripe's test card number:

```
curl -d 'card_number=4242424242424242&card_expiry=12/34&card_cvc=123' http://localhost:8000/naive.php
docker compose logs shop
```

The card number is in your log.
