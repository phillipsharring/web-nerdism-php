# csv: https://phillipharrington.com/php/csv
out="$(cli csv import.php shirts.csv)"
expect "a plain row" "$out" "TS-01: Plain tee, 1500 cents"
expect "a comma inside quotes stays in the field" "$out" "TS-02: Tee, with pocket, 1800 cents"
expect "doubled quotes are one quote" "$out" 'TS-03: The "Classic" tee, 2000 cents'
expect "UTF-8 comes through" "$out" "TS-04: Café tee, 2200 cents"
expect "a bad price is skipped" "$out" "Line 6: bad price or text, skipped."
expect "a short row is skipped" "$out" "Line 7: wrong number of fields, skipped."
if grep -q "Deprecated\|Warning" <<< "$out"; then fail "runs clean" "$out"; else pass "runs clean"; fi
expect "the wrong columns are refused" "$(cli csv import.php README.md)" "Expected the columns sku, name, price_cents."
serve csv -t public
out="$(curl -si "http://localhost:$PORT/export.php")"
expect "the export is CSV" "$out" "Content-Type: text/csv; charset=utf-8"
expect "  ...as a download" "$out" 'Content-Disposition: attachment; filename="shirts.csv"'
expect "  ...with a header row" "$out" "sku,name,price_cents"
expect "  ...quotes around a comma" "$out" 'TS-02,"Tee, with pocket",1800'
expect "  ...and doubled quotes inside" "$out" 'TS-03,"The ""Classic"" tee",2000'
stop
