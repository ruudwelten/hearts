<?php

declare(strict_types=1);

namespace Hearts;

use Faker;

class Game
{
    private static ?Game $instance = null;
    private int $numberOfPlayers;
    private array $players = [];
    private int $currentPlayerId;
    private array $scores = [];
    private Deck $deck;
    private int $cardsInGame;
    private int $tricksInGame;
    private int $currentHandNumber = 0;
    private int $currentTrickNumber;
    private array $passedCards = [];
    private bool $heartsBroken = false;
    private Player $loser;

    private function __construct()
    {
        // Default number of players is 4, set custom amount in Config.php
        $this->numberOfPlayers = Config::$numberOfPlayers ?? 4;
        if ($this->numberOfPlayers < 4 || $this->numberOfPlayers > 6) {
            throw new \OutOfRangeException('Incorrect number of players: 4, 5 or 6 are allowed. Number of players given: ' . $this->numberOfPlayers);
        }

        $this->createPlayers();

        $this->scores = array_fill(0, $this->numberOfPlayers, 0);

        // Determine number of cards and tricks in the game based on
        // number of players and deck size.
        $this->cardsInGame = 52;
        if (is_array(Config::$deckComposition)) {
            $this->cardsInGame = count(Config::$deckComposition, COUNT_RECURSIVE) -
                count(Config::$deckComposition);
        }
        $this->cardsInGame -= $this->cardsInGame % $this->numberOfPlayers;

        $this->tricksInGame = (int) floor($this->cardsInGame / $this->numberOfPlayers);

        $this->deck = new Deck(Config::$deckComposition, $this->cardsInGame);
    }

    public static function getInstance(): Game
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function createPlayers(): void
    {
        $faker = Faker\Factory::Create();

        for ($i = 1; $i <= $this->numberOfPlayers; $i++) {
            $newPlayer = new Player(count($this->players), $faker->firstName());
            array_push($this->players, $newPlayer);
        }
    }

    public function startHand(): bool
    {
        // Prevent a hand from being started if the game is over or if not all
        // tricks in the last hand have been played.
        if (isset($this->loser)) {
            return false;
        } elseif (
            isset($this->currentTrickNumber) &&
            $this->currentTrickNumber != $this->tricksInGame
        ) {
            return false;
        }

        $this->currentHandNumber++;
        $this->currentTrickNumber = 0;
        $this->heartsBroken = false;

        // Reset the deck if a hand has already been played
        if ($this->currentHandNumber > 1) {
            $this->deck->reset();
        }
        $this->deck->shuffle();

        $this->deal();

        $this->passCards();

        return true;
    }

    private function deal(): void
    {
        $player = -1;
        while ($card = $this->deck->pickCard()) {
            $player = ($player + 1) % $this->numberOfPlayers;
            $this->players[$player]->addCard($card);

            // Set the current player when the first playable card is dealt
            if ($card == $this->deck->firstPlayableCard()) {
                $this->currentPlayerId = $player;
            }
        }
    }

    private function passCards(): void
    {
        // Change direction every hand
        $directions = [1, -1, 2, 0];
        $direction = $directions[($this->currentHandNumber-1) % 4];

        if ($direction === 0) {
            $this->passedCards = [];
            return;
        }

        $cardsToPass = [];
        foreach ($this->players as $key => $player) {
            $cardsToPass[$key][
                ($key + $direction + $this->numberOfPlayers) % $this->numberOfPlayers
            ] = [
                $player->passCard(),
                $player->passCard(),
                $player->passCard(),
            ];
        }

        foreach ($cardsToPass as $cardsFromPlayer) {
            foreach ($cardsFromPlayer as $key => $cards) {
                foreach ($cards as $card) {
                    $this->players[$key]->addCard($card);
                }
            }
        }

        $this->passedCards = $cardsToPass;
    }

    public function passedCards(): array
    {
        return $this->passedCards;
    }

    public function playTrick(): ?Trick
    {
        if (
            isset($this->loser) ||
            !isset($this->currentTrickNumber) ||
            $this->currentTrickNumber >= $this->tricksInGame
        ) {
            return null;
        }

        $this->currentTrickNumber += 1;
        $trick = new Trick();

        $cardFilter = new CardFilter('allow');
        // Deny penalty cards in the first trick
        if ($this->currentTrickNumber == 1) {
            $cardFilter->denySuit('H');
            $cardFilter->denyCard(new Card('S', 12));
        }

        for ($i = 1; $i <= $this->numberOfPlayers; $i++) {
            $filter = $cardFilter;

            // Only allow first playable card as opening card
            if ($this->currentTrickNumber == 1 && $i == 1) {
                $firstFilter = new CardFilter('deny');
                $firstFilter->appendStage($cardFilter);
                $firstFilter->allowCard($this->deck->firstPlayableCard());
                $filter = $firstFilter;
            }
            // Deny opening with a penalty card until hearts have been broken
            elseif ($i == 1 && !$this->heartsBroken) {
                $firstFilter = new CardFilter('allow');
                $firstFilter->appendStage($cardFilter);
                $firstFilter->denySuit('H');
                $firstFilter->denyCard(new Card('S', 12));
                $filter = $firstFilter;
            }

            $this->playTurn($trick, $filter);

            // Must follow suit in all following turns
            if ($i == 1) {
                $cardFilter = $cardFilter->prependStage(new CardFilter('deny'));
                $cardFilter->allowSuit($trick->suit());
            }
        }

        // Add points to the loser's score
        $this->addPoints($trick->loser(), $trick->points());

        // Set current player for the next trick to the loser of this trick
        $this->currentPlayerId = $trick->loser()->id();

        if (!$this->heartsBroken) {
            $this->heartsBroken = $trick->heartsBroken();
        }

        return $trick;
    }

    private function playTurn(Trick $trick, CardFilter $cardFilter): void
    {
        $player = $this->currentPlayer();
        $player->playCard($trick, $cardFilter);
        $this->advanceTurn();
    }

    private function addPoints($player, $points): void
    {
        $this->scores[$player->id()] += $points;

        // When one player reaches the score limit the game ends
        if ($this->scores[$player->id()] >= (Config::$scoreLimit ?? 100)) {
            $this->loser = $player;
        }
    }

    private function advanceTurn(): void
    {
        $this->currentPlayerId = ($this->currentPlayerId + 1) % $this->numberOfPlayers;
    }

    private function currentPlayer(): Player
    {
        return $this->players[$this->currentPlayerId];
    }

    public function players(): array
    {
        return $this->players;
    }

    public function playerNames(): array
    {
        $playerNames = [];
        foreach ($this->players as $player) {
            $playerNames[] = $player->name();
        }
        return $playerNames;
    }

    public function playerNameById(int $playerId): string
    {
        if ($playerId < 0 || $playerId > $this->numberOfPlayers-1) {
            throw new \OutOfRangeException('Player ID out of range. Supplied ID: ' . $playerId);
        }

        return $this->players[$playerId]->name();
    }

    public function currentHandNumber(): int
    {
        return $this->currentHandNumber;
    }

    public function currentTrickNumber(): ?int
    {
        if (!isset($this->currentTrickNumber)) {
            return null;
        }

        return $this->currentTrickNumber;
    }

    public function scores(): array
    {
        return $this->scores;
    }

    public function loser(): ?Player
    {
        if (!isset($this->loser)) {
            return null;
        }

        return $this->loser;
    }
}
