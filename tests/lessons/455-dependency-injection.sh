# dependency-injection: https://phillipharrington.com/php/dependency-injection
docker run --rm -v "$ROOT/dependency-injection":/app -w /app composer:2 composer install -q 2>&1
out="$(cli dependency-injection by-hand.php)"
expect "wired by hand, Welcome uses the mailer it's given" "$out" "To: ada@example.com
Subject: Welcome to Example"
expect "  ...and a different one, unchanged" "$out" "Kept 1 email, to grace@example.com. Nothing sent."
expect "the container does the wiring" "$(cli dependency-injection container.php)" "Hi Katherine, thanks for signing up."
expect "never trust input: a bad address is refused" "$(cli dependency-injection -r 'require "vendor/autoload.php"; (new App\Welcome(new App\MemoryMailer()))->send("not an email", "Ada");')" "That is not an email address."
expect "  ...and line breaks in a name are flattened" "$(cli dependency-injection -r 'require "vendor/autoload.php"; $m = new App\MemoryMailer(); (new App\Welcome($m))->send("ada@example.com", "Ada\r\nBcc: x@example.com"); echo $m->sent[0]["body"];')" "Hi Ada Bcc: x@example.com, thanks"
expect "autowiring can't guess an interface" "$(cli dependency-injection -r 'require "vendor/autoload.php"; (new DI\ContainerBuilder())->build()->get(App\Welcome::class);')" 'Entry "App\Mailer" cannot be resolved: the class is not instantiable'
