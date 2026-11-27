<?php
// BROKEN ON PURPOSE, for the Break It section. Never put this on a server.
// It glues the values together with commas: no quoting, no formula check.
require dirname(__DIR__) . '/src/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="contacts.csv"');
echo "Name,Email,Company,Spent\n";
foreach (connect()->query('SELECT name, email, company, spent_cents FROM contacts ORDER BY created_at') as $row) {
    echo $row['name'], ',', $row['email'], ',', $row['company'], ',', $row['spent_cents'] / 100, "\n";
}
