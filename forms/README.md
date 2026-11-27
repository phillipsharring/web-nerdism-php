# Forms

Lesson: https://phillipharrington.com/php/forms

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Open http://localhost:8000 and order a shirt. Then go around the browser:

```
curl -d 'name=&size=huge' http://localhost:8000
curl --data-urlencode 'name=<b>hi</b>' -d 'size=large' http://localhost:8000
```
