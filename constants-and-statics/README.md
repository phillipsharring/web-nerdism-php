# Constants, Static Variables and Functions

Lesson: https://phillipharrington.com/php/constants-and-statics

Constants, a static variable, and a static property and method:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php statics.php
```

Why a static call makes code hard to check:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php why-not.php
```

A static variable only lasts one request:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
curl http://localhost:8000/request.php
curl http://localhost:8000/request.php
```
