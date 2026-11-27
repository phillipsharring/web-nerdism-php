# text-and-utf-8: https://phillipharrington.com/php/text-and-utf-8
out="$(cli text-and-utf-8 bytes.php)"
expect "strlen counts bytes, mb_strlen characters" "$out" "strlen: 5, mb_strlen: 4"
expect "é is two bytes" "$out" "636166c3a9"
expect "substr cuts é in half" "$out" "substr: 636166c3, mb_substr: café"
expect "strtoupper misses é" "$out" "strtoupper: CAFé, mb_strtoupper: CAFÉ"
expect "an emoji is four bytes" "$out" "thumbs up: 4 bytes, 1 character"
expect "a combining accent looks the same but isn't" "$out" "is 5 characters; equal to café? no"
expect "mojibake" "$out" "Read wrong: cafÃ©"
expect "broken UTF-8 fails the check" "$out" "bool(false)"
serve text-and-utf-8
out="$(curl -s "http://localhost:$PORT/?name=caf%C3%A9")"
expect "the page counts characters and bytes" "$out" "Hello, café: 4 characters, 5 bytes."
expect "  ...and says it's UTF-8" "$(curl -sI "http://localhost:$PORT/")" "charset=UTF-8"
out="$(curl -s "http://localhost:$PORT/?name=caf%E9")"
expect "bytes that aren't UTF-8 are refused" "$out" "That name is not valid UTF-8 text."
if grep -qF "Hello" <<< "$out"; then fail "  ...and never echoed" "$out"; else pass "  ...and never echoed"; fi
e_times() { printf '%%C3%%A9%.0s' $(seq 1 "$1"); } # é, URL-encoded, N times
expect "too long is refused, counted in characters" "$(curl -s "http://localhost:$PORT/?name=$(e_times 21)")" "Twenty characters at most"
expect "twenty é is fine, though it's forty bytes" "$(curl -s "http://localhost:$PORT/?name=$(e_times 20)")" "20 characters, 40 bytes."
expect "an array instead of a name is refused" "$(curl -s -g "http://localhost:$PORT/?name[]=x")" "not valid UTF-8 text"
stop
