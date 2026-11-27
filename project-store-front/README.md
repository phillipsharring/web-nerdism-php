# Store Front

Project: https://phillipharrington.com/php/projects/store-front

A product list from MySQL, with categories and sorting, a page per product,
and prices kept as whole cents. Start it:

```
docker compose up
```

Then open http://localhost:8000, or:

```
curl 'http://localhost:8000/?category=mugs&sort=price-high'
curl http://localhost:8000/products/black-mug
```

`Ctrl+C` stops it; `docker compose down -v` removes the database. (`APP_PORT`
changes the port, if 8000 is taken.)
