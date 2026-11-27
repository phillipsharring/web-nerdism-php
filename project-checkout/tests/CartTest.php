<?php
declare(strict_types=1);

namespace Shop\Tests;

use PHPUnit\Framework\TestCase;
use Shop\Cart;

final class CartTest extends TestCase
{
    public function testAddingTheSameProductAddsUp(): void
    {
        $cart = new Cart();
        $cart->add(7, 2);
        $cart->add(7);
        $this->assertSame([7 => 3], $cart->items());
        $this->assertSame(3, $cart->count());
    }

    public function testSettingZeroRemovesTheLine(): void
    {
        $cart = new Cart([7 => 2, 8 => 1]);
        $cart->set(7, 0);
        $this->assertSame([8 => 1], $cart->items());
    }

    public function testNegativeQuantitiesNeverMakeANegativeLine(): void
    {
        $cart = new Cart([7 => 2]);
        $cart->set(7, -5);
        $cart->add(8, -3);
        $this->assertTrue($cart->isEmpty());
    }

    public function testQuantitiesAreCapped(): void
    {
        $cart = new Cart();
        $cart->add(7, 1000000);
        $this->assertSame([7 => Cart::MAX_QUANTITY], $cart->items());
    }

    public function testTheNumberOfLinesIsCapped(): void
    {
        $cart = new Cart();
        for ($id = 1; $id <= Cart::MAX_LINES + 10; $id++) {
            $cart->add($id);
        }
        $this->assertCount(Cart::MAX_LINES, $cart->items());
    }

    public function testJunkInTheSessionIsDropped(): void
    {
        $cart = new Cart([7 => 2, 'x' => 1, 8 => '3', 9 => 1.5, 0 => 4, 10 => 1]);
        $this->assertSame([7 => 2, 10 => 1], $cart->items());
        $this->assertTrue((new Cart('not an array'))->isEmpty());
    }
}
