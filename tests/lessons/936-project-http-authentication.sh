# project-http-authentication: https://phillipharrington.com/php/projects/http-authentication
serve project-http-authentication -t public
out="$(curl -si "http://localhost:$PORT/")"
expect "no credentials: 401" "$out" "HTTP/1.1 401"
expect "  ...and the header that makes the browser ask" "$out" 'WWW-Authenticate: Basic realm="Staff only", charset="UTF-8"'
expect "a wrong password: 401" "$(curl -si -u 'editor:guess' "http://localhost:$PORT/")" "HTTP/1.1 401"
expect "a name that doesn't exist: 401" "$(curl -si -u 'nobody:correct horse battery staple' "http://localhost:$PORT/")" "HTTP/1.1 401"
out="$(curl -si -u 'editor:correct horse battery staple' "http://localhost:$PORT/")"
expect "the right password: 200" "$out" "HTTP/1.1 200"
expect "  ...and the page" "$out" "Hello, editor. You're in."
expect "  ...marked private" "$out" "Cache-Control: private, no-store"
sent="$(curl -sv -o /dev/null -u 'editor:correct horse battery staple' "http://localhost:$PORT/" 2>&1 | sed -n 's/^> Authorization: Basic //p' | tr -d '\r')"
expect "the password travels as base64, which anyone can decode" "$(echo "$sent" | base64 -d)" "editor:correct horse battery staple"
expect "users.php is out of reach" "$(curl -s "http://localhost:$PORT/../users.php" --path-as-is)" "You need to log in"
stop
expect "hash-password.php makes a hash that verifies" "$(docker run --rm -i -v "$ROOT/project-http-authentication":/app -w /app php:8.4-cli sh -c 'echo "s3cret" | php hash-password.php | sed "s/^Password: //" > /tmp/h && php -r "echo password_verify(\"s3cret\", trim(file_get_contents(\"/tmp/h\"))) ? \"verified\" : \"no\";"')" "verified"
