# configuration-and-the-environment: https://phillipharrington.com/php/configuration-and-the-environment
dir="$ROOT/configuration-and-the-environment"
serve_env() { # serve_env [DOCKER RUN ARGS...]
    docker rm -f "$NAME" >/dev/null 2>&1
    docker run -d --name "$NAME" -p "$PORT:8000" "$@" -v "$dir":/app -w /app "$IMAGE" \
        php -S 0.0.0.0:8000 -t public >/dev/null
    for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && return 0; sleep 0.5; done
}
work="$(mktemp -d)"
cp "$dir/.env.example" "$work/.env"
sed -i.bak 's/^MAIL_API_KEY=$/MAIL_API_KEY=test-key-1234/' "$work/.env"
serve_env --env-file "$work/.env"
out="$(curl -s "http://localhost:$PORT/")"
expect "settings come from the .env file" "$out" "Shirt Shop, running in development."
expect "  ...and the secret is there" "$out" "Mail key: set, ending in 1234"
if grep -qF "test-key-1234" <<< "$out"; then fail "  ...but never printed whole" "$out"; else pass "  ...but never printed whole"; fi
serve_env --env-file "$work/.env" -e APP_ENV=production
expect "-e overrides the file" "$(curl -s "http://localhost:$PORT/")" "running in production."
serve_env
expect "with no settings, it refuses" "$(curl -s -w '%{http_code}' "http://localhost:$PORT/")" "This app isn't set up yet."$'\n'"500"
expect "  ...and says why in the log" "$(docker logs "$NAME" 2>&1)" "MAIL_API_KEY is not set."
stop
printf 'APP_NAME="Quoted"\nMAIL_API_KEY=key-5678\n' > "$work/quoted.env"
serve_env --env-file "$work/quoted.env"
expect "docker keeps quotes as part of the value" "$(curl -s "http://localhost:$PORT/")" '"Quoted", running in'
stop
cp "$dir/.gitignore" "$dir/.env.example" "$work/"
git -C "$work" init -q
status="$(git -C "$work" status --porcelain --untracked-files=all)"
expect "git sees .env.example" "$status" ".env.example"
if grep -qE '(^| )\.env$' <<< "$status"; then fail "  ...but not .env" "$status"; else pass "  ...but not .env"; fi
rm -rf "$work"
