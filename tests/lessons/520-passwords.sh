# passwords: https://phillipharrington.com/php/passwords
out="$(cli passwords naive.php)"
alice="$(grep '^alice@' <<< "$out" | cut -d' ' -f2)"
bob="$(grep '^bob@' <<< "$out" | cut -d' ' -f2)"
expect "naive: md5 of sunshine" "$alice" "0571749e2ac330a7455809c6b0e7af90"
if [ "$alice" = "$bob" ]; then pass "naive: the same password makes the same hash"; else fail "naive: the same password makes the same hash" "$out"; fi
out="$(cli passwords crack.php leaked.txt)"
expect "crack: Alice's md5 falls" "$out" "Cracked alice@example.com: sunshine"
expect "  ...and Bob's" "$out" "Cracked bob@example.com: sunshine"
expect "  ...and a word with a number on the end" "$out" "Cracked dana@example.com: dragon2024"
expect "  ...but not a long passphrase" "$out" "Cracked 3 of 4."

out="$(cli passwords fixed.php)"
alice="$(grep '^alice@' <<< "$out" | cut -d' ' -f2)"
bob="$(grep '^bob@' <<< "$out" | cut -d' ' -f2)"
expect "fixed: bcrypt, cost 12 (PHP 8.4's default)" "$alice" '$2y$12$'
if [ "$alice" != "$bob" ]; then pass "fixed: the same password makes different hashes"; else fail "fixed: the same password makes different hashes" "$out"; fi
expect "fixed: the right password verifies" "$out" 'Alice types "sunshine": yes'
expect "fixed: a different one doesn't" "$out" 'Alice types "Sunshine": no'
expect "fixed: an old hash needs rehashing" "$out" "A cost-10 hash needs rehashing: yes"
expect "crack: five seconds against password_hash() cracks nothing" "$(cli passwords crack.php leaked-fixed.txt)" "Cracked 0 of 4."
expect "crack: it wants a file" "$(cli passwords crack.php)" "Usage: php crack.php leaked.txt"
docker run --rm -v "$ROOT/passwords":/app -w /app "$IMAGE" rm -f leaked.txt leaked-fixed.txt
