<?php

declare(strict_types=1);

namespace Hearts;

class Hand extends CardSet
{
    protected array $allowedCards = [];

    public function pickCard(Card $card): ?Card
    {
        if (!in_array($card, $this->cards)) {
            return null;
        }

        $this->removeCard($card);

        return $card;
    }

    public function pickHighestCard(): ?Card
    {
        $highestCard = null;
        foreach ($this->cards as $card) {
            if ($highestCard === null) {
                $highestCard = $card;
            }
            if ($highestCard->compareTo($card) < 0) {
                $highestCard = $card;
            }
        }

        if ($highestCard !== null) {
            $this->removeCard($highestCard);
        }

        return $highestCard;
    }

    public function pickRandomCard(?CardFilter $cardFilter = null): ?Card
    {
        if ($cardFilter !== null) {
            $cards = $cardFilter->filter($this->cards);
        } else {
            $cards = $this->cards;
        }

        if (empty($cards)) {
            return null;
        }

        $card = $cards[array_rand($cards)];
        $this->removeCard($card);

        return $card;
    }

    private function removeCard(Card $card): void
    {
        $key = array_search($card, $this->cards);
        if ($key !== false) {
            unset($this->cards[$key]);
        }
    }
}
