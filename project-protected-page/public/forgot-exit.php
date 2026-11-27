<?php
// A broken guard, for the Gotchas: it redirects, but forgets to stop.
// Don't copy this one.
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: /login.php');
}
?>
<h1>Members Only</h1>
<p>The meeting is Thursday at 7.</p>
