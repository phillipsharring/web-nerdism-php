# Blog

Project: https://phillipharrington.com/php/projects/blog

Posts in MySQL: a paged list, one page per post, drafts and scheduled posts
kept out of sight, and an Atom feed. Start it:

```
docker compose up
```

Then open http://localhost:8000, or:

```
curl 'http://localhost:8000/?page=2'
curl http://localhost:8000/posts/hello
curl http://localhost:8000/feed
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
