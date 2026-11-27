# Rate Limiting

Lesson: https://phillipharrington.com/php/rate-limiting

MySQL 8.4 with one member (alice@example.com, whose password is in
`words.txt`, on purpose) and a table of failed logins, plus PHP's built-in
server. Start both, and wait until they're ready:

```
docker compose up -d --build --wait
```

Guess Alice's password, against the naive page, then the fixed one:

```
sh guess.sh http://localhost:8000/naive.php
sh guess.sh http://localhost:8000/
```

The fixed page allows 10 failures per email and 50 per IP address in 15
minutes, then answers 429. `docker compose down -v` stops everything and
deletes the database, failures and all. (`WEB_PORT` changes the port.)
