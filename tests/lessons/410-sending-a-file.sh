# sending-a-file: https://phillipharrington.com/php/sending-a-file
expect "make-big-file.php writes the big file" "$(cli sending-a-file make-big-file.php)" "bytes to files/big.csv."
serve sending-a-file -d memory_limit=16M -t public
out="$(curl -sI "http://localhost:$PORT/download.php?file=report")"
expect "a download is labeled as one" "$out" 'Content-Disposition: attachment; filename="sales-report.csv"'
expect "  ...with its type" "$out" "Content-Type: text/csv; charset=utf-8"
expect "  ...and its size" "$out" "Content-Length: $(wc -c < "$ROOT/sending-a-file/files/report.csv" | tr -d ' ')"
expect "a UTF-8 name gets filename*" "$(curl -sI "http://localhost:$PORT/download.php?file=menu")" "filename*=UTF-8''caf%C3%A9-menu.csv"
size="$(wc -c < "$ROOT/sending-a-file/files/big.csv" | tr -d ' ')"
got="$(curl -s "http://localhost:$PORT/download.php?file=big" | wc -c | tr -d ' ')"
if [ "$got" = "$size" ]; then pass "readfile() streams a 40 MB file under a 16 MB limit"; else fail "readfile() streams a 40 MB file under a 16 MB limit" "got $got of $size bytes"; fi
expect "file_get_contents() runs out of memory" "$(curl -s "http://localhost:$PORT/all-at-once.php" | tail -c 400)" "Allowed memory size"
for f in ../.gitignore ../files/report.csv report.csv; do
    expect "?file=$f is refused" "$(curl -si --get --data-urlencode "file=$f" "http://localhost:$PORT/download.php")" "404 Not Found"
done
stop
rm -f "$ROOT/sending-a-file/files/big.csv"
