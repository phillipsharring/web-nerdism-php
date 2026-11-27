# project-layout: https://phillipharrington.com/php/projects/layout
serve project-layout -t public
home="$(curl -s "http://localhost:$PORT/")"
expect "the home page is wrapped in the layout" "$home" "<title>Home | Alex Example</title>"
expect "  ...its own content inside <main>, as HTML" "$home" "<h1>Hello</h1>"
expect "  ...and the layout's footer" "$home" "&copy; $(date -u +%Y) Alex Example."
about="$(curl -s "http://localhost:$PORT/about")"
expect "about gets the same layout" "$about" "<title>About | Alex Example</title>"
expect "  ...marked in the nav" "$about" '<a href="/about" aria-current="page">About</a>'
expect "  ...and its template can use the settings" "$about" "<h1>About Alex Example</h1>"
out="$(curl -si "http://localhost:$PORT/nowhere")"
expect "an unknown path is a 404" "$out" "HTTP/1.1 404"
expect "  ...inside the same layout" "$out" "<title>Not Found | Alex Example</title>"
expect "a path that looks like a file is just a 404" "$(curl -si --path-as-is "http://localhost:$PORT/../src/config")" "404"
stop
