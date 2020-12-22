<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Card;
use Hearts\CardSet;
use PHPUnit\Framework\TestCase;

class CardSetTest extends TestCase
{
    protected function setUp(): void
    {
        $this->cardSet = $this->getMockForAbstractClass(CardSet::class);
    }

    public function testIsEmptyOnInstantiation(): void
    {
        $this->assertSame(0, $this->cardSet->countCards());
    }

    /**
     * CardSet->add()
     */

    public function testCountIsOneAfterAddingOnce(): void
    {
        $this->cardSet->addCard(new Card('S', 13));
        $this->assertSame(1, $this->cardSet->countCards());
    }

    public function testCountIsTwoAfterAddingTwice(): void
    {
        $this->cardSet->addCard(new Card('S', 13));
        $this->cardSet->addCard(new Card('C', 7));
        $this->assertSame(2, $this->cardSet->countCards());
    }

    /**
     * CardSet->cards()
     */

    public function testCardsEmptyOnInstantiation(): void
    {
        $this->assertSame([], $this->cardSet->cards());
    }

    public function testCardsMatchesAddedCard(): void
    {
        $expected = [
            new Card('D', 14),
        ];
        $this->cardSet->addCard(new Card('D', 14));
        $this->assertEquals($expected, $this->cardSet->cards());
    }

    public function testCardsMatchesAddedCardsInOrder(): void
    {
        $expected = [
            new Card('D', 14),
            new Card('C', 7),
            new Card('H', 11),
        ];
        $this->cardSet->addCard(new Card('D', 14));
        $this->cardSet->addCard(new Card('C', 7));
        $this->cardSet->addCard(new Card('H', 11));
        $this->assertEquals($expected, $this->cardSet->cards());
    }
}
