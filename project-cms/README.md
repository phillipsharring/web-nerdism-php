# Content Management System

Project: https://phillipharrington.com/php/projects/cms

Pages in MySQL, edited at `/admin.php` (HTTP Basic authentication: user
`editor`, password `correct horse battery staple`), each with an optional
image upload. `public/upload-naive.php` is the broken uploader from Break It;
never put it on a server. Start it:

```
docker compose up
```

Then open http://localhost:8000 and http://localhost:8000/admin.php.

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.) Uploads land in `storage/uploads/` (and
the naive one's in `public/uploads/`); delete them when you're done.
