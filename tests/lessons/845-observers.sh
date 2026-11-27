# observers: https://phillipharrington.com/php/observers
out="$(cli observers before/app.php)"
expect "before: the signup does it all itself" "$out" "  added alice@example.com to the newsletter"
after="$(cli observers after/app.php)"
for line in "Saved the account for alice@example.com" "  welcome email to alice@example.com" "  added alice@example.com to the newsletter" "  audit log: signup alice@example.com"; do
    expect "after: $line" "$after" "$line"
done
if grep -qF "added bob@example.com" <<< "$after"; then fail "  ...bob didn't ask for the newsletter" "$after"; else pass "  ...bob didn't ask for the newsletter"; fi
expect "  ...a new listener joined" "$after" "  told the team: bob@example.com signed up"
if grep -qE "SendWelcomeEmail|AddToNewsletter|WriteAuditLog|welcome email|audit log|the team" "$ROOT/observers/after/Signup.php"; then fail "  ...and Signup.php knows none of them" "$(cat "$ROOT/observers/after/Signup.php")"; else pass "  ...and Signup.php knows none of them"; fi
