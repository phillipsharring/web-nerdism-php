# Cookies

Lesson: https://phillipharrington.com/php/cookies

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Open http://localhost:8000 and reload. Then lie to it:

```
curl -b 'visits=41' http://localhost:8000
```
