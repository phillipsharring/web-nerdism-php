# abstract-classes: https://phillipharrington.com/php/abstract-classes
out="$(cli abstract-classes reports.php)"
expect "one layout for every report" "$out" "Sales
====="
expect "  ...with each report's own rows" "$out" "November    \$1,610.99"
expect "  ...and the other's" "$out" "This week          42"
expect "an abstract class can't be made" "$(cli abstract-classes -r 'require "Report.php"; new Report();')" "Cannot instantiate abstract class Report"
expect "a subclass must finish the job" "$(cli abstract-classes broken.php)" "Class RefundReport contains 1 abstract method and must therefore be declared abstract or implement the remaining methods (Report::rows)"
