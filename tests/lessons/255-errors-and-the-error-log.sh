# errors-and-the-error-log: https://phillipharrington.com/php/errors-and-the-error-log
serve errors-and-the-error-log -d display_errors=1 -d log_errors=1
expect "no problem, no errors" "$(curl -s "http://localhost:$PORT/")" "Order 1001 is done."
out="$(curl -s "http://localhost:$PORT/?problem=warning")"
expect "development: the warning is on the page" "$out" 'Undefined array key "email"'
expect "  ...and the script carried on" "$out" "Order 1001 is done."
expect "development: the fatal error is on the page" "$(curl -s "http://localhost:$PORT/?problem=exception")" "Uncaught RuntimeException: The payment service did not answer"
stop
serve errors-and-the-error-log -d display_errors=0 -d log_errors=1
out="$(curl -s "http://localhost:$PORT/?problem=warning")"
if grep -qF "Undefined" <<< "$out"; then fail "production: no warning on the page" "$out"; else pass "production: no warning on the page"; fi
out="$(curl -si "http://localhost:$PORT/?problem=exception")"
expect "production: a fatal error is a 500" "$out" "500 Internal Server Error"
if grep -qF "payment service" <<< "$out"; then fail "  ...with nothing on the page" "$out"; else pass "  ...with nothing on the page"; fi
curl -s -o /dev/null "http://localhost:$PORT/?problem=%0AFAKE%20LOG%20LINE"
logs="$(docker logs "$NAME" 2>&1)"
expect "the log has the warning" "$logs" 'PHP Warning:  Undefined array key "email" in /app/index.php'
expect "  ...and the fatal error" "$logs" "PHP Fatal error:  Uncaught RuntimeException: The payment service did not answer"
expect "  ...and your own message" "$logs" "Order page viewed, problem: warning"
if grep -qF "FAKE LOG LINE" <<< "$logs"; then fail "  ...but no line forged from input" "$logs"; else pass "  ...but no line forged from input"; fi
stop
