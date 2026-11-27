# Project: View Source Link

Project: https://phillipharrington.com/php/projects/view-source-link

A page with a link that shows its own PHP source, highlighted, and nothing but
the files on the allow list.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and follow the link. Then ask for something that
isn't on the list:

```
curl 'http://localhost:8000/source.php?file=../../../etc/passwd'
curl 'http://localhost:8000/source.php?file=/etc/passwd'
```
