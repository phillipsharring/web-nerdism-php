# security-headers: https://phillipharrington.com/php/security-headers
serve security-headers
naive="$(curl -sI "http://localhost:$PORT/naive.php")"
fixed="$(curl -sI "http://localhost:$PORT/")"
for h in Content-Security-Policy X-Frame-Options X-Content-Type-Options Referrer-Policy Permissions-Policy Strict-Transport-Security; do
    if grep -qi "^$h:" <<< "$naive"; then fail "naive: no $h" "$naive"; else pass "naive: no $h"; fi
done
expect "naive: PHP announces its version" "$naive" "X-Powered-By: PHP/8.4"
expect "fixed: CSP allows scripts only from the site" "$fixed" "script-src 'self'"
expect "  ...and no framing" "$fixed" "frame-ancestors 'none'"
expect "fixed: X-Frame-Options" "$fixed" "X-Frame-Options: DENY"
expect "fixed: nosniff" "$fixed" "X-Content-Type-Options: nosniff"
expect "fixed: Referrer-Policy" "$fixed" "Referrer-Policy: strict-origin-when-cross-origin"
expect "fixed: Permissions-Policy" "$fixed" "Permissions-Policy: camera=(), microphone=(), geolocation=()"
expect "fixed: HSTS" "$fixed" "Strict-Transport-Security: max-age=31536000; includeSubDomains"
if grep -qi "^X-Powered-By" <<< "$fixed"; then fail "fixed: no X-Powered-By" "$fixed"; else pass "fixed: no X-Powered-By"; fi
expect "the bait page frames the naive page" "$(curl -s "http://localhost:$PORT/evil.html")" '<iframe src="/naive.php">'
expect "  ...and evil-fixed.html the fixed one" "$(curl -s "http://localhost:$PORT/evil-fixed.html")" '<iframe src="/">'
expect "the page has an inline script for CSP to block" "$(curl -s "http://localhost:$PORT/")" "<script>document.getElementById('inline')"
expect "the delete button still needs its CSRF token" "$(curl -s -w ' %{http_code}' -d 'x=1' "http://localhost:$PORT/")" "403"
stop
