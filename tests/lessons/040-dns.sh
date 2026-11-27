# dns: https://phillipharrington.com/php/dns
out="$(cli dns dns.php example.com)"
expect "A records" "$out" "[type] => A"
expect "example.com's null MX" "$out" "[pri] => 0"
out="$(cli dns dns.php gmail.com)"
expect "gmail's MX records, with priorities" "$out" "gmail-smtp-in.l.google.com"
out="$(cli dns -r 'var_dump(gethostbyname("no-such-name.invalid"));')"
expect "gethostbyname returns the name when it fails" "$out" 'string(20) "no-such-name.invalid"'
