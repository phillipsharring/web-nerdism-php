# arrays: https://phillipharrington.com/php/arrays
out="$(cli arrays arrays.php)"
expect "a list, numbered from 0, with one added" "$out" "small, 4 sizes"
expect "named keys and a nested array" "$out" "Order 1001 has 2 items"
expect "foreach with keys" "$out" "items: shirt, mug"
expect "'7' becomes an int key, '07' doesn't" "$out" 'string(2) "07"'
expect "array_filter keeps the keys" "$out" "[sticker] => 300"
expect "array_map" "$out" "[shirt] => 4000"
expect "plain sort puts capitals first" "$out" "[2] => alice"
expect "a case-insensitive sort" "$out" "[0] => alice"
expect "assigning copies" "$out" "3 and 4"
serve arrays
expect "one value" "$(curl -s "http://localhost:$PORT/query.php?tag=php")" "One tag: php"
out="$(curl -s -g "http://localhost:$PORT/query.php?tag[]=php&tag[]=web&size[shirt]=large")"
expect "tag[] makes a list" "$out" 'string(3) "web"'
expect "size[shirt] makes named keys" "$out" '["shirt"]=>'
expect "  ...and the page refuses the list" "$out" "Refused: tag should be one value, not a list"
stop
