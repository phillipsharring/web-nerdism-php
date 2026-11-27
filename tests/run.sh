#!/usr/bin/env bash
#
# Runs every example the way its lesson says to, and checks the output.
# Needs Docker and curl. Run from anywhere: bash tests/run.sh
#
# Servers run detached (-d) on port 8099 instead of the lessons' -it on 8000,
# so the script can talk to them and stop them; everything else matches.
set -uo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
IMAGE=php:8.4-cli
PORT=8099
FAILED=0

pass() { echo "  ok    $1"; }
fail() { echo "  FAIL  $1"; echo "$2" | sed 's/^/        /'; FAILED=1; }

# expect NAME OUTPUT NEEDLE
expect() {
    if grep -qF -- "$3" <<< "$2"; then pass "$1"; else fail "$1" "$2"; fi
}

cli() { # cli DIR ARGS...
    local dir="$1"; shift
    docker run --rm -v "$ROOT/$dir":/app -w /app "$IMAGE" php "$@" 2>&1
}

serve() { # serve DIR [EXTRA PHP -S ARGS...]
    local dir="$1"; shift
    docker rm -f wnp-test >/dev/null 2>&1
    docker run -d --name wnp-test -p "$PORT:8000" -v "$ROOT/$dir":/app -w /app "$IMAGE" \
        php -S 0.0.0.0:8000 "$@" >/dev/null
    for _ in $(seq 1 30); do
        curl -s -o /dev/null "http://localhost:$PORT/" && return 0
        sleep 0.5
    done
    echo "server in $dir never answered"; return 1
}
stop() { docker rm -f wnp-test >/dev/null 2>&1; }
trap stop EXIT

docker pull -q "$IMAGE" >/dev/null

echo "client-server"
serve client-server
expect "the server says hello" "$(curl -s "http://localhost:$PORT/")" "Hello from the server."
stop

echo "anatomy-of-a-url"
out="$(cli anatomy-of-a-url url.php)"
expect "parse_url finds every part" "$out" "[port] => 8080"
expect "  ...including the fragment" "$out" "[fragment] => comments"
serve anatomy-of-a-url
out="$(curl -s "http://localhost:$PORT/anything/you/like?name=you&page=2#here")"
expect "the server gets the path and query" "$out" "Path and query: /anything/you/like?name=you&page=2"
expect "  ...and \$_GET is decoded" "$out" "[page] => 2"
if grep -qF "#here" <<< "$out"; then fail "the fragment never arrives" "$out"; else pass "the fragment never arrives"; fi
stop

echo "dns"
out="$(cli dns dns.php example.com)"
expect "A records" "$out" "[type] => A"
expect "example.com's null MX" "$out" "[pri] => 0"
out="$(cli dns dns.php gmail.com)"
expect "gmail's MX records, with priorities" "$out" "gmail-smtp-in.l.google.com"
out="$(cli dns -r 'var_dump(gethostbyname("no-such-name.invalid"));')"
expect "gethostbyname returns the name when it fails" "$out" 'string(20) "no-such-name.invalid"'

echo "http"
out="$(cli http http.php)"
expect "status line" "$out" "200 OK"
expect "page size" "$out" "bytes of page"

echo "request-and-response"
serve request-and-response
expect "GET shows the request" "$(curl -s "http://localhost:$PORT/hello")" "GET /hello"
out="$(curl -s -d 'name=Alice&comment=Hi' "http://localhost:$PORT/comments")"
expect "POST shows the body" "$out" "name=Alice&comment=Hi"
expect "  ...and its Content-Type" "$out" "application/x-www-form-urlencoded"
expect "the teapot" "$(curl -si "http://localhost:$PORT/teapot")" "418"
stop

echo "content-types"
serve content-types
expect "html by default" "$(curl -sI "http://localhost:$PORT/")" "text/html"
expect "plain text, charset added by PHP" "$(curl -sI "http://localhost:$PORT/?as=text")" "text/plain;charset=UTF-8"
expect "unknown bytes" "$(curl -sI "http://localhost:$PORT/?as=bytes")" "application/octet-stream"
expect "a named download" "$(curl -sI "http://localhost:$PORT/?save=1")" 'attachment; filename="hello.html"'
expect "the body never changes" "$(curl -s "http://localhost:$PORT/?as=bytes")" "<h1>Hello</h1>"
stop

echo "cookies"
serve cookies
expect "the first visit sets the cookie" "$(curl -si "http://localhost:$PORT/")" "Set-Cookie: visits=1;"
expect "  ...HttpOnly and SameSite" "$(curl -si "http://localhost:$PORT/")" "HttpOnly; SameSite=Lax"
expect "the server believes a made-up cookie" "$(curl -s -b 'visits=41' "http://localhost:$PORT/")" "Visit number 42."
expect "(int) turns nonsense into 0" "$(curl -s -b 'visits=banana' "http://localhost:$PORT/")" "Visit number 1."
stop

echo "query-strings"
serve query-strings
out="$(curl -sg "http://localhost:$PORT/?q=red+shoes&page=2&tag[]=sale&tag[]=new")"
expect "+ decodes to a space" "$out" "Searching for: red shoes"
expect "page is a number" "$out" "Page: 2"
expect "tag[] becomes an array" "$out" "Tags: sale, new"
out="$(curl -sg "http://localhost:$PORT/?q[]=x&page=banana&tag=solo")"
if grep -qE "Searching for: .+" <<< "$out"; then fail "an array where a string belongs is refused" "$out"; else pass "an array where a string belongs is refused"; fi
expect "a word for a page number falls back to 1" "$out" "Page: 1"
expect "a negative page falls back to 1" "$(curl -sg "http://localhost:$PORT/?page=-5")" "Page: 1"
stop

echo "forms"
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

echo "whats-in-the-middle"
mid() { PHP_PORT=$((PORT - 1)) PROXY_PORT=$PORT docker compose -p wnp-middle -f "$ROOT/whats-in-the-middle/compose.yml" "$@"; }
mid up -d >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
expect "directly, no X-Forwarded-For" "$(curl -s "http://localhost:$((PORT - 1))/")" "X-Forwarded-For:        (none)"
out="$(curl -s "http://localhost:$PORT/")"
if grep -qF "(none)" <<< "$out"; then fail "through the proxy, X-Forwarded-For is set" "$out"; else pass "through the proxy, X-Forwarded-For is set"; fi
expect "the Host comes through unchanged" "$out" "Host:                   localhost:$PORT"
expect "the proxy marks the answer" "$(curl -sI "http://localhost:$PORT/")" "X-Passed-Through: proxy"
expect "a forged address arrives first, the real one last" "$(curl -s -H 'X-Forwarded-For: 1.2.3.4' "http://localhost:$PORT/")" "X-Forwarded-For:        1.2.3.4, "
mid down >/dev/null 2>&1

echo "document-root"
serve document-root -t public
expect "the page reads the settings from outside public/" "$(curl -s "http://localhost:$PORT/")" "Hello from the document root."
for path in /.env /../.env; do
    out="$(curl -s --path-as-is "http://localhost:$PORT$path")"
    if grep -qF "API_KEY" <<< "$out"; then fail "-t public keeps $path private" "$out"; else pass "-t public keeps $path private"; fi
done
stop
serve document-root
expect "serving the whole project hands over .env" "$(curl -s "http://localhost:$PORT/.env")" "API_KEY="
stop

echo
if [ "$FAILED" -ne 0 ]; then echo "Some examples failed."; exit 1; fi
echo "Every example ran."
