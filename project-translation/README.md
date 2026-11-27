# Translation

Project: https://phillipharrington.com/php/projects/translation

One page in four languages (English, French, Polish, Japanese), picked from
the browser's Accept-Language header, a `?lang=` link, or a cookie. Product
names come from MySQL, in UTF-8 the whole way. Start it:

```
docker compose up
```

Then open http://localhost:8000, or ask for a language:

```
curl -H 'Accept-Language: fr-CA,fr;q=0.9,en;q=0.5' http://localhost:8000
curl -H 'Accept-Language: ja' http://localhost:8000
```

See the plural rules at work:

```
docker compose run --rm app php plurals.php
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
