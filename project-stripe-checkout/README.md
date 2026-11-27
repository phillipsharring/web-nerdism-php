# Taking a Payment with Stripe Checkout

Project: https://phillipharrington.com/php/projects/stripe-checkout

The checkout, plus payment on Stripe's own page: the card never reaches this
server. A signed webhook from Stripe, not the visitor's return, marks the
order paid. Test mode only: the example refuses to run with a live key.

1. Get your test-mode secret key from the Stripe Dashboard (it starts with
   `sk_test_`), copy `.env.example` to `.env`, and put it there.
2. Install and start, with the Stripe CLI forwarding webhooks:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose --profile stripe up
```

3. The CLI prints a webhook signing secret (`whsec_...`). Put it in `.env` as
   `STRIPE_WEBHOOK_SECRET`, then restart the app:
   `docker compose up -d app`.

Shop at http://localhost:8000, check out, and pay with Stripe's test card
4242 4242 4242 4242, any future date, any CVC. (4000 0000 0000 0002 is
declined; 4000 0025 0000 3155 asks for authentication.)

`docker compose --profile stripe down -v` stops everything and removes the
database.
