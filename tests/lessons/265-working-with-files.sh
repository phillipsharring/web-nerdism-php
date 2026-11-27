# working-with-files: https://phillipharrington.com/php/working-with-files
wipe() { docker run --rm -v "$ROOT/working-with-files":/app -w /app "$IMAGE" rm -rf data; }
wipe
expect "no notes to start" "$(cli working-with-files notes.php list)" "No notes yet."
expect "add a note" "$(cli working-with-files notes.php add 'Buy milk')" "Saved."
cli working-with-files notes.php add 'Call the café about the order' >/dev/null
expect "an empty note is refused" "$(cli working-with-files notes.php add '   ')" "A note is 1 to 200 characters of text."
expect "a note too long is refused" "$(cli working-with-files notes.php add "$(printf 'x%.0s' $(seq 1 201))")" "A note is 1 to 200 characters of text."
cli working-with-files notes.php add "$(printf 'one\ntwo')" >/dev/null
out="$(cli working-with-files notes.php list)"
expect "list reads them back, in order" "$out" "1. Buy milk"
expect "  ...UTF-8 intact" "$out" "2. Call the café about the order"
expect "  ...and a new line in input can't make two notes" "$out" "3. one two"
out="$(cli working-with-files search.php notes.txt milk)"
expect "search reads line by line" "$out" "1 of 3 lines matched"
for name in ../notes.php ../../tests/run.sh /etc/passwd ''; do
    out="$(cli working-with-files search.php "$name" php)"
    expect "search refuses '$name'" "$out" "Not a file in data/"
done
wipe
