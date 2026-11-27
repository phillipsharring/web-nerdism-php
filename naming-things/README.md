# Naming Things

Lesson: https://phillipharrington.com/php/naming-things

`before.php` is one vague `PostManager` that does everything. `after.php` is
the same behavior split into pieces named for what each one is: a record, a
policy, validation, a table with an invariant, an action, an event and a
listener. Both print exactly the same thing.

```
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php before.php
docker run --rm -v "$PWD":/app -w /app php:8.4-cli php after.php
```
