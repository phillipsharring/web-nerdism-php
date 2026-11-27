# project-rest-api: https://phillipharrington.com/php/projects/rest-api
api() { APP_PORT=$PORT docker compose -p "$NAME-rest" -f "$ROOT/project-rest-api/compose.yml" "$@"; }
api up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT/api/tasks"
ANN='Authorization: Bearer wnp_ann_3f1c9a7e5b2d4f60'
ANNRO='Authorization: Bearer wnp_annro_8e2b7c4d1a9f3e56'
BO='Authorization: Bearer wnp_bo_6d4a2f9c1e7b3a58'
J='Content-Type: application/json'
code() { curl -s -o /dev/null -w '%{http_code}' "$@"; }

out="$(curl -si "$U")"
expect "no token: 401" "$out" "HTTP/1.1 401"
expect "  ...saying how to authenticate" "$out" 'WWW-Authenticate: Bearer realm="tasks"'
expect "a wrong token: 401, invalid_token" "$(curl -si -H 'Authorization: Bearer wnp_nope_0000000000' "$U")" 'error="invalid_token"'
expect "Basic instead of Bearer: 401" "$(code -u ann:pw "$U")" "401"
out="$(curl -s -H "$ANN" "$U")"
expect "Ann's token lists Ann's tasks" "$out" '"title": "Write the API"'
if grep -qF "Bo's private plans" <<< "$out"; then fail "  ...and only Ann's" "$out"; else pass "  ...and only Ann's"; fi
expect "Ann asking for Bo's task: 404, not 403" "$(code -H "$ANN" "$U/3")" "404"
expect "  ...the same as a task that doesn't exist" "$(code -H "$ANN" "$U/999")" "404"
expect "Bo can read his own" "$(curl -s -H "$BO" "$U/3")" '"title": "Bo'"'"'s private plans"'
out="$(curl -si -H "$ANN" -H "$J" -d '{"title": "Try the API"}' "$U")"
expect "creating: 201" "$out" "HTTP/1.1 201"
expect "  ...with a Location" "$out" "Location: /api/tasks/4"
expect "  ...and the new task" "$out" '"done": false'
expect "a read-only token can't create: 403" "$(code -H "$ANNRO" -H "$J" -d '{"title": "Nope"}' "$U")" "403"
expect "  ...but can read" "$(code -H "$ANNRO" "$U")" "200"
expect "not JSON: 415" "$(code -H "$ANN" -d 'title=x' "$U")" "415"
expect "broken JSON: 400" "$(code -H "$ANN" -H "$J" -d '{"title": ' "$U")" "400"
out="$(curl -si -H "$ANN" -H "$J" -d '{"title": "", "owner": 2}' "$U")"
expect "bad fields: 422" "$out" "HTTP/1.1 422"
expect "  ...saying which" "$out" '"owner": "Not a field a task has."'
expect "done must be true or false" "$(curl -s -H "$ANN" -H "$J" -X PATCH -d '{"done": "yes"}' "$U/2")" '"done": "true or false."'
out="$(curl -si -H "$ANN" -H "$J" -X PATCH -d '{"done": true}' "$U/2")"
expect "PATCH changes only what's sent: 200" "$out" '"done": true'
expect "  ...the title stays" "$out" '"title": "Write the client"'
expect "Ann can't PATCH Bo's task: 404" "$(code -H "$ANN" -H "$J" -X PATCH -d '{"done": true}' "$U/3")" "404"
expect "  ...and it's unchanged" "$(curl -s -H "$BO" "$U/3")" '"done": false'
expect "DELETE: 204" "$(code -H "$ANN" -X DELETE "$U/4")" "204"
expect "  ...then it's gone: 404" "$(code -H "$ANN" "$U/4")" "404"
out="$(curl -si -H "$ANN" -X PUT "$U/2")"
expect "a method the resource doesn't allow: 405" "$out" "HTTP/1.1 405"
expect "  ...with Allow" "$out" "Allow: GET, PATCH, DELETE"
expect "an unknown path: 404" "$(code -H "$ANN" "http://localhost:$PORT/api/users")" "404"
expect "an id that isn't one: 404" "$(code -H "$ANN" "$U/1%20OR%201=1")" "404"
expect "make-token makes a working token" "$(t="$(api exec -T app php bin/make-token.php bo@example.com Test)"; curl -s -o /dev/null -w '%{http_code}' -H "Authorization: Bearer $t" "$U")" "200"
expect "  ...stored only as a hash" "$(api exec -T db mysql -uapp -papp-password -N course -e "SELECT COUNT(*) FROM api_tokens WHERE token_hash LIKE 'wnp_%'" 2>/dev/null)" "0"
# Break It: the naive API
expect "naive: anyone can read Bo's tasks by asking" "$(curl -s "http://localhost:$PORT/naive.php?user_id=2")" "Bo's private plans"
out="$(curl -si "http://localhost:$PORT/naive.php?task=999")"
expect "naive: a missing task is a 200" "$out" "HTTP/1.1 200"
expect "  ...with an error inside" "$out" '{"error":"not found"}'
api down -v >/dev/null 2>&1
