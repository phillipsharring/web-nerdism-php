# Configuration and the Environment

Lesson: https://phillipharrington.com/php/configuration-and-the-environment

`.env.example` is the list of settings the app needs, and it's in git. Your
own `.env`, with the real values, never is (see `.gitignore`). Make yours:

```
cp .env.example .env
```

Give `MAIL_API_KEY` a value in `.env`, then start the server with it:

```
docker run --rm -it -p 8000:8000 --env-file .env -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000. Then try it without `--env-file .env` (it refuses
to start work), and with `-e APP_ENV=production` added (it overrides the file).
