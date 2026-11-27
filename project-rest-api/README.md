# REST API

Project: https://phillipharrington.com/php/projects/rest-api

A small JSON API for tasks: bearer tokens (stored as hashes), each caller
sees only their own tasks, and every answer has the status code that says
what happened. `public/naive.php` is the broken version from Break It; never
put it on a server. Start it:

```
docker compose up
```

Then, with one of the example tokens from `init.sql`:

```
curl -i http://localhost:8000/api/tasks
curl -i -H 'Authorization: Bearer wnp_ann_3f1c9a7e5b2d4f60' http://localhost:8000/api/tasks
curl -i -H 'Authorization: Bearer wnp_ann_3f1c9a7e5b2d4f60' -H 'Content-Type: application/json' \
     -d '{"title": "Try the API"}' http://localhost:8000/api/tasks
```

Make a new token:

```
docker compose exec app php bin/make-token.php ann@example.com "My script" --write
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
