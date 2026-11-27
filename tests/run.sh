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

serve() { # serve DIR
    docker rm -f wnp-test >/dev/null 2>&1
    docker run -d --name wnp-test -p "$PORT:8000" -v "$ROOT/$1":/app -w /app "$IMAGE" \
        php -S 0.0.0.0:8000 >/dev/null
    for _ in $(seq 1 30); do
        curl -s -o /dev/null "http://localhost:$PORT/" && return 0
        sleep 0.5
    done
    echo "server in $1 never answered"; return 1
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

echo
if [ "$FAILED" -ne 0 ]; then echo "Some examples failed."; exit 1; fi
echo "Every example ran."
