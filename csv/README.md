# CSV

Lesson: https://phillipharrington.com/php/csv

Reading a CSV, row by row, and checking every row:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php import.php shirts.csv
```

Writing one, as a download. Start the server:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Then `curl -i http://localhost:8000/export.php`, or open it in a browser to
download it.
