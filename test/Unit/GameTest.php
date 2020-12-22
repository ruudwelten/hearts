<?php

declare(strict_types=1);

namespace HeartsTest\Unit;

use Hearts\Config;
use Hearts\Game;
use Hearts\Deck;
use Hearts\Player;
use Hearts\Card;
use PHPUnit\Framework\TestCase;

class GameTest extends TestCase
{
    protected function setUp(): void
    {
        // Determine number of players and tricks in the game
        // based on configuration in Config.php
        $this->numberOfPlayers = Config::$numberOfPlayers ?? 4;
        $this->cardsInGame = 52;
        if (is_array(Config::$deckComposition)) {
            $this->cardsInGame = count(Config::$deckComposition, COUNT_RECURSIVE);
        }
        $this->numberOfTricks = (int) floor($this->cardsInGame / $this->numberOfPlayers);

        $this->game = Game::getInstance();
    }

    protected function tearDown(): void
    {
        $reflection = new \ReflectionClass($this->game);
        $instance = $reflection->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
        $instance->setAccessible(false);
    }

    public function testSingletonOnlyOneInstancePossible(): void
    {
        $expected = Game::getInstance();
        $this->assertSame($expected, $this->game);
    }

    public function testHasFourPlayersAfterInstantiation(): void
    {
        $this->assertSame($this->numberOfPlayers, count($this->game->players()));
    }

    public function testHasEmptyScoresForEveryPlayerAfterInstantiation(): void
    {
        $expected = array_fill(0, $this->numberOfPlayers, 0);

        $this->assertSame($expected, $this->game->scores());
    }

    public function testCurrentHandNumberIsZeroAfterInstantiation(): void
    {
        $this->assertSame(0, $this->game->currentHandNumber());
    }

    public function testCurrentTrickNumberIsNullAfterInstantiation(): void
    {
        $this->assertNull($this->game->currentTrickNumber());
    }

    /**
     * Game->deal()
     */

    public function testFirstPlayerSelectedDuringDealing(): void
    {
        $dealReflection = new \ReflectionMethod(Game::class, 'deal');
        $dealReflection->setAccessible(true);
        $dealReflection->invoke($this->game);

        $reflection = new \ReflectionClass($this->game);
        $gameCurrentPlayerId = $reflection->getProperty('currentPlayerId');
        $gameCurrentPlayerId->setAccessible(true);

        $expected = range(0, $this->numberOfPlayers-1);
        $result = $gameCurrentPlayerId->getValue($this->game);

        $this->assertTrue(in_array($result, $expected));
    }

    /**
     * Game->passCards()
     */

    public function testPassCardsPassesCardsAround(): void
    {
        $gameReflection = new \ReflectionClass($this->game);

        $gameCurrentHandNumber = $gameReflection->getProperty('currentHandNumber');
        $gameCurrentHandNumber->setAccessible(true);
        $gameCurrentHandNumber->setValue($this->game, 1);

        $gamePlayers = $gameReflection->getProperty('players');
        $gamePlayers->setAccessible(true);
        $mockPlayers = [];
        foreach ($gamePlayers->getValue($this->game) as $key => $player) {
            $playerStub = $this->fixturePlayerPassCard();
            $playerStub->expects($this->exactly(3))
                       ->method('addCard');
            $mockPlayers[$key] = $playerStub;
        }
        $gamePlayers->setValue($this->game, $mockPlayers);

        $gamePassCards = $gameReflection->getMethod('passCards');
        $gamePassCards->setAccessible(true);
        $gamePassCards->invoke($this->game);
    }

    /**
     * Game->startHand()
     */

    public function testStartHandIncrementsCurrentHand(): void
    {
        $this->game->startHand();

        $this->assertSame(1, $this->game->currentHandNumber());
    }

    public function testStartHandShufflesTheDeck(): void
    {
        $deck = $this->getMockBuilder(Deck::class)->getMock();

        $deck->expects($this->once())
             ->method('shuffle');

        $reflection = new \ReflectionClass($this->game);

        $gameDeck = $reflection->getProperty('deck');
        $gameDeck->setAccessible(true);
        $gameDeck->setValue($this->game, $deck);
        $gameDeck->setAccessible(false);

        $gamePlayers = $reflection->getProperty('players');
        $gamePlayers->setAccessible(true);
        $gamePlayers->setValue($this->game, array_fill(
            0,
            count($gamePlayers->getValue($this->game)),
            $this->fixturePlayerPassCard(),
        ));
        $gamePlayers->setAccessible(false);

        $this->game->startHand();
    }

    public function testStartHandSecondTimeResetsTheDeck(): void
    {
        $deck = $this->getMockBuilder(Deck::class)->getMock();

        $deck->expects($this->once())
             ->method('reset');

        $reflection = new \ReflectionClass($this->game);

        $gameDeck = $reflection->getProperty('deck');
        $gameDeck->setAccessible(true);
        $gameDeck->setValue($this->game, $deck);
        $gameDeck->setAccessible(false);

        $gamePlayers = $reflection->getProperty('players');
        $gamePlayers->setAccessible(true);
        $gamePlayers->setValue($this->game, array_fill(
            0,
            count($gamePlayers->getValue($this->game)),
            $this->fixturePlayerPassCard(),
        ));
        $gamePlayers->setAccessible(false);

        $this->game->startHand();

        $currentTrickNumber = $reflection->getProperty('currentTrickNumber');
        $currentTrickNumber->setAccessible(true);
        $currentTrickNumber->setValue($this->game, $this->numberOfTricks);
        $currentTrickNumber->setAccessible(false);

        $this->game->startHand();
    }

    /**
     * Game->playTrick()
     */

    public function testPlayTrickReturnsNullIfCurrentTrickNumberIsUnsetOrLastTrick(): void
    {
        $this->assertNull($this->game->playTrick());

        $this->game->startHand();

        $reflection = new \ReflectionClass($this->game);
        $currentTrickNumber = $reflection->getProperty('currentTrickNumber');
        $currentTrickNumber->setAccessible(true);
        $currentTrickNumber->setValue($this->game, $this->numberOfTricks);
        $currentTrickNumber->setAccessible(false);

        $this->assertNull($this->game->playTrick());
    }

    /**
     * Game->playerNames()
     */

    public function testPlayerNamesReturnsArrayOfPlayerNames(): void
    {
        $expected = [];
        foreach ($this->game->players() as $player) {
            $expected[] = $player->name();
        }

        $this->assertEquals($expected, $this->game->playerNames());
    }

    /**
     * Game->playerNameById()
     */

    public function testPlayerNameByIdReturnsCorrectPlayerName(): void
    {
        $players = $this->game->players();

        $expected = $players[0]->name();
        $this->assertEquals($expected, $this->game->playerNameById(0));

        $expected = $players[2]->name();
        $this->assertEquals($expected, $this->game->playerNameById(2));
    }

    private function fixturePlayerPassCard(): \PHPUnit\Framework\MockObject\MockObject
    {
        $playerStub = $this->createMock(Player::class);
        $playerStub->method('passCard')
                   ->willReturnCallback(function() {
                       if (!isset($this->_passCardCount)) {
                           $this->_passCardCount = -1;
                       }
                       $this->_passCardCount++;

                       return new Card('H', $this->_passCardCount % 12 + 2);
                   });

        return $playerStub;
    }
}
