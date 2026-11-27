# Creating Tables

Lesson: https://phillipharrington.com/php/creating-tables

`init.sql` creates a `books` table, column by column. Start the database and
wait until it's ready:

```
docker compose up -d --wait db
```

Look at the table, and try to break its rules (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

Add a book from PHP, which checks the format against its own list:

```
docker compose run --rm php php add-book.php 'Frankenstein' paperback 1299
docker compose run --rm php php add-book.php 'Frankenstein' scroll 1299
```

When you're done:

```
docker compose down -v
```
