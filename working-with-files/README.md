# Working with Files

Lesson: https://phillipharrington.com/php/working-with-files

Add some notes, then list them. They're saved in `data/notes.txt`.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php notes.php add "Buy milk"
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php notes.php add "Call the café about the order"
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php notes.php list
```

Search a file in `data/` line by line, then try to reach outside it:

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php search.php notes.txt milk
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php search.php ../notes.php php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php search.php /etc/passwd root
```

`data/` is in `.gitignore`: notes are yours, not the repo's.
