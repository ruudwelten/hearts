<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Trick;
use Hearts\Player;
use Hearts\Card;
use PHPUnit\Framework\TestCase;

class TrickTest extends TestCase
{
    protected function setUp(): void
    {
        $this->player = new Player(0, 'Tessa');
        $this->trick = new Trick();
    }

    public function testLeaderIsNullAfterInstantiation(): void
    {
        $this->assertNull($this->trick->leader());
    }

    public function testSuitIsNullAfterInstantiation(): void
    {
        $this->assertNull($this->trick->suit());
    }

    public function testPointsIsZeroAfterInstantiation(): void
    {
        $this->assertSame(0, $this->trick->points());
    }

    public function testLoserIsNullAfterInstantiation(): void
    {
        $this->assertNull($this->trick->loser());
    }

    public function testLosingCardIsNullAfterInstantiation(): void
    {
        $this->assertNull($this->trick->losingCard());
    }

    /**
     * Trick->placeCard()
     */

    public function testPlacingFirstCardSetsLeader(): void
    {
        $this->trick->placeCard($this->player, new Card('S', 9));

        $this->assertSame($this->player, $this->trick->leader());
    }

    public function testPlacingFirstCardSetsSuitToMatch(): void
    {
        $this->trick->placeCard($this->player, new Card('S', 9));

        $this->assertSame('S', $this->trick->suit());
    }

    public function testPlacingFirstCardSetsLoser(): void
    {
        $this->trick->placeCard($this->player, new Card('S', 9));

        $this->assertSame($this->player, $this->trick->loser());
    }

    public function testPlacingFirstCardSetsLosingCardToMatch(): void
    {
        $this->trick->placeCard($this->player, new Card('S', 9));

        $this->assertEquals(new Card('S', 9), $this->trick->losingCard());
    }

    public function testPlacingHigherCardOfCorrectSuitSetsLoserAndLosingCard(): void
    {
        $secondPlayer = new Player(1, 'Kanye Test');
        $this->trick->placeCard($this->player, new Card('S', 9));
        $this->trick->placeCard($secondPlayer, new Card('S', 10));

        $this->assertSame($secondPlayer, $this->trick->loser());
        $this->assertEquals(new Card('S', 10), $this->trick->losingCard());
    }

    public function testPlacingLowerCardOfCorrectSuitKeepsLoserAndLosingCard(): void
    {
        $secondPlayer = new Player(1, 'Kanye Test');
        $this->trick->placeCard($this->player, new Card('S', 9));
        $this->trick->placeCard($secondPlayer, new Card('S', 8));

        $this->assertSame($this->player, $this->trick->loser());
        $this->assertEquals(new Card('S', 9), $this->trick->losingCard());
    }

    public function testPlacingHigherCardOfDifferentSuitKeepsLoserAndLosingCard(): void
    {
        $secondPlayer = new Player(1, 'Kanye Test');
        $this->trick->placeCard($this->player, new Card('S', 9));
        $this->trick->placeCard($secondPlayer, new Card('D', 10));

        $this->assertSame($this->player, $this->trick->loser());
        $this->assertEquals(new Card('S', 9), $this->trick->losingCard());
    }
}
