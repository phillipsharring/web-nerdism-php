# Factories

Lesson: https://phillipharrington.com/php/factories

Which mailer the app gets depends on the settings. `before.php` chooses inline
and guesses when the settings are wrong. `after.php` asks `MailerFactory`,
which refuses.

```
docker run --rm -e MAILER=log -v "$PWD":/app -w /app php:8.4-cli php after.php
docker run --rm -e MAILER=smtp -e SMTP_HOST=smtp.example.com -e SMTP_PORT=587 -v "$PWD":/app -w /app php:8.4-cli php after.php
```

A typo, both ways:

```
docker run --rm -e MAILER=smpt -v "$PWD":/app -w /app php:8.4-cli php before.php
docker run --rm -e MAILER=smpt -v "$PWD":/app -w /app php:8.4-cli php after.php
```
