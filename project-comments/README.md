# Comments

Project: https://phillipharrington.com/php/projects/comments

Comments on a post, with escaping on the way out, and spam checks on the way
in: a honeypot field, a signed form token that must be at least 3 seconds old,
a limit of 5 comments per address per 10 minutes, and comments with links held
for a person to read. `public/naive.php` is the broken version from Break It;
never put it on a server. Start it:

```
docker compose up
```

Then open http://localhost:8000. Break the naive one:

```
curl --data-urlencode 'name=Mallory' --data-urlencode 'body=<script>alert(document.cookie)</script>' http://localhost:8000/naive.php
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
