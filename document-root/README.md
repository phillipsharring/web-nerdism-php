# Document Root

Lesson: https://phillipharrington.com/php/document-root

`.env` holds a pretend secret, outside `public/`, the web root. (A real `.env`
never goes in git; this one is a sample for the lesson.)

Serve just `public/`, the right way:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

`curl http://localhost:8000/.env` gets the page, not the secret. Then serve the
whole project, the wrong way (no `-t public`), and the same `curl` hands over
the secret.
