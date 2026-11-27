# Relating Tables: Keys and Foreign Keys

Lesson: https://phillipharrington.com/php/relating-tables

`init.sql` makes authors, books (each with an `author_id`), tags, and a
`book_tags` table that pairs books with tags. Start the database:

```
docker compose up -d --wait db
```

Read across the tables, and try to break the links (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

Add a book from PHP, for an author who exists and one who doesn't:

```
docker compose run --rm php php add-book.php 2 'Dawn' 1987
docker compose run --rm php php add-book.php 99 'Nobody' 2000
```

When you're done:

```
docker compose down -v
```
