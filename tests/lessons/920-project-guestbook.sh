# project-guestbook: https://phillipharrington.com/php/projects/guestbook
rm -rf "$ROOT/project-guestbook/storage"
serve project-guestbook -t public
app="http://localhost:$PORT"
expect "signing works" "$(curl -si -d 'name=Alice&message=Lovely site.' "$app/")" "Location: /"
expect "  ...and the entry shows" "$(curl -s "$app/")" "Lovely site."
curl -s -o /dev/null --data-urlencode "message=<script>alert('Hacked')</script>" -d 'name=Mallory' "$app/naive.php"
curl -s -o /dev/null --data-urlencode 'message=<img src=x onerror=alert(1)>' -d 'name=Mallory' "$app/naive.php"
curl -s -o /dev/null --data-urlencode 'website=javascript:alert(document.cookie)' -d 'name=Click me&message=hi' "$app/naive.php"
naive="$(curl -s "$app/naive.php")"
expect "naive: the script is in the page" "$naive" "<p><script>alert('Hacked')</script></p>"
expect "naive: so is the image that runs one" "$naive" "<p><img src=x onerror=alert(1)></p>"
expect "naive: and the link that runs one" "$naive" '<a href="javascript:alert(document.cookie)">Click me</a>'
fixed="$(curl -si "$app/")"
expect "fixed: the same entries, as text" "$fixed" "&lt;script&gt;alert(&#039;Hacked&#039;)&lt;/script&gt;"
expect "  ...the image too" "$fixed" "&lt;img src=x onerror=alert(1)&gt;"
if grep -qF 'javascript:' <<< "$(grep -F 'href=' <<< "$fixed")"; then fail "  ...and the javascript: link isn't a link" "$fixed"; else pass "  ...and the javascript: link isn't a link"; fi
if grep -qF '<script>' <<< "$fixed"; then fail "  ...no script anywhere" "$fixed"; else pass "  ...no script anywhere"; fi
expect "fixed: a Content-Security-Policy, just in case" "$fixed" "Content-Security-Policy: default-src 'self'"
expect "fixed: a javascript: website is refused" "$(curl -s -d 'name=x&message=hi&website=javascript:alert(1)' "$app/")" "A website has to start with http:// or https://."
curl -s -o /dev/null -d 'name=Bob&message=Hi&website=https://example.com/bob' "$app/"
expect "fixed: a real website is a link" "$(curl -s "$app/")" '<a href="https://example.com/bob" rel="nofollow ugc">Bob</a>'
curl -s -o /dev/null --data-urlencode $'message=line one\nline two' -d 'name=Carol' "$app/"
expect "fixed: line breaks survive, safely" "$(curl -s "$app/")" "line one<br />"
stop
rm -rf "$ROOT/project-guestbook/storage"
