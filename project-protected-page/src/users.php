<?php
declare(strict_types=1);

// One member, for now. The password is "correct horse battery staple", and
// this is its hash, made with:
//   php -r 'echo password_hash("correct horse battery staple", PASSWORD_DEFAULT);'
// A demo account for your own computer. Never ship one. The Signup Form and
// Login Form projects move members into a database.
return [
    'alice@example.com' => '$2y$12$noC7DVSxqpYFmE7NBKN48O9sDfYQ9/T8LfV0RjNOgF34NYMDqvv1q',
];
