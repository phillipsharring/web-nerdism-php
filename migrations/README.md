# Migrations

Lesson: https://phillipharrington.com/php/migrations

`migrate.php` applies every file in `migrations/` that this database hasn't
had yet, oldest first, and records each one in a `migrations` table. They only
go forward: to undo a change, you add a new migration. Start the database:

```
docker compose up -d --wait db
```

Bring it up to date, twice:

```
docker compose run --rm php php migrate.php
docker compose run --rm php php migrate.php
```

Then undo the phone column, the forward way, by adding the migration from
`later/`, and migrate again:

```
cp later/2026_11_27_1000_drop_phone_from_users.sql migrations/
docker compose run --rm php php migrate.php
```

Look at the result (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

When you're done, take the copied migration back out, and throw the data away:

```
rm migrations/2026_11_27_1000_drop_phone_from_users.sql
docker compose down -v
```
