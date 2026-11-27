# How PHP Actually Runs: nginx and PHP-FPM

Lesson: https://phillipharrington.com/php/how-php-runs

nginx serving `public/`, handing `.php` files to PHP-FPM. Start both:

```
docker compose up
```

Then, from a second terminal:

```
curl http://localhost:8000/                 # PHP's answer
curl -I http://localhost:8000/              # X-Powered-By: PHP ran
curl -I http://localhost:8000/style.css     # no X-Powered-By: nginx sent it alone
docker compose top php                      # the master and its workers
```

Ctrl+C stops both. (`WEB_PORT` changes the port, if 8000 is taken.)
