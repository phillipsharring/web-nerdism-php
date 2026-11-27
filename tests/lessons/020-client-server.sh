# client-server: https://phillipharrington.com/php/client-server
serve client-server
expect "the server says hello" "$(curl -s "http://localhost:$PORT/")" "Hello from the server."
stop
