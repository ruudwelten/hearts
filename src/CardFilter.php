<?php

declare(strict_types=1);

namespace Hearts;

class CardFilter
{
    private bool $allowAll;  // true = allow | false = deny

    private array $allowedSuits = [];
    private array $allowedCards = [];

    private array $deniedSuits = [];
    private array $deniedCards = [];

    private CardFilter $nextStage;

    public function __construct(string $allowAll = 'allow')
    {
        if ($allowAll != 'allow' && $allowAll != 'deny') {
            throw new \UnexpectedValueException('`$allowAll` only accepts \'allow\' or \'deny\', supplied: ' . $allowAll);
        }

        $this->allowAll = $allowAll == 'allow';
    }

    public function filter(array $cards): array
    {
        $filteredCards = $cards;
        foreach ($cards as $key => $card) {
            if (
                in_array($card, $this->deniedCards) ||
                in_array($card->suit(), $this->deniedSuits)
            ) {
                unset($filteredCards[$key]);
            }

            // No need to check for allowed values if allowAll is set to true
            if ($this->allowAll) {
                continue;
            }

            if (
                !in_array($card, $this->allowedCards) &&
                !in_array($card->suit(), $this->allowedSuits)
            ) {
                unset($filteredCards[$key]);
            }
        }

        if (count($filteredCards) == 0 && isset($this->nextStage)) {
            $filteredCards = $this->nextStage->filter($cards);
        }

        return $filteredCards;
    }

    public function allowSuit(string ...$suits): void
    {
        foreach ($suits as $suit) {
            $this->allowedSuits[] = $suit;
        }
    }

    public function denySuit(string ...$suits): void
    {
        foreach ($suits as $suit) {
            $this->deniedSuits[] = $suit;
        }
    }

    public function allowCard(Card ...$cards): void
    {
        foreach ($cards as $card) {
            $this->allowedCards[] = $card;
        }
    }

    public function denyCard(Card ...$cards): void
    {
        foreach ($cards as $card) {
            $this->deniedCards[] = $card;
        }
    }

    public function appendStage(CardFilter $cardFilter): void
    {
        if (isset($this->nextStage)) {
            $this->nextStage->addStage($cardFilter);
        } else {
            $this->nextStage = $cardFilter;
        }
    }

    public function prependStage(CardFilter $cardFilter): CardFilter
    {
        $cardFilter->appendStage($this);

        return $cardFilter;
    }
}
