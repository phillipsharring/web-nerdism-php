<?php
// BROKEN ON PURPOSE, for the Break It section. Never put this on a server.
// It joins whatever name it's given onto the logs folder and sends the file.
header('Content-Type: text/plain; charset=utf-8');
readfile(dirname(__DIR__) . '/logs/' . $_GET['file']);
