<?php
declare(strict_types=1);

namespace Tests;

use App\MemoryMailer;
use App\Welcome;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class WelcomeTest extends TestCase
{
    public function testItSendsOneEmailToTheNewUser(): void
    {
        // Arrange: a fake mailer that keeps what it's given.
        $mailer = new MemoryMailer();
        $welcome = new Welcome($mailer);

        // Act.
        $welcome->send('ada@example.com', 'Ada');

        // Assert.
        $this->assertCount(1, $mailer->sent);
        $this->assertSame('ada@example.com', $mailer->sent[0]['to']);
        $this->assertStringContainsString('Hi Ada', $mailer->sent[0]['body']);
    }

    public function testLineBreaksInANameAreFlattened(): void
    {
        $mailer = new MemoryMailer();

        (new Welcome($mailer))->send('ada@example.com', "Ada\r\nBcc: eve@example.com");

        $this->assertStringNotContainsString("\n", $mailer->sent[0]['body']);
    }

    public function testItRefusesSomethingThatIsNotAnEmailAddress(): void
    {
        $mailer = new MemoryMailer();

        $this->expectException(InvalidArgumentException::class);

        (new Welcome($mailer))->send('not an email', 'Ada');
    }
}
