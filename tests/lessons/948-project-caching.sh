# project-caching: https://phillipharrington.com/php/projects/caching
cli project-caching clear-cache.php >/dev/null
serve project-caching -t public
U="http://localhost:$PORT/report.php"
out="$(curl -si -w 'took=%{time_total}' "$U?region=north")"
expect "the first request is a miss" "$out" "X-Cache: MISS"
expect "  ...and makes the report" "$out" '"region": "north"'
slow="$(sed -n 's/.*took=//p' <<< "$out")"
if awk "BEGIN{exit !($slow >= 1)}"; then pass "  ...slowly ($slow s)"; else fail "  ...slowly" "$slow"; fi
out="$(curl -si -w 'took=%{time_total}' "$U?region=north")"
expect "the second is a hit" "$out" "X-Cache: HIT"
fast="$(sed -n 's/.*took=//p' <<< "$out")"
if awk "BEGIN{exit !($fast < 0.5)}"; then pass "  ...and fast ($fast s)"; else fail "  ...and fast" "$fast"; fi
expect "  ...with the same report" "$(curl -s "$U?region=north")" "$(curl -s "$U?region=north")"
expect "browsers may keep it a minute" "$out" "Cache-Control: public, max-age=60"
etag="$(sed -n 's/^ETag: //p' <<< "$out" | tr -d '\r')"
expect "  ...and it has an ETag" "$etag" '"'
out="$(curl -si -H "If-None-Match: $etag" "$U?region=north")"
expect "the matching ETag gets a 304" "$out" "HTTP/1.1 304"
if grep -qF '"region"' <<< "$out"; then fail "  ...with no body" "$out"; else pass "  ...with no body"; fi
expect "an old ETag gets the whole thing" "$(curl -si -H 'If-None-Match: "old"' "$U?region=north")" "HTTP/1.1 200"
expect "each region is cached on its own" "$(curl -si "$U?region=south")" "X-Cache: MISS"
expect "a region not on the list is refused" "$(curl -si "$U?region=narnia")" "HTTP/1.1 400"
for i in $(seq 1 10); do curl -s -o /dev/null "$U?region=made-up-$i"; done
expect "  ...and made-up regions make no cache files" "$(ls "$ROOT/project-caching/cache" | grep -c json)" "2"
stop
expect "a cached value expires" "$(cli project-caching -r 'require "src/FileCache.php"; $c = new FileCache("cache"); $n = 0; $f = function () use (&$n) { return ++$n; }; $c->remember("t", 1, $f); $c->remember("t", 1, $f, $hit); echo $hit ? "hit " : "miss "; sleep(2); $c->remember("t", 1, $f, $hit); echo $hit ? "hit" : "miss", " computed=$n";')" "hit miss computed=2"
expect "clear-cache.php empties it" "$(cli project-caching clear-cache.php)" "Removed 3 cached value(s)."
