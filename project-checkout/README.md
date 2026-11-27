# Checkout

Project: https://phillipharrington.com/php/projects/checkout

The shopping cart, plus a checkout that turns it into an order: prices and
names copied from the database, stock checked under a lock, one order per
checkout however many times the button is pressed, and no card fields at all.
`public/naive-checkout.php` is the broken version from Break It; never put it
on a server. Install, then start it:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose up
```

Shop at http://localhost:8000, then check out.

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.) Delete `logs/app.log` when you're done
with Break It.
