# Working with a Database: PDO and Prepared Statements

Lesson: https://phillipharrington.com/php/pdo

MySQL and PHP, together. The first start builds the PHP image and loads
`init.sql`, so give it a minute:

```
docker compose up --build
```

Open http://localhost:8000, search, and add a book. Then, from a second
terminal, search the way an attacker would:

```
curl --get --data-urlencode "q=' OR '1'='1" http://localhost:8000/
```

Ctrl+C stops both. `docker compose down -v` also deletes the database, so the
next start loads `init.sql` fresh. (`PHP_PORT` changes the port, if 8000 is taken.)
