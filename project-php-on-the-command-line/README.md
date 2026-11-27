# PHP on the Command Line

Project: https://phillipharrington.com/php/projects/php-on-the-command-line

`bin/top-words.php` prints the most common words in files, or in whatever is
piped into it. Run it in a container:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php bin/top-words.php --top=5 sample.txt
cat sample.txt | docker run --rm -i -v "$PWD":/app -w /app php:8.4-cli php bin/top-words.php --top=3
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php bin/top-words.php --help
```

Exit codes: 0 when it worked, 1 when a file can't be read, 2 when the
options are wrong.
