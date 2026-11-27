# xss: https://phillipharrington.com/php/xss
serve xss
page() { curl -s --get --data-urlencode "$2" "http://localhost:$PORT/$1"; }
script='name=<script>alert(document.cookie)</script>'
attr='name=" autofocus onfocus="this.onfocus=null;alert(document.cookie)'
js='website=javascript:alert(document.cookie)'

expect "naive: the pretend login cookie has no HttpOnly" "$(curl -sI "http://localhost:$PORT/naive.php" | grep -i '^set-cookie')" "Set-Cookie: session=pretend-session-id"
expect "naive: a script tag goes into the page as-is" "$(page naive.php "$script")" "<p>Hello, <script>alert(document.cookie)</script>.</p>"
expect "naive: a quote breaks out of the attribute" "$(page naive.php "$attr")" 'value="" autofocus onfocus="this.onfocus=null;alert(document.cookie)"'
expect "naive: a javascript: link goes in" "$(page naive.php "$js")" '<a href="javascript:alert(document.cookie)">'

out="$(page '' "$script")"
expect "fixed: the script tag comes out as text" "$out" "<p>Hello, &lt;script&gt;alert(document.cookie)&lt;/script&gt;.</p>"
if grep -qF "<script>" <<< "$out"; then fail "  ...never raw" "$out"; else pass "  ...never raw"; fi
out="$(page '' "$attr")"
expect "fixed: the quote stays inside the attribute" "$out" 'value="&quot; autofocus onfocus=&quot;this.onfocus=null;alert(document.cookie)"'
for bad in 'javascript:alert(document.cookie)' 'JavaScript:alert(1)' 'data:text/html,<script>alert(1)</script>'; do
    out="$(page '' "website=$bad")"
    expect "fixed: $bad isn't linked" "$out" "That website isn't a web address, so it isn't linked."
    if grep -qF '<a href' <<< "$out"; then fail "  ...no link at all" "$out"; else pass "  ...no link at all"; fi
done
expect "fixed: a real web address is linked" "$(page '' 'website=https://example.com/me?a=1&b=2')" '<a href="https://example.com/me?a=1&amp;b=2">'
expect "fixed: an array is ignored, not a crash" "$(curl -s "http://localhost:$PORT/?name[]=x")" "Hello, stranger."
stop
