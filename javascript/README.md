# JavaScript

Lesson: https://phillipharrington.com/php/javascript

PHP hands data to JavaScript, safely, in a JSON script tag. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000 and pick a size. Then try to break out of the script tag:

```
curl 'http://localhost:8000/?name=%3C/script%3E%3Cscript%3Ealert(1)%3C/script%3E'
```
