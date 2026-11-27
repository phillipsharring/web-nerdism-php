# Passwords: password_hash() and Nothing Else

Lesson: https://phillipharrington.com/php/passwords

Every command runs in a container, from this folder. The naive way, md5,
then five seconds of guessing against the leak:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php naive.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php crack.php leaked.txt
```

The right way, `password_hash()`, then the same five seconds:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php fixed.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php crack.php leaked-fixed.txt
```

`words.txt` is a short list of common passwords. Real attackers' lists have
millions.
