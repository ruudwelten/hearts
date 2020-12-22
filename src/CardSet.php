<?php

declare(strict_types=1);

namespace Hearts;

abstract class CardSet
{
    protected array $cards = [];

    public function countCards(): int
    {
        return count($this->cards);
    }

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
    }

    public function cards(): array
    {
        return $this->cards;
    }
}
