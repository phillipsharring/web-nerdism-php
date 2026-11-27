# project-useful-includes: https://phillipharrington.com/php/projects/useful-includes
serve project-useful-includes -t public
home="$(curl -s "http://localhost:$PORT/")"
about="$(curl -s "http://localhost:$PORT/about.php")"
expect "the home page gets the shared header" "$home" "<title>Home | Alex Example</title>"
expect "  ...and marks itself in the nav" "$home" '<a href="/" aria-current="page">Home</a>'
expect "  ...and the shared footer" "$home" "&copy; $(date -u +%Y) Alex Example."
expect "about gets the same header" "$about" "<title>About | Alex Example</title>"
expect "  ...with About marked instead" "$about" '<a href="/about.php" aria-current="page">About</a>'
expect "  ...and a source link for itself" "$about" 'href="/source.php?file=about.php"'
expect "the source viewer shows a shared file" "$(curl -s "http://localhost:$PORT/source.php?file=functions.php")" "nav_link"
expect "  ...and nothing off its list" "$(curl -s "http://localhost:$PORT/source.php?file=config.php")" "No source for that."
for path in /src/config.php /templates/header.php; do
    out="$(curl -s "http://localhost:$PORT$path")"
    if grep -qF "site_name" <<< "$out" || grep -qF '<?=' <<< "$out"; then fail "$path is not served" "$out"; else pass "$path is not served"; fi
done
stop
