# project-cookies: https://phillipharrington.com/php/projects/cookies
serve project-cookies -t public
attack='name=<script>alert(document.cookie)</script>'
out="$(curl -si -d 'name=Alice' "http://localhost:$PORT/naive.php")"
expect "naive: the cookie goes out with no flags" "$out" $'Set-Cookie: name=Alice\r'
expect "naive: the name comes back" "$(curl -s -b 'name=Alice' "http://localhost:$PORT/naive.php")" "Welcome back, Alice."
expect "naive: a planted cookie runs as a script" "$(curl -s -b "$attack" "http://localhost:$PORT/naive.php")" "Welcome back, <script>alert(document.cookie)</script>."
out="$(curl -si -d 'name=Alice' "http://localhost:$PORT/")"
expect "fixed: the cookie lasts 30 days" "$out" "Max-Age=2592000; path=/; HttpOnly; SameSite=Lax"
if grep -qi "secure" <<< "$(grep -i '^Set-Cookie' <<< "$out")"; then fail "  ...and isn't marked Secure over plain HTTP" "$out"; else pass "  ...and isn't marked Secure over plain HTTP"; fi
expect "  ...and answers with a 303" "$out" "HTTP/1.1 303"
expect "fixed: the name comes back" "$(curl -s -b 'name=Alice' "http://localhost:$PORT/")" "Welcome back, Alice."
out="$(curl -s -b "$attack" "http://localhost:$PORT/")"
expect "fixed: a planted cookie is only text" "$out" "Welcome back, &lt;script&gt;alert(document.cookie)&lt;/script&gt;."
if grep -qF "<script>" <<< "$out"; then fail "  ...never a script" "$out"; else pass "  ...never a script"; fi
expect "fixed: a cookie too long is ignored" "$(curl -s -b "name=$(printf 'x%.0s' {1..51})" "http://localhost:$PORT/")" "Your name"
expect "fixed: a name too long is refused" "$(curl -s -d "name=$(printf 'x%.0s' {1..51})" "http://localhost:$PORT/")" "A name, please, up to 50 characters."
expect "fixed: forget me deletes it" "$(curl -si -b 'name=Alice' -d 'forget=' "http://localhost:$PORT/")" "Set-Cookie: name=deleted; expires=Thu, 01 Jan 1970"
stop
