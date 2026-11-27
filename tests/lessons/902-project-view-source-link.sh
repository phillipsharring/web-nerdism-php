# project-view-source-link: https://phillipharrington.com/php/projects/view-source-link
serve project-view-source-link -t public
expect "the page links to its source" "$(curl -s "http://localhost:$PORT/")" 'href="/source.php?file=index.php"'
out="$(curl -s "http://localhost:$PORT/source.php?file=index.php")"
expect "the source is shown, highlighted" "$out" "<code"
expect "  ...as text, not run" "$out" "&lt;?php"
expect "the viewer can show itself" "$(curl -s "http://localhost:$PORT/source.php?file=source.php")" "array_key_exists"
for f in ../../../etc/passwd /etc/passwd index.php%00 ''; do
    out="$(curl -s -w ' %{http_code}' "http://localhost:$PORT/source.php?file=$f")"
    if grep -qF "root:" <<< "$out" || ! grep -qF "No source for that. 404" <<< "$(tr -d '\n' <<< "$out")"; then fail "file=$f is refused with a 404" "$out"; else pass "file=$f is refused with a 404"; fi
done
expect "an array in the query string is refused too" "$(curl -s -g "http://localhost:$PORT/source.php?file[]=index.php")" "No source for that."
stop
