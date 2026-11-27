# SQL

Lesson: https://phillipharrington.com/php/sql

MySQL 8.4 with a table of books (made by `init.sql`), and a PHP container that
can reach it. Start the database and wait until it's ready:

```
docker compose up -d --wait db
```

Type SQL straight in (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

Or ask from PHP:

```
docker compose run --rm php php books.php 1900
```

When you're done, stop it all and throw the data away:

```
docker compose down -v
```
