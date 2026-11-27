<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class VerificationTest extends TestCase
{
    private const SECRET = 'not-a-real-secret';
    private const NOW = 1_800_000_000;

    private function link(): string
    {
        return verificationLink('ada@example.com', self::NOW + 3600, self::SECRET, 'https://example.com');
    }

    public function testAFreshLinkGivesBackTheEmail(): void
    {
        $this->assertSame('ada@example.com', verifiedEmail($this->link(), self::NOW, self::SECRET));
    }

    public function testALinkStillWorksUpToTheLastSecond(): void
    {
        $this->assertSame('ada@example.com', verifiedEmail($this->link(), self::NOW + 3600, self::SECRET));
    }

    public function testAnExpiredLinkIsRefused(): void
    {
        $this->assertNull(verifiedEmail($this->link(), self::NOW + 3601, self::SECRET));
    }

    public function testALinkMadeWithAnotherSecretIsRefused(): void
    {
        $this->assertNull(verifiedEmail($this->link(), self::NOW, 'some-other-secret'));
    }

    // Never trust input: each of these is a link someone edited, or never had.
    public static function tamperedLinks(): array
    {
        return [
            'changed email' => ['https://example.com/verify?email=eve%40example.com&expires=1800003600&sig=abc'],
            'no signature' => ['https://example.com/verify?email=ada%40example.com&expires=1800003600'],
            'email as an array' => ['https://example.com/verify?email[]=ada%40example.com&expires=1800003600&sig=abc'],
            'expiry not a number' => ['https://example.com/verify?email=ada%40example.com&expires=soon&sig=abc'],
            'nothing at all' => [''],
        ];
    }

    #[DataProvider('tamperedLinks')]
    public function testATamperedLinkIsRefused(string $url): void
    {
        $this->assertNull(verifiedEmail($url, self::NOW, self::SECRET));
    }

    public function testChangingTheEmailInARealLinkIsRefused(): void
    {
        $edited = str_replace('ada', 'eve', $this->link());

        $this->assertNull(verifiedEmail($edited, self::NOW, self::SECRET));
    }
}
