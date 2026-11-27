# project-translation: https://phillipharrington.com/php/projects/translation
tr_() { APP_PORT=$PORT docker compose -p "$NAME-translation" -f "$ROOT/project-translation/compose.yml" "$@"; }
tr_ up -d --build --wait >/dev/null 2>&1
for _ in $(seq 1 30); do curl -s -o /dev/null "http://localhost:$PORT/" && break; sleep 0.5; done
U="http://localhost:$PORT/"
out="$(curl -si "$U")"
expect "no header: English" "$out" '<html lang="en">'
expect "  ...sent as UTF-8" "$out" "Content-Type: text/html; charset=utf-8"
expect "  ...with Content-Language" "$out" "Content-Language: en"
expect "  ...and Vary, for caches" "$out" "Vary: Accept-Language, Cookie"
expect "  ...three products, plural" "$out" "3 products"
out="$(curl -s -H 'Accept-Language: fr-CA,fr;q=0.9,en;q=0.5' "$U")"
expect "fr-CA counts as French" "$out" '<html lang="fr">'
expect "  ...names from the database, accents intact" "$out" "Thé vert"
expect "  ...and the non-breaking space before !" "$out" "Bienvenue"$' '"!"
expect "q values decide, not order" "$(curl -s -H 'Accept-Language: en;q=0.2, pl;q=0.8' "$U")" '<html lang="pl">'
expect "Polish: 3 is 'few'" "$(curl -s -H 'Accept-Language: pl' "$U")" "3 produkty"
out="$(curl -s -H 'Accept-Language: ja' "$U")"
expect "Japanese, multibyte from the database" "$out" "緑茶"
expect "  ...a missing translation falls back to English" "$out" "<li>Honey</li>"
expect "an unknown language gets English" "$(curl -s -H 'Accept-Language: de-DE,de;q=0.9' "$U")" '<html lang="en">'
expect "a header full of junk gets English" "$(curl -s -H 'Accept-Language: <script>, ../../etc/passwd;q=1' "$U")" '<html lang="en">'
expect "?lang= with a value not on the list is ignored" "$(curl -s "$U?lang=../../etc/passwd")" '<html lang="en">'
expect "?lang=fr sets a cookie" "$(curl -si "$U?lang=fr")" "Set-Cookie: lang=fr"
expect "  ...which beats Accept-Language next time" "$(curl -s -H 'Cookie: lang=fr' -H 'Accept-Language: ja' "$U")" '<html lang="fr">'
expect "a forged cookie is ignored" "$(curl -s -H 'Cookie: lang=xx' -H 'Accept-Language: pl' "$U")" '<html lang="pl">'
expect "each language name is marked with its own lang" "$(curl -s "$U")" 'hreflang="ja" lang="ja">日本語</a>'
out="$(tr_ exec -T app php plurals.php 2>&1)"
expect "plurals: French 0 is singular" "$out" "0 produit |"
expect "plurals: Polish 2 few, 5 many, 12 many, 22 few" "$out" "2 produkty | 5 produktów | 12 produktów | 22 produkty"
expect "plurals: Japanese has one form" "$out" "商品1点 | 商品2点"
tr_ down -v >/dev/null 2>&1
