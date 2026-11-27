# Affiliate Program

Project: https://phillipharrington.com/php/projects/affiliate-program

Affiliate links (`/?ref=ALICE10`) that are checked, counted, and remembered
in a cookie for 30 days, and commissions worked out in whole cents when an
order comes in. Start it:

```
docker compose up
```

Then open http://localhost:8000/?ref=ALICE10 and buy the widget. See who's
owed what:

```
docker compose exec app php bin/report.php
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
