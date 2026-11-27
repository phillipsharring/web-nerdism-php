# project-php-on-the-command-line: https://phillipharrington.com/php/projects/php-on-the-command-line
D=project-php-on-the-command-line
out="$(cli $D bin/top-words.php --top=2 sample.txt)"
expect "the top words in a file" "$out" "    5  café"
expect "  ...ties in alphabetical order" "$out" "    5  the"
if grep -qF "coffee" <<< "$out"; then fail "  ...only as many as asked for" "$out"; else pass "  ...only as many as asked for"; fi
expect "Café, CAFÉ and café are one word" "$(cli $D bin/top-words.php --top=1 sample.txt)" "5  café"
expect "--min-length skips short words" "$(cli $D bin/top-words.php --top=1 --min-length=5 sample.txt)" "2  coffee"
expect "it reads what's piped in" "$(printf 'one two two three three three\n' | docker run --rm -i -v "$ROOT/$D":/app -w /app "$IMAGE" php bin/top-words.php --top=1)" "    3  three"
expect "two files add up" "$(cli $D bin/top-words.php --top=1 sample.txt sample.txt)" "   10  café"
expect "--help shows the usage" "$(cli $D bin/top-words.php --help)" "Usage: top-words.php"
code() { docker run --rm -v "$ROOT/$D":/app -w /app "$IMAGE" php bin/top-words.php "$@" >/dev/null 2>&1; echo "exit $?"; }
expect "it works: exit 0" "$(code sample.txt)" "exit 0"
expect "a missing file: exit 1" "$(code nope.txt)" "exit 1"
expect "a bad number: exit 2" "$(code --top=lots sample.txt)" "exit 2"
expect "an option after a file: exit 2" "$(code sample.txt --top=3)" "exit 2"
out="$(docker run --rm -v "$ROOT/$D":/app -w /app "$IMAGE" php bin/top-words.php nope.txt 2>/dev/null)"
if [ -z "$out" ]; then pass "errors go to STDERR, not STDOUT"; else fail "errors go to STDERR, not STDOUT" "$out"; fi
expect "  ...where they say what's wrong" "$(docker run --rm -v "$ROOT/$D":/app -w /app "$IMAGE" php bin/top-words.php nope.txt 2>&1 >/dev/null)" "Can't read nope.txt"
expect "a directory isn't a file" "$(code bin)" "exit 1"
serve $D -t bin
expect "from a web server, it refuses" "$(curl -si "http://localhost:$PORT/top-words.php")" "HTTP/1.1 404"
stop
