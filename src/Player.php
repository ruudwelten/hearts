<?php

declare(strict_types=1);

namespace Hearts;

class Player
{
    private int $id;
    private string $name;
    private Hand $hand;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
        $this->hand = new Hand();
    }

    public function addCard(Card $card): void
    {
        $this->hand->addCard($card);
    }

    public function passCard(): Card
    {
        return $this->hand->pickHighestCard();
    }

    public function playCard(Trick $trick, ?CardFilter $cardFilter = null): void
    {
        if ($this->hand->countCards() == 0) {
            throw new \UnderflowException('Can not play card, hand is empty.');
        }

        $card = null;

        $card = $this->hand->pickRandomCard($cardFilter, true);

        // Play random card if no cards are available
        if ($card === null) {
            $card = $this->hand->pickRandomCard();
        }

        if ($card !== null) {
            $trick->placeCard($this, $card);
        }
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function hand(): Hand
    {
        return $this->hand;
    }
}
