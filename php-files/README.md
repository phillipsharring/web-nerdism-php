# PHP Files, Escaping into PHP, and phpinfo()

Lesson: https://phillipharrington.com/php/php-files

Start the server, with a pretend secret in the environment:

```
docker run --rm -it -p 8000:8000 -e API_KEY=pretend-secret -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Open http://localhost:8000 for the page, and view its source: no PHP in it.
Then http://localhost:8000/info.php for phpinfo(), and look for the secret:

```
curl -s http://localhost:8000/info.php | grep pretend-secret
```

The same information, on the command line:

```
docker run --rm php:8.4-cli php -v
docker run --rm php:8.4-cli php -m
docker run --rm php:8.4-cli php --ini
```
