# CSRF

Lesson: https://phillipharrington.com/php/csrf

Two sites: yours (`site/`) on port 8000, and an attacker's page (`evil/`) on
port 8001. Start both:

```
docker compose up
```

1. Open http://localhost:8000/naive.php. You're "logged in" as alice@example.com.
2. Open http://localhost:8001. It submits a hidden form to the naive page.
   Your email is now attacker@example.net.
3. Open http://localhost:8000/ (the fixed version, with a token), then
   http://localhost:8001/fixed.html, which tries the same thing against it.
   It's refused with a 403.

Ctrl+C stops both. (`SITE_PORT` and `EVIL_PORT` change the ports. The
attacker's pages aim at port 8000, so leave `SITE_PORT` alone for the browser
version.)
