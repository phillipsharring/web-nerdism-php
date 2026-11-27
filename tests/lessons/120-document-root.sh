# document-root: https://phillipharrington.com/php/document-root
serve document-root -t public
expect "the page reads the settings from outside public/" "$(curl -s "http://localhost:$PORT/")" "Hello from the document root."
for path in /.env /../.env; do
    out="$(curl -s --path-as-is "http://localhost:$PORT$path")"
    if grep -qF "API_KEY" <<< "$out"; then fail "-t public keeps $path private" "$out"; else pass "-t public keeps $path private"; fi
done
stop
serve document-root
expect "serving the whole project hands over .env" "$(curl -s "http://localhost:$PORT/.env")" "API_KEY="
stop
