# Indexes

Lesson: https://phillipharrington.com/php/indexes

`init.sql` makes 200,000 customers, with no index on `email`. Start the
database (the first start takes a little longer, making the rows):

```
docker compose up -d --wait db
```

Time a lookup by email:

```
docker compose run --rm php php find.php user123456@example.com
```

Then, in the `mysql` command, `EXPLAIN` the query, add the index, and
`EXPLAIN` it again (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

Run `find.php` again to see the difference. When you're done:

```
docker compose down -v
```
