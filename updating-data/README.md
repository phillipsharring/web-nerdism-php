# Updating Data

Lesson: https://phillipharrington.com/php/updating-data

Three products, with prices and stock. Start the database:

```
docker compose up -d --wait db
```

Change a price, twice, and for a product that isn't there:

```
docker compose run --rm php php set-price.php 2 1800
docker compose run --rm php php set-price.php 2 1800
docker compose run --rm php php set-price.php 99 100
```

Sell the last T-shirt, then try to sell another:

```
docker compose run --rm php php sell.php 2
docker compose run --rm php php sell.php 2
```

The UPDATE without a WHERE, with the safety on, and inside a transaction you
can roll back (`exit` to leave):

```
docker compose exec db mysql --safe-updates -u app -papp-password course
docker compose exec db mysql -u app -papp-password course
```

When you're done:

```
docker compose down -v
```
