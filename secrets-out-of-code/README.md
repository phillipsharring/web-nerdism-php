# Secrets Out of Code

Lesson: https://phillipharrington.com/php/secrets-out-of-code

`naive-config.php` has a (pretend) key in the code. `config.php` reads its
secrets from the environment instead, and stops if one is missing. Try it
with nothing set:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php app.php
```

Then copy `.env.example` to `.env`, fill in pretend values, and pass it in:

```
cp .env.example .env
docker run --rm --env-file .env -v "$PWD":/app -w /app php:8.4-cli php app.php
```

`.env` is in `.gitignore`. The lesson's Break It shows why that matters.
