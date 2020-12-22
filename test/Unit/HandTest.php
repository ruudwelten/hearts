<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Hand;
use Hearts\Card;
use PHPUnit\Framework\TestCase;

class HandTest extends TestCase
{
    protected function setUp(): void
    {
        $this->hand = new Hand();
    }

    /**
     * Hand->pickCard()
     */

    public function testPickCardReturnsNullIfCardIsNotInHand(): void
    {
        $this->assertNull($this->hand->pickCard(new Card('S', 10)));
    }

    public function testPickCard(): void
    {
        $card = new Card('C', 7);

        $this->hand->addCard($card);

        $result = $this->hand->pickCard($card);

        $this->assertEquals($card, $result);
        $this->assertEquals(0, $this->hand->countCards());
     }

    /**
     * Hand->pickHighestCard()
     */

    public function testPickHighestCardReturnsNullWhenHandIsEmpty(): void
    {
        $this->assertNull($this->hand->pickHighestCard());
    }

    public function testPickHighestCardReturnsCardWhenHandContainsOneCard(): void
    {
        $this->hand->addCard(new Card('S', 7));

        $expected = new Card('S', 7);
        $result = $this->hand->pickHighestCard();

        $this->assertEquals($expected, $result);
    }

    public function testPickHighestCardReturnsHighestCard(): void
    {
        $this->hand->addCard(new Card('S', 9));
        $this->hand->addCard(new Card('H', 7));
        $this->hand->addCard(new Card('S', 11));
        $this->hand->addCard(new Card('D', 7));
        $this->hand->addCard(new Card('S', 7));
        $this->hand->addCard(new Card('S', 14));
        $this->hand->addCard(new Card('C', 7));

        $expected = new Card('S', 14);
        $result = $this->hand->pickHighestCard();

        $this->assertEquals($expected, $result);
    }

    /**
     * Hand->pickRandomCard()
     */

    public function testPickRandomCardReturnsNullWhenHandIsEmpty(): void
    {
        $this->assertNull($this->hand->pickRandomCard());
    }

    public function testPickRandomCardReturnsCardWhenHandContainsOneCard(): void
    {
        $this->hand->addCard(new Card('H', 12));

        $expected = new Card('H', 12);
        $result = $this->hand->pickRandomCard();

        $this->assertEquals($expected, $result);
    }

    public function testPickRandomCardReturnsACardWhenHandContainsMultiple(): void
    {
        $cards = [
            new Card('S', 9),
            new Card('H', 7),
            new Card('H', 11),
            new Card('D', 13),
            new Card('C', 8),
            new Card('S', 14),
            new Card('C', 12),
        ];
        foreach ($cards as $card) {
            $this->hand->addCard($card);
        }

        $result = $this->hand->pickRandomCard();

        $this->assertTrue(in_array($result, $cards));
    }

    /**
     * Hand->removeCard()
     */

    public function testCountIsOneLessAfterPickingCard(): void
    {
        $cards = [
            new Card('D', 13),
            new Card('C', 8),
            new Card('S', 14),
        ];
        foreach ($cards as $card) {
            $this->hand->addCard($card);
        }
        $this->hand->pickRandomCard();

        $result = $this->hand->countCards();

        $this->assertSame(2, $result);
    }

    public function testCountIsTwoLessAfterPickingCardTwice(): void
    {
        $cards = [
            new Card('S', 13),
            new Card('D', 11),
            new Card('H', 7),
            new Card('C', 8),
            new Card('S', 14),
        ];
        foreach ($cards as $card) {
            $this->hand->addCard($card);
        }
        $this->hand->pickRandomCard();
        $this->hand->pickRandomCard();

        $result = $this->hand->countCards();

        $this->assertSame(3, $result);
    }
}
