# Query Strings

Lesson: https://phillipharrington.com/php/query-strings

Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Then open http://localhost:8000/?q=red+shoes&page=2&tag[]=sale&tag[]=new, and
then some garbage: http://localhost:8000/?q[]=x&page=banana&tag=solo
