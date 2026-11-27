# Project: Useful Includes

Project: https://phillipharrington.com/php/projects/useful-includes

A small site whose pages share one bootstrap file (settings, helper functions,
time zone), one header and one footer. Only `public/` is served; the shared
files live outside it.

```
docker run --rm -it -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000 -t public
```

Open http://localhost:8000, click between Home and About, and use the View
source link in the footer.
