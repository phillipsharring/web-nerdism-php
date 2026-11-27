# Sending a File

Lesson: https://phillipharrington.com/php/sending-a-file

Make the big file first (about 40 MB of made-up rows, not in git):

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php make-big-file.php
```

Serve `public/`, with PHP's memory capped at 16 MB to make the point:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -d memory_limit=16M -S 0.0.0.0:8000 -t public
```

Then, from a second terminal:

```
curl -OJ 'http://localhost:8000/download.php?file=report'   # saved as sales-report.csv
curl -I 'http://localhost:8000/download.php?file=menu'      # a UTF-8 file name
curl -o everyone.csv 'http://localhost:8000/download.php?file=big'   # all 40 MB, streamed
curl 'http://localhost:8000/all-at-once.php' | tail -c 300  # runs out of memory
curl -i 'http://localhost:8000/download.php?file=../.gitignore'      # 404
```
