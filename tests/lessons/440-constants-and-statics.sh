# constants-and-statics: https://phillipharrington.com/php/constants-and-statics
out="$(cli constants-and-statics statics.php)"
expect "constants, by name" "$out" "Bill Splitter: 1 to 50 people"
expect "a static variable keeps counting" "$out" "Tickets: 1, 2, 3"
expect "a static property is shared by every object" "$out" "a counted 2, b counted 1, all counters together: 3"
expect "a constant can't change" "$(cli constants-and-statics -r 'const MAX_PEOPLE = 50; const MAX_PEOPLE = 60;')" "Constant MAX_PEOPLE already defined"
out="$(cli constants-and-statics why-not.php)"
expect "the welded version only knows now" "$out" "Welded, right now: Good"
expect "the handed-in version can be any hour" "$out" "Handed 9: Good morning"
expect "  ...morning or afternoon" "$out" "Handed 15: Good afternoon"
serve constants-and-statics
expect "a static resets every request" "$(curl -s "http://localhost:$PORT/request.php")" "Your ticket: 1"
expect "  ...every time" "$(curl -s "http://localhost:$PORT/request.php")" "Your ticket: 1"
stop
