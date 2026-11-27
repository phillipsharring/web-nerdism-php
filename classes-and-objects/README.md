# Classes and Objects

Lesson: https://phillipharrington.com/php/classes-and-objects

`Cart.php` is the class. Use it:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php shop.php
```

Then try to reach inside one from outside:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php peek.php
```
