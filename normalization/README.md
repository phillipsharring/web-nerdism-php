# Normalization

Lesson: https://phillipharrington.com/php/normalization

`init.sql` makes the same orders twice: flat, in `orders_flat`, and
normalized, in `customers`, `products` and `orders`. Start the database:

```
docker compose up -d --wait db
```

Change an email both ways, and compare (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

Place an order, which keeps the copied `order_count` up to date:

```
docker compose run --rm php php place-order.php 2 1
```

When you're done:

```
docker compose down -v
```
