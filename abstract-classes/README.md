# Abstract Classes

Lesson: https://phillipharrington.com/php/abstract-classes

`Report.php` is the abstract class; `SalesReport.php` and `SignupReport.php`
finish it. Print both:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php reports.php
```

Try to make a `Report` itself, then a report that forgot a method:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php -r 'require "Report.php"; new Report();'
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php broken.php
```
