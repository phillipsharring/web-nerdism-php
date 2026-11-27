# forms: https://phillipharrington.com/php/forms
serve forms
expect "GET shows the form" "$(curl -s "http://localhost:$PORT/")" '<form method="post">'
expect "a good order" "$(curl -s -d 'name=Alice&size=medium&wrap=on' "http://localhost:$PORT/")" "Thanks, Alice: one medium shirt, gift wrapped."
expect "an unchecked box sends nothing" "$(curl -s -d 'name=Alice&size=small' "http://localhost:$PORT/")" "one small shirt."
out="$(curl -s -d 'name=&size=huge' "http://localhost:$PORT/")"
expect "the server catches a missing name" "$out" "Your name, please."
expect "  ...and a size that isn't on the list" "$out" "Pick a size from the list."
out="$(curl -s --data-urlencode 'name=<b>hi</b>' -d 'size=large' "http://localhost:$PORT/")"
expect "HTML in input comes back escaped" "$out" "Thanks, &lt;b&gt;hi&lt;/b&gt;"
if grep -qF "<b>hi</b>" <<< "$out"; then fail "  ...never raw" "$out"; else pass "  ...never raw"; fi
stop
