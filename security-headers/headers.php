<?php
// The headers every page sends. Call it before any output.
declare(strict_types=1);

function sendSecurityHeaders(): void
{
    // What the page may load and run. Scripts only from files on this site,
    // so a script injected into the page is refused. No framing by anyone.
    header("Content-Security-Policy: default-src 'self'; script-src 'self'; object-src 'none'; "
        . "base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
    // The same "no framing," for browsers too old for frame-ancestors.
    header('X-Frame-Options: DENY');
    // Use the Content-Type as sent. Never guess that a file is HTML or script.
    header('X-Content-Type-Options: nosniff');
    // Tell other sites which site a visitor came from, not which page.
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // Features this site never uses, turned off.
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    // HTTPS only, for a year, from the first visit on. Browsers ignore it over
    // plain HTTP, so it's harmless on localhost.
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    // Don't advertise the PHP version.
    header_remove('X-Powered-By');
}
