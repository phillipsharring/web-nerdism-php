# singletons: https://phillipharrington.com/php/singletons
out="$(cli singletons before/tests.php)"
expect "before: the first test passes" "$out" "ok    alice gets one welcome email"
expect "  ...and the same test, run second, fails: state leaked" "$out" "FAIL  bob gets one welcome email"
out="$(cli singletons after/tests.php)"
expect "after: the first test passes" "$out" "ok    alice gets one welcome email"
expect "  ...and so does the second" "$out" "ok    bob gets one welcome email"
expect "the app still uses one mailer" "$(cli singletons after/app.php)" "2 emails, one mailer:"
expect "new Mailer() is refused for the singleton" "$(cli singletons -r 'require "before/Mailer.php"; new Mailer();')" "Call to private Mailer::__construct()"
