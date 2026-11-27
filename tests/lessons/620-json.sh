# json: https://phillipharrington.com/php/json
out="$(cli json types.php)"
expect "PHP to JSON" "$out" '"price_cents": 1999'
expect "  ...null stays null" "$out" '"note": null'
expect "a list is an array" "$out" '["red","blue"]'
expect "keys make an object" "$out" '{"size":"large"}'
expect "an empty array is [], an empty object is {}" "$out" '[] {}'
expect "JSON to PHP, as arrays" "$out" '["qty"]=>'
expect "bad JSON throws" "$out" "Not JSON: Syntax error"
expect "text that isn't UTF-8 throws" "$out" "Not UTF-8: Malformed UTF-8 characters"
expect "accents as \\u codes, or as they are" "$out" '"caf\u00e9" "café"'
serve json -t public
post() { curl -s -w ' %{http_code}' -H 'Content-Type: application/json' -d "$1" "http://localhost:$PORT/order.php"; }
expect "a good order" "$(post '{"size":"large","qty":2}')" '{"ordered":{"size":"large","qty":2}} 201'
expect "a qty sent as a string is refused" "$(post '{"size":"large","qty":"2"}')" " 422"
expect "a size not on the list is refused" "$(post '{"size":"huge","qty":1}')" " 422"
expect "too many is refused" "$(post '{"size":"small","qty":11}')" " 422"
expect "JSON that isn't an object is refused" "$(post '"large"')" " 422"
expect "not JSON at all is a 400" "$(post 'size=large')" '{"error":"That is not JSON."} 400'
expect "GET is a 405" "$(curl -si "http://localhost:$PORT/order.php")" "Allow: POST"
stop
