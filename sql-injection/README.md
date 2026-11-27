# SQL Injection

Lesson: https://phillipharrington.com/php/sql-injection

MySQL 8.4 with a table of members (made by `init.sql`), and PHP's built-in
server. Start both, and wait until they're ready:

```
docker compose up -d --build --wait
```

`naive.php` pastes the email into its SQL. Break it:

```
curl --get --data-urlencode "email=' OR '1'='1" http://localhost:8000/naive.php
curl --get --data-urlencode "email=' UNION SELECT CONCAT(email, ' ', password_hash) FROM members -- -" http://localhost:8000/naive.php
```

`index.php` uses a prepared statement. Send it the same thing:

```
curl --get --data-urlencode "email=' OR '1'='1" http://localhost:8000/
```

`docker compose down -v` stops everything and deletes the database.
(`WEB_PORT` changes the port, if 8000 is taken.)
