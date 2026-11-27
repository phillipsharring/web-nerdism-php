# exceptions: https://phillipharrington.com/php/exceptions
serve exceptions -t public
expect "the bill splits, and adds up" "$(curl -s "http://localhost:$PORT/?cents=1000&people=3")" "Shares in cents: 334, 333, 333"
out="$(curl -si "http://localhost:$PORT/?cents=1000&people=0")"
expect "bad input is caught: 400" "$out" "400 Bad Request"
expect "  ...with a message that says why" "$out" "people must be a whole number from 1 to 50."
expect "not a number is caught too" "$(curl -s "http://localhost:$PORT/?cents=lots&people=3")" "cents must be a whole number from 1 to 1000000."
expect "an array is caught too" "$(curl -s "http://localhost:$PORT/?cents[]=1&people=3")" "cents must be a whole number"
out="$(curl -si "http://localhost:$PORT/oops.php")"
expect "an uncaught exception is a 500" "$out" "500 Internal Server Error"
expect "  ...with a short message" "$out" "Something went wrong. It's been logged."
if grep -qF "database is down" <<< "$out"; then fail "  ...and no details for the visitor" "$out"; else pass "  ...and no details for the visitor"; fi
sleep 0.5
expect "the details go to the log" "$(docker logs "$NAME" 2>&1)" "RuntimeException: Pretend the database is down. in /app/public/oops.php:7"
stop
