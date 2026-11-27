# project-log-viewer: https://phillipharrington.com/php/projects/log-viewer
serve project-log-viewer -t public
U='editor:correct horse battery staple'
expect "the viewer needs a login" "$(curl -si "http://localhost:$PORT/")" "HTTP/1.1 401"
out="$(curl -s -u "$U" "http://localhost:$PORT/")"
expect "it lists the logs" "$out" '<a href="?file=access.log">access.log</a>'
expect "  ...all of them" "$out" '<a href="?file=app.log">app.log</a>'
out="$(curl -s -u "$U" "http://localhost:$PORT/?file=app.log&lines=2")"
expect "it shows the last lines of a log" "$out" "ERROR Mail server did not answer"
if grep -qF "Started" <<< "$out"; then fail "  ...only as many as asked for" "$out"; else pass "  ...only as many as asked for"; fi
out="$(curl -s -u "$U" "http://localhost:$PORT/?file=access.log")"
expect "a script in a log line comes out escaped" "$out" "&lt;script&gt;alert(&#039;hi&#039;)&lt;/script&gt;"
if grep -qF "<script>" <<< "$out"; then fail "  ...never raw" "$out"; else pass "  ...never raw"; fi
expect "a bad line count falls back to 100" "$(curl -s -u "$U" "http://localhost:$PORT/?file=app.log&lines=lots")" "app.log, last 100 lines"
# Break It: the naive version
expect "naive: ../.env hands over the secret" "$(curl -s "http://localhost:$PORT/naive.php?file=../.env")" "API_KEY="
expect "naive: so does any file on the machine" "$(curl -s "http://localhost:$PORT/naive.php?file=../../../../../etc/passwd")" "root:x:0:0"
expect "naive: and the password hashes" "$(curl -s "http://localhost:$PORT/naive.php?file=../users.php")" '$2y$12$'
# Fix It: the allow list
for attack in '../.env' '../../../../../etc/passwd' '../users.php' 'app.log/../../.env' '%2e%2e%2f.env' 'app.log%00'; do
    out="$(curl -si -u "$U" "http://localhost:$PORT/?file=$attack")"
    if grep -qF "HTTP/1.1 404" <<< "$out" && ! grep -qF "API_KEY" <<< "$out" && ! grep -qF "root:x" <<< "$out"; then
        pass "fixed: $attack is a 404"
    else
        fail "fixed: $attack is a 404" "$out"
    fi
done
expect "fixed: an array instead of a name is a 404 too" "$(curl -si -u "$U" "http://localhost:$PORT/?file[]=app.log")" "HTTP/1.1 404"
stop
