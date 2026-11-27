# autoloading: https://phillipharrington.com/php/autoloading
out="$(cli autoloading by-hand.php)"
expect "the hand-written autoloader finds App\\Greeter" "$out" "(loading /app/src/Greeter.php)"
expect "  ...and App\\Text\\Shout, a folder down" "$out" "(loading /app/src/Text/Shout.php)"
expect "  ...and the code runs" "$out" "HELLO, ADA!"
docker run --rm -v "$ROOT/autoloading":/app -w /app composer:2 composer install -q 2>&1
out="$(cli autoloading hello.php)"
expect "Composer's autoloader does the same" "$out" "HELLO, ADA!"
expect "  ...UTF-8 and all" "$out" "HELLO, ZOË!"
expect "composer run runs the script" "$(docker run --rm -v "$ROOT/autoloading":/app -w /app composer:2 composer run hello 2>&1)" "HELLO, ADA!"
expect "a class with no file isn't found" "$(cli autoloading -r 'require "vendor/autoload.php"; new App\Missing();')" 'Class "App\Missing" not found'
