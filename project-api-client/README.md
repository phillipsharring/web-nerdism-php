# API Client

Project: https://phillipharrington.com/php/projects/api-client

A PHP client for an API (a pretend one, in `api/`), with the token kept out
of the code: it comes from the environment, by way of a `.env` file that git
ignores, and travels only in a header. Timeouts, retries on 429 and 503, and
every answer checked before it's used. `bin/naive.php` is the broken version
from Break It; never write a client like it.

```
cp .env.example .env        # then put a long random token in it
docker compose up -d api
docker compose run --rm client php bin/tasks.php
docker compose run --rm client php bin/tasks.php /flaky
docker compose down
```

(`API_PORT` changes the API's port, if 8000 is taken.)
