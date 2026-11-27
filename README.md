# Web Nerdism w/ PHP 101: the examples

The code from the Try It sections of
[Web Nerdism w/ PHP 101](https://phillipharrington.com/php), one folder per lesson.

Everything runs in Docker, so there's nothing to install but Docker itself
([Tools](https://phillipharrington.com/php/tools) has the setup). Each folder's
README has the exact command, run from inside that folder.

| Folder | Lesson |
| --- | --- |
| [client-server](client-server) | [Client/Server](https://phillipharrington.com/php/client-server) |
| [anatomy-of-a-url](anatomy-of-a-url) | [Anatomy of a URL](https://phillipharrington.com/php/anatomy-of-a-url) |
| [dns](dns) | [DNS](https://phillipharrington.com/php/dns) |
| [http](http) | [HTTP, HTTPS and TLS](https://phillipharrington.com/php/http) |
| [request-and-response](request-and-response) | [Request and Response](https://phillipharrington.com/php/request-and-response) |
| [content-types](content-types) | [Content Types](https://phillipharrington.com/php/content-types) |
| [cookies](cookies) | [Cookies](https://phillipharrington.com/php/cookies) |
| [query-strings](query-strings) | [Query Strings](https://phillipharrington.com/php/query-strings) |
| [forms](forms) | [Forms](https://phillipharrington.com/php/forms) |

## Every example runs

`tests/run.sh` runs every example the way its lesson says to and checks the
output. GitHub Actions runs it on every push, and weekly. The lesson pages
don't keep their own copy: the site's build reads these files and puts them
on the page, so what you read is what was tested.

## License

MIT. Take it, change it, use it.
