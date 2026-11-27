# DRY

Lesson: https://phillipharrington.com/php/dry

Three pages work out the same order total. In `before/`, each has its own copy
of the shipping rule, and one copy got updated. In `after/`, the rule lives in
`pricing.php` and the pages ask it.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli sh -c 'php before/cart.php; php before/email.php; php before/invoice.php'
docker run --rm -v "$PWD":/app -w /app php:8.4-cli sh -c 'php after/cart.php; php after/email.php; php after/invoice.php'
```
