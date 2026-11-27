#!/usr/bin/env bash
#
# Runs every example the way its lesson says to, and checks the output.
# Needs Docker and curl. Run from anywhere:
#
#   bash tests/run.sh              every lesson
#   bash tests/run.sh dns cookies  just those (by folder name)
#
# Each lesson's checks live in tests/lessons/NNN-<folder>.sh, numbered in
# course order. They use the helpers below: serve, cli, stop, expect, pass,
# fail, and $PORT for the server they start.
#
# Servers run detached (-d) on $PORT (default 8099) instead of the lessons'
# -it on 8000, so the script can talk to them and stop them; everything else
# matches. PORT and NAME (the container name) can be set, so several runs can
# go at once without colliding: PORT=8120 NAME=wnp-b bash tests/run.sh dns
set -uo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
IMAGE=php:8.4-cli
PORT="${PORT:-8099}"
NAME="${NAME:-wnp-test}"
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
    docker rm -f "$NAME" >/dev/null 2>&1
    docker run -d --name "$NAME" -p "$PORT:8000" -v "$ROOT/$dir":/app -w /app "$IMAGE" \
        php -S 0.0.0.0:8000 "$@" >/dev/null
    for _ in $(seq 1 30); do
        curl -s -o /dev/null "http://localhost:$PORT/" && return 0
        sleep 0.5
    done
    echo "server in $dir never answered"; return 1
}
stop() { docker rm -f "$NAME" >/dev/null 2>&1; }
trap stop EXIT

docker pull -q "$IMAGE" >/dev/null

shopt -s nullglob
files=()
if [ "$#" -eq 0 ]; then
    files=("$ROOT"/tests/lessons/*.sh)
else
    for name in "$@"; do
        match=("$ROOT"/tests/lessons/*-"$name".sh)
        [ "${#match[@]}" -eq 1 ] || { echo "no test file for '$name'"; exit 1; }
        files+=("${match[0]}")
    done
fi

for f in "${files[@]}"; do
    name="$(basename "$f" .sh)"
    echo "${name#*-}"
    # shellcheck disable=SC1090
    . "$f"
    stop
done

echo
if [ "$FAILED" -ne 0 ]; then echo "Some examples failed."; exit 1; fi
echo "Every example ran."
