# project-something-database-y: https://phillipharrington.com/php/projects/something-database-y
dbp() { APP_PORT=$PORT docker compose -p "$NAME-something-database-y" -f "$ROOT/project-something-database-y/compose.yml" "$@"; }
dbp up -d --build >/dev/null 2>&1
app="http://localhost:$PORT"
for _ in $(seq 1 90); do curl -s -o /dev/null "$app/" && break; sleep 1; done
union="q=' UNION SELECT email, password_hash, 0 FROM users -- "
expect "a search finds a book" "$(curl -s "$app/?q=frank")" "<td>Frankenstein</td><td>Mary Shelley</td><td>1818</td>"
expect "  ...accents and case don't matter (utf8mb4_0900_ai_ci)" "$(curl -s "$app/?q=MISE")" "<td>Les Misérables</td>"
expect "sorting by year works" "$(curl -s "$app/?sort=year" | grep -m1 '<td>')" "Pride and Prejudice"
out="$(curl -s -G --data-urlencode "$union" "$app/naive.php")"
expect "naive: UNION pulls another table into the page" "$out" "<td>admin@example.com</td><td>\$2y\$12\$"
expect "naive: a quote spills the SQL error" "$(curl -s "$app/naive.php?q=%27")" "SQLSTATE[42000]: Syntax error"
expect "naive: % matches everything" "$(curl -s "$app/naive.php?q=%25" | grep -c '<tr>')" "9"
out="$(curl -s -G --data-urlencode "$union" "$app/")"
expect "fixed: the same payload is just a search that finds nothing" "$out" "No books match."
if grep -qF "admin@example.com" <<< "$out"; then fail "  ...and no users" "$out"; else pass "  ...and no users"; fi
expect "  ...shown back as text" "$out" 'value="&#039; UNION SELECT email'
expect "fixed: a quote is only a quote" "$(curl -s "$app/?q=%27")" "No books match."
expect "fixed: % means a percent sign" "$(curl -s "$app/?q=%25")" "No books match."
expect "fixed: a made-up sort falls back to title" "$(curl -s -G --data-urlencode 'sort=(SELECT SLEEP(5))' "$app/" | grep -m1 '<td>')" "Dracula"
expect "fixed: an array is refused quietly" "$(curl -s -g "$app/?q[]=x&sort[]=year" | grep -m1 '<td>')" "Dracula"
dbp down -v >/dev/null 2>&1
