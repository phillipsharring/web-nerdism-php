# defensive-programming: https://phillipharrington.com/php/defensive-programming
out="$(cli defensive-programming run.php before)"
expect "before: a fair discount" "$out" "25% off 20.00: 15.00"
expect "  ...a negative price" "$out" "150% off 20.00: -10.00"
expect "  ...and a discount that costs more" "$out" "-50% off 20.00: 30.00"
expect "  ...though the type still catches a string" "$out" "'25' as a string: refused by the type"
out="$(cli defensive-programming run.php after)"
expect "after: the same fair discount" "$out" "25% off 20.00: 15.00"
expect "  ...refuses 150, and says what it got" "$out" "150% off 20.00: refused: percentOff must be 0 to 100, got 150"
expect "  ...refuses -50" "$out" "-50% off 20.00: refused: percentOff must be 0 to 100, got -50"
expect "run.php takes only before or after" "$(cli defensive-programming run.php ../etc/passwd)" "Usage: php run.php before|after"
serve defensive-programming
expect "the shop takes a real code, in any case" "$(curl -s "http://localhost:$PORT/shop.php?code=half")" "Your price: 10.00"
out="$(curl -si "http://localhost:$PORT/shop.php?code=FREE")"
expect "  ...turns away an unknown one politely" "$out" "That code isn't one of ours."
expect "  ...with a 422" "$out" "422"
expect "  ...and an array where a string should be" "$(curl -s "http://localhost:$PORT/shop.php?code[]=HALF")" "That code isn't one of ours."
stop
