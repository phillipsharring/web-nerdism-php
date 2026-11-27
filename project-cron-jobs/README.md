# Cron Jobs

Project: https://phillipharrington.com/php/projects/cron-jobs

`bin/prune.php` deletes login attempts older than 30 days, in batches, with a
lock so two runs never overlap. Run it by hand:

```
docker compose run --rm app php bin/prune.php --dry-run
docker compose run --rm app php bin/prune.php
docker compose run --rm app php bin/prune.php --days=1
```

Or let cron run it every minute, using `crontab`, and watch the log:

```
docker compose --profile cron up -d
tail -f var/prune.log
```

`docker compose --profile cron down -v` stops everything and removes the
database.
