# project-blog: https://phillipharrington.com/php/projects/blog
blog() { APP_PORT=$PORT docker compose -p "$NAME-blog" -f "$ROOT/project-blog/compose.yml" "$@"; }
blog up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT"
out="$(curl -s "$U/")"
expect "the list starts with the newest" "$out" '<a href="/posts/escaping">Escaping &lt;b&gt;output&lt;/b&gt;</a>'
expect "  ...three to a page" "$out" "Page 1 of 2"
expect "  ...with a link to older posts" "$out" '<a href="/?page=2" rel="next">Older</a>'
expect "  ...and readable dates" "$out" "November 20, 2026"
if grep -qF "hello" <<< "$out"; then fail "  ...the oldest isn't on page 1" "$out"; else pass "  ...the oldest isn't on page 1"; fi
out="$(curl -s "$U/?page=2")"
expect "page 2 has the oldest" "$out" '<a href="/posts/hello">Hello</a>'
expect "  ...and a link back" "$out" 'rel="prev">Newer</a>'
expect "a page that isn't a number is page 1" "$(curl -s "$U/?page=abc")" "Page 1 of 2"
expect "a negative page is page 1" "$(curl -s "$U/?page=-3")" "Page 1 of 2"
expect "past the last page is a 404" "$(curl -si "$U/?page=99")" "HTTP/1.1 404"
expect "SQL in the page number goes nowhere" "$(curl -s "$U/?page=1%20OR%201=1")" "Page 1 of 2"
out="$(curl -s "$U/posts/hello")"
expect "a post has its own page" "$out" "<h1>Hello</h1>"
expect "  ...paragraphs from blank lines" "$out" "<p>It has two paragraphs.</p>"
expect "  ...a machine-readable date" "$out" 'datetime="2026-11-01T09:00:00+00:00"'
expect "UTF-8 from the database" "$(curl -s "$U/posts/cafe")" "café, naïve, Zoë"
expect "a draft is a 404" "$(curl -si "$U/posts/unfinished")" "HTTP/1.1 404"
expect "a scheduled post is a 404" "$(curl -si "$U/posts/someday")" "HTTP/1.1 404"
expect "a bad slug is a 404" "$(curl -si "$U/posts/..%2f..%2fetc")" "HTTP/1.1 404"
out="$(curl -si "$U/feed")"
expect "the feed is Atom" "$out" "Content-Type: application/atom+xml; charset=utf-8"
expect "  ...with absolute links from SITE_URL" "$out" "<id>http://localhost:$PORT/posts/escaping</id>"
expect "  ...and the HTML in a title escaped" "$out" "<title>Escaping &lt;b&gt;output&lt;/b&gt;</title>"
if grep -qF "unfinished" <<< "$out" || grep -qF "someday" <<< "$out"; then fail "  ...no drafts or scheduled posts" "$out"; else pass "  ...no drafts or scheduled posts"; fi
expect "  ...it parses as XML" "$(curl -s "$U/feed" | blog exec -T app php -r '$x = simplexml_load_string(stream_get_contents(STDIN)); echo $x ? count($x->entry) . " entries" : "broken";')" "5 entries"
expect "a forged Host header doesn't change the feed's links" "$(curl -s -H 'Host: evil.example' "$U/feed")" "<id>http://localhost:$PORT/"
blog down -v >/dev/null 2>&1
