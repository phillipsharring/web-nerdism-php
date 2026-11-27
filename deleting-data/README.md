# Deleting Data

Lesson: https://phillipharrington.com/php/deleting-data

Users, their posts, and comments on the posts. Comments are deleted along
with their post (`ON DELETE CASCADE`); users are only ever marked as deleted.
Start the database:

```
docker compose up -d --wait db
```

Delete a post, and its comments go with it:

```
docker compose run --rm php php delete-post.php 1
```

Close an account, with a soft delete:

```
docker compose run --rm php php close-account.php 2
```

Try deleting things the hard way, inside a transaction you can roll back
(`exit` to leave):

```
docker compose exec db mysql -u app -papp-password course
```

When you're done:

```
docker compose down -v
```
