# Request and Response

Lesson: https://phillipharrington.com/php/request-and-response

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Then, from a second terminal:

```
curl http://localhost:8000/hello
curl -d 'name=Alice&comment=Hi' http://localhost:8000/comments
curl -i http://localhost:8000/teapot
```
