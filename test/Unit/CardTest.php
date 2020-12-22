<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Card;
use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCorrectPropertiesWithIntegerValue(): void
    {
        $card = new Card('S', 2);
        $this->assertSame('S', $card->suit());
        $this->assertSame('2', $card->type());
        $this->assertSame(2, $card->value());
    }

    public function testCorrectPropertiesWithIntegerValueAsString(): void
    {
        $card = new Card('C', 9);
        $this->assertSame('C', $card->suit());
        $this->assertSame('9', $card->type());
        $this->assertSame(9, $card->value());
    }

    public function testCorrectPropertiesWithCourtValue(): void
    {
        $card = new Card('D', 13);  // 13 = King
        $this->assertSame('D', $card->suit());
        $this->assertSame('K', $card->type());
        $this->assertSame(13, $card->value());
    }

    public function testZeroPointsForOtherCards(): void
    {
        $card = new Card('D', 12);
        $this->assertSame(0, $card->points());

        $card = new Card('C', 10);
        $this->assertSame(0, $card->points());

        $card = new Card('S', 13);
        $this->assertSame(0, $card->points());
    }

    public function testOnePointForHearts(): void
    {
        $card = new Card('H', 7);
        $this->assertSame(1, $card->points());

        $card = new Card('H', 11);
        $this->assertSame(1, $card->points());

        $card = new Card('H', 13);
        $this->assertSame(1, $card->points());
    }

    public function testThirteenPointsForQueenOfSpades(): void
    {
        $card = new Card('S', 12);
        $this->assertSame(13, $card->points());
    }

    public function testTwoCardObjectsForDifferentCardEvaluateAsNotEqual(): void
    {
        $cardOne = new Card('H', 8);
        $cardTwo = new Card('D', 8);
        $this->assertNotEquals($cardOne, $cardTwo);
    }

    public function testTwoCardObjectsForSameCardEvaluateAsEqual(): void
    {
        $cardOne = new Card('H', 10);
        $cardTwo = new Card('H', 10);
        $this->assertEquals($cardOne, $cardTwo);
    }

    public function testExceptionWithIncorrectSuit(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $card = new Card('A', 13);
    }

    public function testExceptionWithTypeTooLow(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $card = new Card('D', 0);
    }

    public function testExceptionWithTypeTooHigh(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $card = new Card('C', 15);
    }

    /**
     * Card->isOfSuit()
     */

    public function testIsOfSuitReturnsTrueIfSuitMatches(): void
    {
        $card = new Card('D', 13);
        $this->assertTrue($card->isOfSuit('D'));
    }

    public function testIsOfSuitReturnsFalseIfSuitDoesNotMatch(): void
    {
        $card = new Card('H', 8);
        $this->assertFalse($card->isOfSuit('C'));
    }

    /**
     * Card->compareTo()
     */

    public function testCompareToSmallerCardReturnsPositiveInt(): void
    {
        $card = new Card('H', 10);
        $result = $card->compareTo(new Card('D', 8));
        $this->assertTrue($result > 0);
    }

    public function testCompareToLargerCardReturnsNegativeInt(): void
    {
        $card = new Card('H', 10);
        $result = $card->compareTo(new Card('S', 12));
        $this->assertTrue($result < 0);
    }

    public function testCompareToSameValueCardReturnsZero(): void
    {
        $card = new Card('H', 10);
        $result = $card->compareTo(new Card('C', 10));
        $this->assertTrue($result == 0);
    }

    /**
     * Card->__toString()
     */

    public function testStringRepresentationCorrect(): void
    {
        $expected = 'H8';
        $result = (string) new Card('H', 8);

        $this->assertSame($expected, $result);

        $expected = 'DQ';
        $result = (string) new Card('D', 12);

        $this->assertSame($expected, $result);
    }
}
