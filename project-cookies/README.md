# Project: Cookies

Project: https://phillipharrington.com/php/projects/cookies

A page that remembers your name in a cookie. `public/naive.php` is the naive
version, with no cookie flags and the cookie printed raw; `public/index.php`
is the fixed one, with every cookie set through `src/cookies.php`.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Break the naive one:

```
curl -si -d 'name=Alice' http://localhost:8000/naive.php
curl -b 'name=<script>alert(document.cookie)</script>' http://localhost:8000/naive.php
```

Then the same against the fixed one:

```
curl -si -d 'name=Alice' http://localhost:8000/
curl -b 'name=<script>alert(document.cookie)</script>' http://localhost:8000/
```
