# Output Buffering

Lesson: https://phillipharrington.com/php/output-buffering

Serve `public/`:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Then, from a second terminal:

```
curl -i http://localhost:8000/too-late.php     # the header is lost, with a warning
curl -i http://localhost:8000/buffered.php     # the header makes it
curl -i 'http://localhost:8000/?name=Ada'      # a template rendered into a string
```
