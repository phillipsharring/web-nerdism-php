# File Uploads

Lesson: https://phillipharrington.com/php/file-uploads

Serve `public/`, the web root:

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

`attack/` holds the files to upload: a PHP file that only proves it ran, a real PNG, and a GIF with
PHP inside it. Against the naive page:

```
curl -F 'photo=@attack/proof.php' http://localhost:8000/naive.php
curl http://localhost:8000/uploads/proof.php
```

Against the fixed page (`index.php`), the PHP file is refused, the PNG is saved
in `storage/` under a random name, and the GIF is stored and served as an
image, never run:

```
curl -F 'photo=@attack/proof.php' http://localhost:8000/
curl -F 'photo=@attack/photo.png' http://localhost:8000/
curl -F 'photo=@attack/polyglot.gif' http://localhost:8000/
```

Uploaded files are ignored by git.
