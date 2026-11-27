# Transactions

Lesson: https://phillipharrington.com/php/transactions

Two bank accounts, and two ways to move money between them. Start the
database:

```
docker compose up -d --wait db
```

The naive way, to an account that doesn't exist, then check the total:

```
docker compose run --rm php php transfer-naive.php 1 99 2500
docker compose run --rm php php balances.php
```

The same with a transaction, then a good transfer:

```
docker compose run --rm php php transfer.php 1 99 2500
docker compose run --rm php php transfer.php 2 1 1000
docker compose run --rm php php balances.php
```

What another connection sees while a transaction is open:

```
docker compose run --rm php php isolation.php
```

When you're done:

```
docker compose down -v
```
