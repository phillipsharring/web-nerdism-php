# Observers

Lesson: https://phillipharrington.com/php/observers

`before/Signup.php` does everything that follows a signup itself. In `after/`,
`Signup` announces a `UserSignedUp` event, and the listeners in `listeners/`
(wired up in `app.php`) each do their own part.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before/app.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after/app.php
```
