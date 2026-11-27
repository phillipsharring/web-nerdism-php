# Client/Server

Lesson: https://phillipharrington.com/php/client-server

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Then open http://localhost:8000, or `curl http://localhost:8000`. Stop it with Ctrl+C.
