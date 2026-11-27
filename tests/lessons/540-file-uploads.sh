# file-uploads: https://phillipharrington.com/php/file-uploads
serve file-uploads -t public
up() { curl -s -w '\nstatus %{http_code}' -F "photo=@$ROOT/file-uploads/attack/$2" "http://localhost:$PORT/$1"; }

expect "naive: a PHP file uploads" "$(up naive.php proof.php)" "Saved. It&#039;s at /uploads/proof.php"
out="$(curl -s "http://localhost:$PORT/uploads/proof.php")"
expect "  ...and runs" "$out" "This uploaded file just ran on the server."
expect "  ...as root" "$out" "Running as: root"
expect "  ...with the project in reach" "$out" "storage"

out="$(up '' proof.php)"
expect "fixed: a PHP file is refused" "$out" "That is not a PNG, JPEG or GIF."
expect "  ...with a 422" "$out" "status 422"
expect "fixed: lying about the type and name doesn't help" "$(curl -s -F "photo=@$ROOT/file-uploads/attack/proof.php;type=image/png;filename=cat.png" "http://localhost:$PORT/")" "That is not a PNG, JPEG or GIF."
expect "fixed: no file at all is refused" "$(curl -s -d 'x=1' "http://localhost:$PORT/")" "Choose one photo to upload."
out="$(up '' photo.png)"
expect "fixed: a real PNG is saved under a new name" "$out" "Saved. It&#039;s at /image.php?id="
id="$(grep -o 'id=[0-9a-f]*\.png' <<< "$out" | cut -d= -f2)"
expect "  ...with a 32-character random name" "${#id}" "36"
out="$(curl -s -D - -o /dev/null "http://localhost:$PORT/image.php?id=$id")"
expect "  ...and served back as an image" "$out" "Content-Type: image/png"
expect "  ...with nosniff" "$out" "X-Content-Type-Options: nosniff"
out="$(up '' polyglot.gif)"
expect "fixed: a GIF with PHP inside passes the type checks" "$out" "Saved. It&#039;s at /image.php?id="
gid="$(grep -o 'id=[0-9a-f]*\.gif' <<< "$out" | cut -d= -f2)"
out="$(curl -s "http://localhost:$PORT/image.php?id=$gid")"
expect "  ...but it's sent as bytes, never run" "$out" '<?php echo "This GIF just ran as PHP."'
out="$(curl -s --path-as-is "http://localhost:$PORT/../storage/$gid")"
if grep -qF "GIF89a" <<< "$out"; then fail "  ...and storage/ can't be reached by URL" "$out"; else pass "  ...and storage/ can't be reached by URL"; fi
expect "fixed: image.php refuses a path" "$(curl -s -w ' %{http_code}' "http://localhost:$PORT/image.php?id=../storage/$gid")" "Not found. 404"
stop
docker run --rm -v "$ROOT/file-uploads":/app -w /app "$IMAGE" sh -c 'rm -f public/uploads/*.php storage/*.png storage/*.gif storage/*.jpg'
