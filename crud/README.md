# CRUD

Lesson: https://phillipharrington.com/php/crud

A notes page that does all four: create, read, update, delete. MySQL and PHP,
with Docker Compose. `src/notes.php` has one function per letter.

```
docker compose up
```

Open http://localhost:8000, add, edit and delete notes. Or with curl:

```
curl -i -d 'body=Buy coffee' http://localhost:8000/notes
curl http://localhost:8000/
curl -i -d 'body=Buy more coffee' http://localhost:8000/notes/3
curl -i -X POST http://localhost:8000/notes/3/delete
```

`docker compose down -v` stops it and throws the data away.
