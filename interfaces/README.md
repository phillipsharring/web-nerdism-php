# Interfaces

Lesson: https://phillipharrington.com/php/interfaces

`Notifier.php` is the interface. `ScreenNotifier.php` and `MemoryNotifier.php`
implement it, and `remindAll()` takes either:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php remind.php
```

A class with the right method that doesn't say `implements Notifier`:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php wrong.php
```
