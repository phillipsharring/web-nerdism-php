# HTTP Authentication

Project: https://phillipharrington.com/php/projects/http-authentication

A page behind HTTP Basic authentication. The user name is `editor` and the
password is `correct horse battery staple`. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and the browser asks you to log in. Or with curl:

```
curl -i http://localhost:8000
curl -i -u 'editor:correct horse battery staple' http://localhost:8000
```

Make a hash for a new password (it asks for the password):

```
docker run --rm -it -v "$PWD":/app -w /app php:8.4-cli php hash-password.php
```
