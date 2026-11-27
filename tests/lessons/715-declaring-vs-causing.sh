# declaring-vs-causing: https://phillipharrington.com/php/declaring-vs-causing
rm -f "$ROOT"/declaring-vs-causing/{before,after}/visits.log
out="$(cli declaring-vs-causing before/slugs.php)"
expect "before: the job gets its slugs" "$out" "ten-tips-for-php"
expect "  ...and a nav bar it never asked for" "$out" "<nav>"
if [ -f "$ROOT/declaring-vs-causing/before/visits.log" ]; then pass "  ...and a visit logged by a command-line job"; else fail "  ...and a visit logged by a command-line job" "no visits.log"; fi
out="$(cli declaring-vs-causing after/slugs.php)"
if [ "$out" = "$(printf 'hello-world\nten-tips-for-php')" ]; then pass "after: the job gets only its slugs"; else fail "after: the job gets only its slugs" "$out"; fi
if [ -f "$ROOT/declaring-vs-causing/after/visits.log" ]; then fail "  ...and logs nothing" "visits.log exists"; else pass "  ...and logs nothing"; fi
out="$(cli declaring-vs-causing -r 'require "after/text.php";')"
if [ -z "$out" ]; then pass "requiring the declaring file is silent"; else fail "requiring the declaring file is silent" "$out"; fi
expect "the page does the doing" "$(cli declaring-vs-causing after/page.php)" '<a href="/posts/hello-world">'
rm -f "$ROOT"/declaring-vs-causing/{before,after}/visits.log
