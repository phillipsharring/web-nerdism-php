# request-and-response: https://phillipharrington.com/php/request-and-response
serve request-and-response
expect "GET shows the request" "$(curl -s "http://localhost:$PORT/hello")" "GET /hello"
out="$(curl -s -d 'name=Alice&comment=Hi' "http://localhost:$PORT/comments")"
expect "POST shows the body" "$out" "name=Alice&comment=Hi"
expect "  ...and its Content-Type" "$out" "application/x-www-form-urlencoded"
expect "the teapot" "$(curl -si "http://localhost:$PORT/teapot")" "418"
stop
