<?php
// BROKEN ON PURPOSE, for the Break It section. Never write a client like this.
// The token is in the code, so it's in git, in every copy of the repository,
// and in every backup. And it goes in the URL, so it's in the API's logs.
$token = 'wnp_live_7d3f9a2c8e1b4f6a';

echo file_get_contents("http://api:8000/tasks?api_key=$token");
