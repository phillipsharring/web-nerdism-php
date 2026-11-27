# Selecting Data

Lesson: https://phillipharrington.com/php/selecting-data

A reading list: four authors (one with no books yet) and their books, some
not rated yet (`NULL`). Start the database:

```
docker compose up -d --wait db
```

Joins, `NULL` and counting, in the `mysql` command (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

A page at a time, with `LIMIT`:

```
docker compose run --rm php php list.php 1
docker compose run --rm php php list.php 2
```

The N+1 query, and the one query that replaces it:

```
docker compose run --rm php php n-plus-one.php
```

When you're done:

```
docker compose down -v
```
