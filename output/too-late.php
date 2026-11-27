<?php
echo "Some output first.\n";

// Too late: the headers went out with the first byte of output.
header('Location: /');

echo "Still here. The redirect never happened.\n";
