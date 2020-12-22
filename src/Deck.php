<?php

declare(strict_types=1);

namespace Hearts;

class Deck extends CardSet
{
    // Default deck composition: Standard pack, no jokers
    // To customize, set composition in Config.php
    private array $composition = [
        'C' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
        'D' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
        'S' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
        'H' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
    ];

    private int $totalCards;

    // Copy of the original cards array to reset the deck after every trick
    private array $baseDeck = [];

    private Card $firstPlayableCard;

    public function __construct(array $composition = null, int $totalCards = 52)
    {
        $this->composition = $composition ?? $this->composition;
        $this->totalCards = $totalCards;

        $this->createDeck();
    }

    private function createDeck(): void
    {
        $this->trimDeck();

        foreach ($this->composition as $suit => $values) {
            foreach ($values as $value) {
                $card = new Card($suit, $value);
                $this->addCard($card);

                // Find first playable card in the deck
                if (
                    !isset($this->firstPlayableCard) ||
                    $this->firstPlayableCard->compareToBySuitAndValue($card) > 0
                ) {
                    $this->firstPlayableCard = $card;
                }
            }
        }
        $this->baseDeck = $this->cards;
    }

    private function trimDeck(): void
    {
        $suits = count($this->composition);
        $suitKey = -1;
        while (count($this->composition, COUNT_RECURSIVE) - $suits > $this->totalCards) {
            $suitKey = ($suitKey + 1) % $suits;
            array_shift($this->composition[array_keys($this->composition)[$suitKey]]);
        }
    }

    public function firstPlayableCard(): Card
    {
        return $this->firstPlayableCard;
    }

    public function shuffle(): void
    {
        shuffle($this->cards);
    }

    public function pickCard(): ?Card
    {
        return array_pop($this->cards);
    }

    public function reset(): void
    {
        $this->cards = $this->baseDeck;
    }
}
