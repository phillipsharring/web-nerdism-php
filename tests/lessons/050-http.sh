# http: https://phillipharrington.com/php/http
out="$(cli http http.php)"
expect "status line" "$out" "200 OK"
expect "page size" "$out" "bytes of page"
