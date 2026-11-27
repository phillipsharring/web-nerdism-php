# dry: https://phillipharrington.com/php/dry
expect "before: the updated page has the new rule" "$(cli dry before/cart.php)" "Cart total:    45.00"
expect "  ...the email still has the old one" "$(cli dry before/email.php)" "Email total:   50.99"
expect "  ...and so does the invoice" "$(cli dry before/invoice.php)" "Invoice total: 50.99"
expect "after: the cart" "$(cli dry after/cart.php)" "Cart total:    45.00"
expect "  ...the email agrees" "$(cli dry after/email.php)" "Email total:   45.00"
expect "  ...and so does the invoice" "$(cli dry after/invoice.php)" "Invoice total: 45.00"
