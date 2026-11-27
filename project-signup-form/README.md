# Project: Signup Form

Project: https://phillipharrington.com/php/projects/signup-form

A signup form on MySQL. `public/naive.php` stores `md5()` of the password;
`public/index.php` uses `password_hash()`, checks length and a list of common
passwords, and lets the UNIQUE index catch duplicate addresses.

```
docker compose up --build
```

Sign up at http://localhost:8000 (naive: http://localhost:8000/naive.php),
then look at what's stored:

```
docker compose exec db mysql -uapp -papp-password course -e 'SELECT email, password_hash FROM users'
docker compose exec php php crack.php 482c811da5d5b4bc6d497ffa98491e38
docker compose exec php php speed.php
```

`docker compose down -v` stops it and deletes the database.
