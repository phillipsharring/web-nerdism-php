<?php
declare(strict_types=1);

/** The languages this site speaks, each named in its own language. */
const LANGUAGES = ['en' => 'English', 'fr' => 'Français', 'pl' => 'Polski', 'ja' => '日本語'];
const DEFAULT_LANGUAGE = 'en';

/**
 * Picks a language from an Accept-Language header, like
 * "fr-CA,fr;q=0.9,en;q=0.5". Never trust input: this is a header, so anything
 * malformed is skipped, and only a language on our list can win.
 */
function negotiateLanguage(string $header): string
{
    $wanted = [];
    foreach (explode(',', $header) as $i => $part) {
        $pieces = explode(';', trim($part));
        $tag = strtolower(trim($pieces[0]));
        if (!preg_match('/^[a-z]{1,8}(-[a-z0-9]{1,8})*$/', $tag)) {
            continue;
        }
        $q = 1.0;
        if (isset($pieces[1]) && preg_match('/^\s*q=([01](\.\d{0,3})?)\s*$/', $pieces[1], $m)) {
            $q = (float) $m[1];
        }
        if ($q > 0 && $q <= 1) {
            $wanted[] = ['tag' => $tag, 'q' => $q, 'order' => $i];
        }
    }

    // Highest q first; ties keep the order they were sent in.
    usort($wanted, fn ($a, $b) => [$b['q'], $a['order']] <=> [$a['q'], $b['order']]);

    foreach ($wanted as $w) {
        $primary = explode('-', $w['tag'])[0]; // fr-CA counts as fr
        if (isset(LANGUAGES[$primary])) {
            return $primary;
        }
    }

    return DEFAULT_LANGUAGE;
}

/**
 * Which plural form a whole number takes in a language: the CLDR category
 * names, simplified to whole numbers.
 */
function pluralCategory(string $lang, int $n): string
{
    return match ($lang) {
        'fr' => $n === 0 || $n === 1 ? 'one' : 'other',
        'pl' => match (true) {
            $n === 1 => 'one',
            in_array($n % 10, [2, 3, 4], true) && !in_array($n % 100, [12, 13, 14], true) => 'few',
            default => 'many',
        },
        'ja' => 'other',
        default => $n === 1 ? 'one' : 'other', // English and many others
    };
}

/**
 * Looks up a message and fills in {placeholders}. A message with plural
 * forms picks one by $vars['count'].
 *
 * @param array<string, string> $messages
 * @param array<string, int|string> $vars
 */
function translate(array $messages, string $lang, string $key, array $vars = []): string
{
    $message = $messages[$key] ?? $key;
    if (is_array($message)) {
        $message = $message[pluralCategory($lang, (int) ($vars['count'] ?? 0))] ?? reset($message);
    }
    foreach ($vars as $name => $value) {
        $message = str_replace('{' . $name . '}', (string) $value, $message);
    }

    return $message;
}
