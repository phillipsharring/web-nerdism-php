# Shopping Cart

Project: https://phillipharrington.com/php/projects/shopping-cart

A cart in the session: add, change and remove products, with prices always
from the database and never from the form. `Cart` has PHPUnit tests. Install,
then start it:

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose up
```

Then open http://localhost:8000. Run the tests:

```
docker compose run --rm app vendor/bin/phpunit tests
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
