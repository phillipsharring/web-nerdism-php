# Project: Profile Page

Project: https://phillipharrington.com/php/projects/profile-page

A profile page that a signed-in member can edit. `public/naive-profile.php`
accepts any form posted to it; `public/profile.php` requires a CSRF token.
`attacker/index.html` is someone else's site, on port 8001, with a form aimed
at yours.

The seeded member is `alice@example.com`, password
`correct horse battery staple`, for your own computer only.

```
docker compose up --build
```

Sign in at http://localhost:8000/login.php, look at
http://localhost:8000/naive-profile.php, then visit http://localhost:8001 and
look again. `docker compose down -v` stops it and deletes the database.
