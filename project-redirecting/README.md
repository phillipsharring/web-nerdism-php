# Project: Redirecting

Project: https://phillipharrington.com/php/projects/redirecting

A moved page with a 301, and a settings form that sends you back where you
came from with a 303. `public/naive-settings.php` sends you anywhere the
request says; `public/settings.php` only to a path on this site.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

```
curl -i http://localhost:8000/about-us.php
curl -i -d 'theme=dark&back=https://evil.example/login' http://localhost:8000/naive-settings.php
curl -i -d 'theme=dark&back=https://evil.example/login' http://localhost:8000/settings.php
```
