# Character Sets and Collation

Lesson: https://phillipharrington.com/php/character-sets-and-collation

`init.sql` makes one table in MySQL's `utf8` (really three-byte `utf8mb3`),
one in `utf8mb4`, a table of unique usernames, and some words to sort.
Start the database:

```
docker compose up -d --wait db
```

Save an emoji to both tables:

```
docker compose run --rm php php save.php 'Hello 🐘'
```

Save "café" over a connection that says latin1:

```
docker compose run --rm php php wrong-charset.php
```

Compare and sort text in the `mysql` command (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

When you're done:

```
docker compose down -v
```
