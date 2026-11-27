# Exporting Data

Project: https://phillipharrington.com/php/projects/exporting-data

A CSV download of the contacts table, behind HTTP Basic authentication (user
`editor`, password `correct horse battery staple`): streamed row by row,
quoted properly, UTF-8 with a byte order mark for Excel, and every text cell
made safe from formulas. `public/naive-export.php` is the broken version from
Break It; never put it on a server. Start it:

```
docker compose up
```

Then download:

```
curl -u 'editor:correct horse battery staple' -OJ http://localhost:8000/export.php
curl -u 'editor:correct horse battery staple' -OJ 'http://localhost:8000/export.php?since=2026-11-10'
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
