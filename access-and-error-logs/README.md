# Access and Error Logs

Lesson: https://phillipharrington.com/php/access-and-error-logs

nginx in front of PHP's built-in server. Both write their logs into `logs/`,
outside the web root (`public/`). Start them:

```
docker compose up
```

In a second terminal, follow the access log, then make some requests from a
third:

```
tail -f logs/access.log
```

```
curl http://localhost:8000/
curl http://localhost:8000/nope                    # a 404
curl http://localhost:8000/broken                  # a warning, in logs/php-error.log
curl -A 'Not a browser' http://localhost:8000/     # the user agent is whatever you say
```

`docker compose stop php`, then `curl http://localhost:8000/` again, puts nginx's
side of the story in `logs/error.log`. `docker compose start php` brings it back.
Ctrl+C stops both. (`PROXY_PORT` changes the port, if 8000 is taken.)
