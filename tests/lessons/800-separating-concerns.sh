# separating-concerns: https://phillipharrington.com/php/separating-concerns
serve separating-concerns -t before
before="$(curl -s "http://localhost:$PORT/")"
stop
serve separating-concerns -t after/public
after="$(curl -s "http://localhost:$PORT/")"
stop
expect "before: newest published post first" "$before" "<li>Winter hours <small>Nov 25</small></li>"
expect "  ...UTF-8 comes through" "$before" "café"
expect "  ...and titles are escaped" "$before" "&lt;script&gt;alert(1)&lt;/script&gt; &amp; other tricks"
if grep -qE "Secret menu|Spring menu" <<< "$before"; then fail "  ...no drafts, nothing from the future" "$before"; else pass "  ...no drafts, nothing from the future"; fi
if [ "$before" = "$after" ]; then pass "after serves the very same page"; else fail "after serves the very same page" "$after"; fi
expect "the rules check out alone" "$(cli separating-concerns after/check.php)" "ok"
