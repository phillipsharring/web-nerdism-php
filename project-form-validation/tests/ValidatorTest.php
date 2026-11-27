<?php
declare(strict_types=1);

namespace Tests;

use App\Validator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testCleanTextPasses(): void
    {
        $v = new Validator(['name' => '  Alice  ']);

        $this->assertSame('Alice', $v->text('name', 'Name', 100));
        $this->assertTrue($v->passes());
    }

    public function testMissingTextIsRequired(): void
    {
        $v = new Validator([]);

        $this->assertNull($v->text('name', 'Name', 100));
        $this->assertSame(['name' => 'Name is required.'], $v->errors());
    }

    public function testOptionalTextMayBeEmpty(): void
    {
        $v = new Validator(['note' => '']);

        $this->assertSame('', $v->text('note', 'Note', 100, required: false));
        $this->assertTrue($v->passes());
    }

    public function testLengthCountsCharactersNotBytes(): void
    {
        // Five characters, ten bytes in UTF-8.
        $v = new Validator(['name' => 'ééééé']);

        $this->assertSame('ééééé', $v->text('name', 'Name', 5));
    }

    public function testTooLongIsRefused(): void
    {
        $v = new Validator(['name' => str_repeat('x', 101)]);

        $this->assertNull($v->text('name', 'Name', 100));
        $this->assertSame('Name can be at most 100 characters.', $v->error('name'));
    }

    public function testInvalidUtf8IsRefused(): void
    {
        $v = new Validator(['name' => "caf\xE9"]);

        $this->assertNull($v->text('name', 'Name', 100));
        $this->assertSame('Name must be text.', $v->error('name'));
    }

    public function testAnArrayIsRefused(): void
    {
        // What PHP makes of name[]=Alice in a form.
        $v = new Validator(['name' => ['Alice']]);

        $this->assertNull($v->text('name', 'Name', 100));
        $this->assertSame('Name must be text.', $v->error('name'));
    }

    public function testEmail(): void
    {
        $v = new Validator(['good' => 'alice@example.com', 'bad' => 'alice@', 'sneaky' => "a@example.com\nBcc: b@example.com"]);

        $this->assertSame('alice@example.com', $v->email('good', 'Email'));
        $this->assertNull($v->email('bad', 'Email'));
        $this->assertNull($v->email('sneaky', 'Email'));
        $this->assertSame(['bad', 'sneaky'], array_keys($v->errors()));
    }

    public function testIntInRange(): void
    {
        $v = new Validator(['age' => '42']);

        $this->assertSame(42, $v->int('age', 'Age', 13, 120));
    }

    public function testIntRefusesNonsenseAndOutOfRange(): void
    {
        foreach (['12abc', '4.5', '7', '121', '0x1A', ''] as $input) {
            $v = new Validator(['age' => $input]);
            $this->assertNull($v->int('age', 'Age', 13, 120), "accepted '$input'");
        }
    }

    public function testOneOfIsAnAllowList(): void
    {
        $choices = ['standard', 'student'];

        $this->assertSame('student', (new Validator(['t' => 'student']))->oneOf('t', 'Ticket', $choices));
        $this->assertNull((new Validator(['t' => 'free']))->oneOf('t', 'Ticket', $choices));
        $this->assertNull((new Validator(['t' => 'Student']))->oneOf('t', 'Ticket', $choices));
    }

    public function testTheFirstErrorPerFieldWins(): void
    {
        $v = new Validator(['email' => '']);
        $v->email('email', 'Email');

        $this->assertSame('Email is required.', $v->error('email'));
    }
}
