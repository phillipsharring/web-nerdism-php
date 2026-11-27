# interfaces: https://phillipharrington.com/php/interfaces
out="$(cli interfaces remind.php)"
expect "one notifier prints" "$out" "To ada@example.com: Your trial ends tomorrow."
expect "  ...the other keeps, and counts" "$out" "Kept 2 messages instead of sending them."
expect "the right method isn't enough without implements" "$(cli interfaces wrong.php)" "remindAll(): Argument #1 (\$notifier) must be of type Notifier, Pager given"
expect "an interface can't be made" "$(cli interfaces -r 'require "Notifier.php"; new Notifier();')" "Cannot instantiate interface Notifier"
