<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Config;
use Hearts\Card;
use Hearts\Deck;
use PHPUnit\Framework\TestCase;

class DeckTest extends TestCase
{
    protected function setUp(): void
    {
        $this->cardsInGame = 52;
        if (is_array(Config::$deckComposition)) {
            $this->cardsInGame = count(Config::$deckComposition, COUNT_RECURSIVE);
        }

        $this->deck = new Deck();
    }

    public function testContainsAllCardsAfterInstantiation(): void
    {
        $this->assertSame($this->cardsInGame, $this->deck->countCards());
    }

    public function testContainsOnlyUniqueCards(): void
    {
        $cards = $this->deck->cards();
        $this->assertSame($cards, array_unique($cards));
    }

    /**
     * Deck->pickCard()
     */

    public function testPickCardReturnsCard(): void
    {
        $card = $this->deck->pickCard();
        $this->assertInstanceOf('Hearts\Card', $card);
    }

    public function testContainsOneLessCardAfterPickCard(): void
    {
        $this->deck->pickCard();
        $this->assertSame($this->cardsInGame - 1, $this->deck->countCards());
    }

    public function testContainsThreeLessCardsAfterPickCardThrice(): void
    {
        $this->deck->pickCard();
        $this->deck->pickCard();
        $this->deck->pickCard();
        $this->assertSame($this->cardsInGame - 3, $this->deck->countCards());
    }

    /**
     * Deck->reset()
     */

    public function testContainsAllCardsAfterReset(): void
    {
        $this->deck->pickCard();
        $this->deck->pickCard();
        $this->deck->pickCard();
        $this->deck->reset();
        $this->assertSame($this->cardsInGame, $this->deck->countCards());
    }
}
