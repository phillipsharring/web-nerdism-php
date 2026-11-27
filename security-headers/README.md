# Security Headers

Lesson: https://phillipharrington.com/php/security-headers

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Compare the headers:

```
curl -I http://localhost:8000/naive.php
curl -I http://localhost:8000/
```

In a browser, open http://localhost:8000/naive.php and http://localhost:8000/:
the inline script runs on the first and is blocked on the second.

Clickjacking: http://localhost:8000/evil.html frames the naive page over a
"Click here to win!" button. http://localhost:8000/evil-fixed.html tries the
same with the fixed page, and the browser refuses to show it.
