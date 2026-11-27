<?php
$url = $argv[1] ?? 'https://www.example.com:8080/blog/post?sort=new&page=2#comments';

print_r(parse_url($url));
