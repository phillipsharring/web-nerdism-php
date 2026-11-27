# Project: Change Password Form

Project: https://phillipharrington.com/php/projects/change-password-form

A change password form for a signed-in member.
`public/naive-change-password.php` needs only a session and a new password;
`public/change-password.php` needs a CSRF token and the current password
(with failures counted like failed sign-ins), checks the new one, and signs
out every other session. `attacker/index.html`, on port 8001, is someone
else's site with a form aimed at yours.

The seeded member is `alice@example.com`, password
`correct horse battery staple`, for your own computer only.

```
docker compose up --build
```

Sign in at http://localhost:8000/login.php, then visit http://localhost:8001.
`docker compose down -v` stops it and deletes the database.
