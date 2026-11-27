# project-form-validation: https://phillipharrington.com/php/projects/form-validation
docker run --rm -v "$ROOT/project-form-validation":/app -w /app composer:2 composer install --no-interaction --quiet 2>&1
expect "the validator's own tests pass" "$(cli project-form-validation vendor/bin/phpunit --colors=never)" "OK (12 tests"
serve project-form-validation -t public
expect "GET shows the form" "$(curl -s "http://localhost:$PORT/")" '<form method="post" novalidate>'
out="$(curl -s -d 'name=&email=alice@&age=12abc&ticket=free' "http://localhost:$PORT/")"
expect "every bad field gets its own error" "$out" "Please fix the 4 problems below."
expect "  ...beside the field" "$out" '<span id="name-error">Name is required.</span>'
expect "  ...an address that isn't one" "$out" "Email must be an email address."
expect "  ...a number that isn't one" "$out" "Age must be a whole number from 13 to 120."
expect "  ...a ticket that isn't on the list" "$out" "Ticket must be one of the choices."
expect "  ...and the field is marked for screen readers" "$out" 'aria-invalid="true" aria-describedby="age-error"'
expect "  ...with what they typed put back" "$out" 'value="alice@"'
out="$(curl -s --data-urlencode 'name=<b>Al</b>' -d 'email=x&age=30&ticket=student' "http://localhost:$PORT/")"
expect "what they typed comes back escaped" "$out" 'value="&lt;b&gt;Al&lt;/b&gt;"'
expect "  ...and one problem is one problem" "$out" "Please fix the problem below."
expect "an array instead of text is refused" "$(curl -s -g -d 'name[]=Alice&email=a@example.com&age=30&ticket=student' "http://localhost:$PORT/")" "Name must be text."
expect "a good registration" "$(curl -s --data-urlencode 'name=Zoë' -d 'email=zoe@example.com&age=30&ticket=student' "http://localhost:$PORT/")" "You're registered, Zoë: one Student ticket."
stop
