<?php
$host = $argv[1] ?? 'phillipharrington.com';

echo gethostbyname($host), "\n";
print_r(dns_get_record($host, DNS_A));
print_r(dns_get_record($host, DNS_MX));
