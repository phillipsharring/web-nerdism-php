# Log Viewer

Project: https://phillipharrington.com/php/projects/log-viewer

A page that shows the logs in `logs/`, behind HTTP Basic authentication (user
`editor`, password `correct horse battery staple`). `public/naive.php` is the
broken version from Break It; never put it on a server. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Break the naive one, then try the same on the real one:

```
curl 'http://localhost:8000/naive.php?file=../.env'
curl -u 'editor:correct horse battery staple' 'http://localhost:8000/?file=../.env'
```
