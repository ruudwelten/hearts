<?php

declare(strict_types=1);

namespace Hearts;

class Trick extends CardSet
{
    private Player $leader;
    private string $suit;
    private int $points = 0;
    private Player $loser;
    private Card $losingCard;

    public function __construct()
    {
        $this->state = 'leader';
    }

    public function placeCard(Player $player, Card $card): void
    {
        if (!isset($this->leader)) {
            $this->leader = $player;
        }

        if (!isset($this->suit)) {
            $this->suit = $card->suit();
        }

        $this->points += $card->points();

        if ($this->isLosingCard($card)) {
            $this->loser = $player;
            $this->losingCard = $card;
        }

        $this->cards[$player->id()] = $card;
    }

    private function isLosingCard(Card $card): bool
    {
        if (!$card->isOfSuit($this->suit)) {
            return false;
        }

        foreach ($this->cards as $c) {
            if (
                $card->compareTo($c) < 0 &&
                $c->isOfSuit($this->suit)
            ) {
                return false;
            }
        }

        return true;
    }

    public function leader(): ?Player
    {
        if (!isset($this->leader)) {
            return null;
        }

        return $this->leader;
    }

    public function suit(): ?string
    {
        if (!isset($this->suit)) {
            return null;
        }

        return $this->suit;
    }

    public function points(): int
    {
        return $this->points;
    }

    public function loser(): ?Player
    {
        if (!isset($this->loser)) {
            return null;
        }

        return $this->loser;
    }

    public function losingCard(): ?Card
    {
        if (!isset($this->losingCard)) {
            return null;
        }

        return $this->losingCard;
    }

    public function heartsBroken(): bool
    {
        foreach ($this->cards as $card) {
            if ($card->isOfSuit('H')) {
                return true;
            }
        }
        return false;
    }
}
