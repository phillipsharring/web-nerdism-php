# XSS and Escaping Output

Lesson: https://phillipharrington.com/php/xss

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

`naive.php` puts input into the page as it came. Open these in a browser:

```
http://localhost:8000/naive.php?name=<script>alert(document.cookie)</script>
http://localhost:8000/naive.php?name=" autofocus onfocus="this.onfocus=null;alert(document.cookie)
http://localhost:8000/naive.php?website=javascript:alert(document.cookie)
```

Then the same ones against `index.php`, the fixed version
(`http://localhost:8000/?name=...`). Every value is escaped, and the link
has to start with `http://` or `https://`.
