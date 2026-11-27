# never-trust-input: https://phillipharrington.com/php/never-trust-input
serve never-trust-input
out="$(curl -s -A 'Definitely a real browser' -e 'https://example.com/trust-me' -b 'admin=yes' -H 'X-Forwarded-For: 1.2.3.4' -d 'price=1' "http://localhost:$PORT/show.php?id[]=7&id[]=8")"
expect "a forged User-Agent arrives as sent" "$out" '"User-Agent": "Definitely a real browser"'
expect "  ...and a forged Referer" "$out" '"Referer": "https://example.com/trust-me"'
expect "  ...and a made-up cookie" "$out" '"admin": "yes"'
expect "  ...and X-Forwarded-For" "$out" '"X-Forwarded-For": "1.2.3.4"'
expect "  ...and a query string value that's an array" "$out" '"8"'
expect "invalid UTF-8 shows up as the replacement character" "$(curl -s "http://localhost:$PORT/show.php?name=%FF")" '"name": "�"'

expect "naive: an honest order" "$(curl -s -d 'qty=2&price=1200' "http://localhost:$PORT/naive.php")" 'Order total: $24.00 for 2 mugs.'
expect "naive: a price from the request is believed" "$(curl -s -d 'qty=2&price=1' "http://localhost:$PORT/naive.php")" 'Order total: $0.02 for 2 mugs.'
expect "naive: a negative quantity is believed" "$(curl -s -d 'qty=-5&price=1200' "http://localhost:$PORT/naive.php")" 'Order total: $-60.00 for -5 mugs.'
expect "naive: an array crashes it" "$(curl -s -d 'qty[]=1&price=1200' "http://localhost:$PORT/naive.php")" 'Uncaught TypeError'

expect "fixed: an honest order" "$(curl -s -d 'qty=2' "http://localhost:$PORT/")" 'Order total: $24.00 for 2 mugs.'
expect "fixed: the request's price is ignored" "$(curl -s -d 'qty=2&price=1' "http://localhost:$PORT/")" 'Order total: $24.00 for 2 mugs.'
for qty in -5 0 11 abc 2.5 1e1; do
    expect "fixed: qty=$qty is refused" "$(curl -s -d "qty=$qty" "http://localhost:$PORT/")" 'Pick from 1 to 10 mugs.'
    expect "  ...with a 422" "$(curl -s -o /dev/null -w 'status %{http_code}' -d "qty=$qty" "http://localhost:$PORT/")" 'status 422'
done
expect "fixed: an array is refused, not a crash" "$(curl -s -d 'qty[]=1' "http://localhost:$PORT/")" 'How many mugs?'
expect "fixed: a missing field is refused" "$(curl -s -d 'other=1' "http://localhost:$PORT/")" 'How many mugs?'
stop
