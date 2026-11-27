# single-point-of-entry: https://phillipharrington.com/php/single-point-of-entry
B="http://localhost:$PORT"
bodycode() { curl -s -w " %{http_code}" "$@" | tr -d "\n"; }
serve single-point-of-entry -t before/public
expect "before: the home page" "$(bodycode "$B/")" "Welcome to the café. 200"
expect "  ...account remembered its check" "$(bodycode "$B/account.php")" "Log in first. 401"
expect "  ...admin forgot, and shows anyone everything" "$(bodycode "$B/admin.php")" "every customer's email address. 200"
stop
serve single-point-of-entry -t after/public after/public/index.php
expect "after: the home page" "$(bodycode "$B/")" "Welcome to the café. 200"
expect "  ...account needs a login" "$(bodycode "$B/account")" "Log in first. 401"
expect "  ...and has one" "$(bodycode -H 'Authorization: Bearer alice-token' "$B/account")" "Your account, Alice. 200"
expect "  ...admin is a 404 to a stranger" "$(bodycode "$B/admin")" "Not found. 404"
expect "  ...and to a forged token" "$(bodycode -H 'Authorization: Bearer admin' "$B/admin")" "Not found. 404"
expect "  ...and to a user who isn't an admin" "$(bodycode -H 'Authorization: Bearer alice-token' "$B/admin")" "Not found. 404"
expect "  ...but not to an admin" "$(bodycode -H 'Authorization: Bearer root-token' "$B/admin")" "every customer's email address. 200"
expect "  ...a path that's no route is a 404" "$(bodycode "$B/admin.php")" "Not found. 404"
expect "  ...the URL can't name a file" "$(bodycode --path-as-is "$B/../src/app.php")" "Not found. 404"
expect "  ...real files still get served" "$(curl -s "$B/style.css")" "font-family"
stop
