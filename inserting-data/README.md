# Inserting Data

Lesson: https://phillipharrington.com/php/inserting-data

A table of newsletter subscribers, one per email address. Start the database:

```
docker compose up -d --wait db
```

Subscribe some people, and subscribe one twice:

```
docker compose run --rm php php subscribe.php alice@example.com Alice
docker compose run --rm php php subscribe.php bo@example.com Bo
docker compose run --rm php php subscribe.php alice@example.com 'Alice Rivera'
docker compose run --rm php php subscribe.php alice@example.com 'Alice Rivera'
docker compose run --rm php php subscribe.php cy@example.com Cy
```

Look at the ids (`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

When you're done:

```
docker compose down -v
```
