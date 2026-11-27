# Project: Protected Page

Project: https://phillipharrington.com/php/projects/protected-page

A members' page behind a sign-in. One member, `alice@example.com`, password
`correct horse battery staple` (a demo account for your own computer). The
guard is `require_login()` in `src/auth.php`.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000/members.php. You're sent to sign in, then back.
`public/forgot-exit.php` is a broken guard, for comparison:

```
curl -i http://localhost:8000/members.php
curl -i http://localhost:8000/forgot-exit.php
```
