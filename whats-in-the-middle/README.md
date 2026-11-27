# What's in the Middle

Lesson: https://phillipharrington.com/php/whats-in-the-middle

A reverse proxy (nginx) in front of PHP's built-in server. Start both:

```
docker compose up
```

Then, from a second terminal:

```
curl http://localhost:8001                                    # PHP directly
curl http://localhost:8000                                    # through the proxy
curl -H 'X-Forwarded-For: 1.2.3.4' http://localhost:8000      # lie to it
```

Ctrl+C stops both. (`PHP_PORT` and `PROXY_PORT` change the ports, if 8000 or
8001 are taken.)
