# project-cms: https://phillipharrington.com/php/projects/cms
cms() { APP_PORT=$PORT docker compose -p "$NAME-cms" -f "$ROOT/project-cms/compose.yml" "$@"; }
cms up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
A='editor:correct horse battery staple'
T="$(mktemp -d)"
printf '%s' 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=' | base64 -d > "$T/dot.png"
printf '%s' '<?php echo "ran: " . (6 * 7);' > "$T/shell.php"
cp "$T/dot.png" "$T/sneaky.php"; printf '%s' '<?php echo "ran: " . (6 * 7);' >> "$T/sneaky.php"

expect "the home page comes from the database" "$(curl -s "$U/")" "This page comes from the database."
expect "/about is the about page" "$(curl -s "$U/about")" "<h1>About us</h1>"
expect "an unknown page is a 404" "$(curl -si "$U/nope")" "HTTP/1.1 404"
expect "admin needs a login" "$(curl -si "$U/admin.php")" "HTTP/1.1 401"

form="$(curl -s -c "$T/jar" -b "$T/jar" -u "$A" "$U/admin.php?edit=about")"
token="$(sed -n 's/.*name="csrf" value="\([a-f0-9]*\)".*/\1/p' <<< "$form")"
post() { curl -s -c "$T/jar" -b "$T/jar" -u "$A" -F "csrf=$token" -F slug=about -F 'title=About us' "$@" "$U/admin.php"; }
expect "a POST without the token is refused" "$(curl -si -u "$A" -F slug=about -F title=Hacked -F body=x "$U/admin.php")" "HTTP/1.1 403"
expect "saving text works" "$(post --form-string 'body=<script>alert(1)</script>' -i)" "Location: /admin.php?saved=about"
expect "  ...and a script in the text shows as text" "$(curl -s "$U/about")" "&lt;script&gt;alert(1)&lt;/script&gt;"
expect "a PNG is accepted" "$(post -F 'body=With a picture.' -F "image=@$T/dot.png;type=image/png" -i)" "HTTP/1.1 303"
img="$(curl -s "$U/about" | sed -n 's/.*image.php?name=\([a-f0-9]*\.png\).*/\1/p')"
expect "  ...stored under a random name" "$img" ".png"
expect "  ...and served as an image" "$(curl -si "$U/image.php?name=$img")" "Content-Type: image/png"
expect "  ...with nosniff" "$(curl -si "$U/image.php?name=$img")" "X-Content-Type-Options: nosniff"
expect "  ...from outside the web root" "$(ls "$ROOT/project-cms/storage/uploads")" "$img"
expect "a PHP file is refused" "$(post -F body=x -F "image=@$T/shell.php;type=image/png")" "PNG, JPEG or WebP images only."
expect "  ...even named .png" "$(post -F body=x -F "image=@$T/shell.php;filename=cat.png;type=image/png")" "PNG, JPEG or WebP images only."
expect "a real PNG with PHP on the end gets through the checks" "$(post -F body=x -F "image=@$T/sneaky.php;type=image/png" -i)" "HTTP/1.1 303"
img2="$(curl -s "$U/about" | sed -n 's/.*image.php?name=\([a-f0-9]*\.png\).*/\1/p')"
out="$(curl -s "$U/image.php?name=$img2" | LC_ALL=C tr -c '[:print:]\n' '.')"
expect "  ...but it's stored as .png and sent as bytes, never run" "$out" '<?php echo'
if grep -qF "ran: 42" <<< "$out"; then fail "  ...the PHP never runs" "$out"; else pass "  ...the PHP never runs"; fi
expect "image.php refuses a path" "$(curl -si "$U/image.php?name=../../.env")" "HTTP/1.1 404"
expect "a bad slug is refused" "$(curl -s -c "$T/jar" -b "$T/jar" -u "$A" -F "csrf=$token" -F 'slug=../x' -F title=x -F body=x "$U/admin.php")" "The address can only use"

# Break It: the naive uploader
expect "naive: a PHP file uploads" "$(curl -s -F "image=@$T/shell.php" "$U/upload-naive.php")" "/uploads/shell.php"
expect "naive: and runs, as code, on the server" "$(curl -s "$U/uploads/shell.php")" "ran: 42"
expect "naive: even inside a real image" "$(curl -s -F "image=@$T/sneaky.php" "$U/upload-naive.php" >/dev/null; curl -s "$U/uploads/sneaky.php" | LC_ALL=C tr -c '[:print:]\n' '.')" "ran: 42"

cms down -v >/dev/null 2>&1
rm -rf "$T"
find "$ROOT/project-cms/storage/uploads" "$ROOT/project-cms/public/uploads" -type f ! -name .gitkeep -delete
