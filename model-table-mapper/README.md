# Model, Table, Mapper

Lesson: https://phillipharrington.com/php/model-table-mapper

`src/Note.php` is the model, `src/NotesTable.php` the table, and
`src/NoteMapper.php` the mapper between them. `demo.php` uses all three against
MySQL.

```
docker run --rm -v "$PWD":/app -w /app composer:2 composer install
docker compose run --rm php
docker compose down -v
```
