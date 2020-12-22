<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Player;
use Hearts\Card;
use Hearts\CardFilter;
use Hearts\Trick;
use Hearts\Hand;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Assert;

class PlayerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->player = new Player(0, 'Tessa');
    }

    public function testHandIsEmptyOnInstantiation(): void
    {
        $this->assertSame(0, $this->player->hand()->countCards());
    }

    public function testCorrectId(): void
    {
        $this->assertSame(0, $this->player->id());
    }

    public function testCorrectName(): void
    {
        $this->assertSame('Tessa', $this->player->name());
    }

    /**
     * Player->addCard()
     */

    public function testHandHasOneCardAfterAddingOnce(): void
    {
        $this->player->addCard(new Card('D', 12));
        $this->assertEquals(1, $this->player->hand()->countCards());
    }

    public function testHandHasTwoCardsAfterAddingTwice(): void
    {
        $this->player->addCard(new Card('D', 12));
        $this->player->addCard(new Card('H', 8));
        $this->assertEquals(2, $this->player->hand()->countCards());
    }

    /**
     * Player->passCard()
     */

    public function testPassCardCallsHandPickHighestCard(): void
    {
        $this->player->addCard(new Card('H', 7));

        $playerHandReflection = new \ReflectionProperty(Player::class, 'hand');
        $playerHandReflection->setAccessible(true);
        $hand = $playerHandReflection->getValue($this->player);

        $handStub = $this->createMock(Hand::class);
        $handStub->method('pickHighestCard')
                 ->willReturn(new Card('H', 7));
        $handStub->expects($this->once())
                 ->method('pickHighestCard');

        $playerHandReflection->setValue($this->player, $handStub);

        $this->player->passCard();
    }

    /**
     * Player->playCard()
     */

    public function testPlayCardPlaysCorrectCardWhenOneOfSuitIsInHand(): void
    {
        $this->player->addCard(new Card('H', 7));
        $this->player->addCard(new Card('C', 8));
        $this->player->addCard(new Card('D', 9));
        $this->player->addCard(new Card('S', 10));

        $trick = $this->fixtureMockTrickForDiamonds();

        $trick->expects($this->once())
              ->method('placeCard')
              ->will($this->returnCallback(function ($player, $card) {
                  Assert::assertEquals(new Card('D', 9), $card);
              }));

        $cardFilter = new CardFilter('deny');
        $cardFilter->allowSuit('D');

        $this->player->playCard($trick, $cardFilter);
    }

    public function testPlayCardThrowsExceptionWhenHandIsEmpty(): void
    {
        $this->expectException(\UnderflowException::class);
        $this->player->playCard(new Trick());
    }

    private function fixtureMockTrickForDiamonds(): \PHPUnit\Framework\MockObject\MockObject
    {
        $stub = $this->getMockBuilder(Trick::class)
                     ->disableOriginalConstructor()
                     ->getMock();
        $stub->method('suit')
             ->willReturn('D');

        return $stub;
    }
}
