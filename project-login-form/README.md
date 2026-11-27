# Project: Login Form

Project: https://phillipharrington.com/php/projects/login-form

A sign-in on MySQL. `public/naive-login.php` allows unlimited guesses and
keeps whatever session id the browser brings; `public/login.php` limits
failures (10 per address, 50 per IP, in 15 minutes), starts sessions in
strict mode, and changes the session id at sign-in.

The seeded member is `alice@example.com`, password
`correct horse battery staple`, for your own computer only.

```
docker compose up --build
```

Then, from a second terminal:

```
docker compose exec php php guess.php http://localhost:8000/naive-login.php alice@example.com
docker compose exec php php guess.php http://localhost:8000/login.php alice@example.com
```

`docker compose down -v` stops it and deletes the database.
