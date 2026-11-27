# Declaring Symbols vs. Causing Changes

Lesson: https://phillipharrington.com/php/declaring-vs-causing

`before/helpers.php` declares a function and does other things too. In
`after/`, `text.php` only declares and `page.php` only does.

Run the same job both ways:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before/slugs.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after/slugs.php
```

The first prints a nav bar it never asked for and writes `before/visits.log`.
The second prints only the slugs. Then run the page:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after/page.php
```
