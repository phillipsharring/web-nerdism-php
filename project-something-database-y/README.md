# Project: Something Database-y

Project: https://phillipharrington.com/php/projects/something-database-y

A book search on MySQL. `public/naive.php` pastes the query string into the
SQL; `public/index.php` uses a prepared statement, escapes LIKE wildcards and
sorts from an allow list.

```
docker compose up --build
```

Open http://localhost:8000 (naive: http://localhost:8000/naive.php). Then,
from a second terminal:

```
curl -G --data-urlencode "q=' UNION SELECT email, password_hash, 0 FROM users -- " http://localhost:8000/naive.php
curl -G --data-urlencode "q=' UNION SELECT email, password_hash, 0 FROM users -- " http://localhost:8000/
```

`docker compose down -v` stops it and deletes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
