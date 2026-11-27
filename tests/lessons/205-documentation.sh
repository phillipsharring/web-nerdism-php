# documentation: https://phillipharrington.com/php/documentation
out="$(docker run --rm "$IMAGE" php --rf strpos 2>&1)"
expect "php --rf shows the parameters" "$out" '<optional> int $offset = 0'
expect "  ...and the return type" "$out" "Return [ int|false ]"
expect "a removed function isn't there" "$(docker run --rm "$IMAGE" php --rf mysql_connect 2>&1)" "Function mysql_connect() does not exist"
out="$(cli documentation strpos.php)"
expect "found at the start is 0" "$out" "int(0)"
expect "not found is false" "$out" "bool(false)"
expect "the loose check gets it wrong" "$out" "Loose check: no apple"
expect "the strict check gets it right" "$out" "Strict check: found apple"
expect "str_contains gets it right" "$out" "str_contains: found apple"
