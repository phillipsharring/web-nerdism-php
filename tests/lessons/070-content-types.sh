# content-types: https://phillipharrington.com/php/content-types
serve content-types
expect "html by default" "$(curl -sI "http://localhost:$PORT/")" "text/html"
expect "plain text, charset added by PHP" "$(curl -sI "http://localhost:$PORT/?as=text")" "text/plain;charset=UTF-8"
expect "unknown bytes" "$(curl -sI "http://localhost:$PORT/?as=bytes")" "application/octet-stream"
expect "a named download" "$(curl -sI "http://localhost:$PORT/?save=1")" 'attachment; filename="hello.html"'
expect "the body never changes" "$(curl -s "http://localhost:$PORT/?as=bytes")" "<h1>Hello</h1>"
stop
