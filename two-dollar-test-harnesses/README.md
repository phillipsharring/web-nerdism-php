# Two-Dollar Test Harnesses

Lesson: https://phillipharrington.com/php/two-dollar-test-harnesses

`src/verification.php` makes and checks an email verification link. The
harness loads it and calls it, with no framework, database or mail server:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php harness.php
```

Every line should say `ok`. Break the expiry check in `src/verification.php`
and run it again to watch it say `FAIL`. `echo $?` after it shows the exit code:
0 when everything passed, 1 when anything failed.
