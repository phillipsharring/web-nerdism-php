# cookies: https://phillipharrington.com/php/cookies
serve cookies
expect "the first visit sets the cookie" "$(curl -si "http://localhost:$PORT/")" "Set-Cookie: visits=1;"
expect "  ...HttpOnly and SameSite" "$(curl -si "http://localhost:$PORT/")" "HttpOnly; SameSite=Lax"
expect "the server believes a made-up cookie" "$(curl -s -b 'visits=41' "http://localhost:$PORT/")" "Visit number 42."
expect "(int) turns nonsense into 0" "$(curl -s -b 'visits=banana' "http://localhost:$PORT/")" "Visit number 1."
stop
