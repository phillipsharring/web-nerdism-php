# Exceptions and Try/Catch

Lesson: https://phillipharrington.com/php/exceptions

Serve `public/`:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Then, from a second terminal:

```
curl 'http://localhost:8000/?cents=1000&people=3'      # split the bill
curl -i 'http://localhost:8000/?cents=1000&people=0'   # 400, caught
curl -i 'http://localhost:8000/?cents=lots&people=3'   # 400, caught
curl -i http://localhost:8000/oops.php                 # 500, logged, nothing caught it
```

The stack trace for the last one shows up in the server's terminal, not the page.
