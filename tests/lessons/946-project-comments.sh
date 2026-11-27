# project-comments: https://phillipharrington.com/php/projects/comments
cmt() { APP_PORT=$PORT docker compose -p "$NAME-comments" -f "$ROOT/project-comments/compose.yml" "$@"; }
rows() { cmt exec -T db mysql -uapp -papp-password -N course -e "$1" 2>/dev/null; }
cmt up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
token() { curl -s "$U/" | sed -n 's/.*name="t" value="\([0-9a-f.]*\)".*/\1/p'; }
send() { curl -s -o /dev/null -w '%{http_code} %{redirect_url}' -d post=1 --data-urlencode "t=$1" --data-urlencode "name=$2" --data-urlencode "body=$3" "${@:4}" "$U/comment.php"; }

t="$(token)"
expect "the form carries a signed token" "$t" "."
expect "a comment sent at once is refused" "$(send "$t" Ann 'First!')" "400"
sleep 3
expect "after a few seconds it's accepted" "$(send "$t" Ann 'Nice post.')" "303 http://localhost:$PORT/?note=thanks#comments"
expect "  ...and shown" "$(curl -s "$U/")" "<p>Nice post.</p>"
out="$(send "$t" 'Mallory' '<script>alert(document.cookie)</script>')"
expect "a script is stored as typed" "$(rows 'SELECT body FROM comments ORDER BY id DESC LIMIT 1')" "<script>alert(document.cookie)</script>"
out="$(curl -s "$U/")"
expect "  ...and shown as text" "$out" "&lt;script&gt;alert(document.cookie)&lt;/script&gt;"
if grep -qF "<script>" <<< "$out"; then fail "  ...never as a script" "$out"; else pass "  ...never as a script"; fi
expect "a forged token is refused" "$(send "$(( $(date +%s) - 60 )).$(printf '%064d' 0)" Bot spam)" "400"
expect "a filled-in honeypot looks like success" "$(send "$t" Bot 'Buy now' -d website=http://spam.example)" "303"
expect "  ...but nothing is stored" "$(rows "SELECT COUNT(*) FROM comments WHERE name = 'Bot'")" "0"
expect "two links: held for a person" "$(send "$t" Lin 'See http://a.example and http://b.example')" "?note=held"
if grep -qF "a.example" <<< "$(curl -s "$U/")"; then fail "  ...and not shown yet" ""; else pass "  ...and not shown yet"; fi
expect "invalid UTF-8 is refused" "$(curl -s -o /dev/null -w '%{http_code}' -d post=1 --data-urlencode "t=$t" -d name=X -d 'body=%C3%28' "$U/comment.php")" "400"
expect "an empty comment is refused" "$(send "$t" Ann '   ')" "422"
expect "a post that doesn't exist is a 404" "$(curl -s -o /dev/null -w '%{http_code}' -d 'post=1 OR 1=1' -d name=X -d body=Y "$U/comment.php")" "404"
send "$t" Ann 'Two.' >/dev/null; send "$t" Ann 'Three.' >/dev/null
expect "the sixth in ten minutes is a 429" "$(send "$t" Ann 'Six.')" "429"
expect "  ...with Retry-After" "$(curl -si -d post=1 --data-urlencode "t=$t" -d name=Ann -d body=Again "$U/comment.php")" "Retry-After: 600"
expect "the note comes from a list, not the URL" "$(curl -s "$U/?note=%3Cscript%3E")" "<h2 id=\"comments\">Comments</h2>"

# Break It: the naive version
curl -s --data-urlencode 'name=Mallory' --data-urlencode 'body=<script>alert(document.cookie)</script>' "$U/naive.php" >/dev/null
expect "naive: the script comes back as a script" "$(curl -s "$U/naive.php")" "wrote: <script>alert(document.cookie)</script>"
before="$(rows 'SELECT COUNT(*) FROM comments')"
for i in $(seq 1 20); do curl -s -o /dev/null -d name=Bot -d "body=Buy now $i http://spam.example" "$U/naive.php"; done
expect "naive: twenty spam comments in a second, all stored" "$(rows 'SELECT COUNT(*) FROM comments')" "$((before + 20))"
cmt down -v >/dev/null 2>&1
