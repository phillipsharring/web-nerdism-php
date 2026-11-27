# Sessions

Lesson: https://phillipharrington.com/php/sessions

Start the server, with a name so you can look inside it later:

```
docker run --rm -it --name sessions -p 8000:8000 -v "$PWD":/app -w /app php:8.4-cli php -S 0.0.0.0:8000
```

Visit twice, keeping the cookie in a jar:

```
curl -c jar.txt -b jar.txt http://localhost:8000
curl -c jar.txt -b jar.txt http://localhost:8000
```

Then read the file on the server (use the id the page printed):

```
docker exec sessions cat /tmp/sess_YOUR_ID
```

Try a made-up id, and PHP refuses it:

```
curl -i -b 'PHPSESSID=madeup' http://localhost:8000
```
