<?php

declare(strict_types=1);

namespace Hearts;

class Card
{
    private string $suit;
    private string $type;
    private int $value;
    private int $points = 0;  // Penalty point value for scoring
    private array $courtTypes = [11 => 'J', 12 => 'Q', 13 => 'K', 14 => 'A'];

    public function __construct(string $suit, int $value)
    {
        if (!$this->isValidSuit($suit)) {
            throw new \InvalidArgumentException('Incorrect suit provided. Input was: ' . $suit);
        }
        if (!$this->isValidValue($value)) {
            throw new \InvalidArgumentException('Incorrect value provided. Input was: ' . $value);
        }

        $this->suit = $suit;
        $this->value = $value;
        if ($value >= 11) {
            $this->type = $this->courtTypes[$value];
        } else {
            $this->type = (string) $value;
        }

        if ($this->isOfSuit('H')) {
            $this->points = 1;
        } elseif ($this->isOfSuitAndType('S', 'Q')) {
            $this->points = 13;
        }
    }

    private function isValidSuit(string $suit): bool
    {
        return in_array($suit, ['H', 'C', 'D', 'S']);
    }

    private function isValidValue(int $value): bool
    {
        if (!ctype_digit((string) $value)) {
            return false;
        } elseif ($value < 1 || $value > 14) {
            return false;
        }

        return true;
    }

    public function isOfSuit(string $suit): bool
    {
        return $suit == $this->suit;
    }

    public function isOfSuitAndType(string $suit, string $type): bool
    {
        return $suit == $this->suit && $type == $this->type;
    }

    public function suit(): string
    {
        return $this->suit;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function points(): int
    {
        return $this->points;
    }

    /**
     * Compare with another Card object by value
     *
     * @return integer
     *     Negative, zero or positive if this object is less than, equal or
     *     greater than the other object respectively.
     */
    public function compareTo(Card $other): int
    {
        return $this->value() - $other->value();
    }

    /**
     * Compare with another Card object by suit and value. Suits rank as folows:
     * Clubs (lowest), Diamonds, Spades, Hearts (highest).
     *
     * @return integer
     *     Negative, zero or positive if this object is less than, equal or
     *     greater than the other object respectively.
     */
    public function compareToBySuitAndValue(Card $other): int
    {
        $suitWeight = ['C' => 1, 'D' => 2, 'S' => 3, 'H' => 4];
        $compareSuit = $suitWeight[$this->suit()] - $suitWeight[$other->suit()];
        if ($compareSuit != 0) {
            return $compareSuit;
        }
        return $this->value() - $other->value();
    }

    public function __toString(): string
    {
        return $this->suit . $this->type;
    }
}
