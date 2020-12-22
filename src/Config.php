<?php

declare(strict_types=1);

namespace Hearts;

class Config
{
    /**
     * @var ?array
     *     Set to null for default (standard pack, no jokers):
     *     [
     *         'C' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
     *         'D' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
     *         'S' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
     *         'H' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
     *     ]
     *     To balance the cards over all players the first cards from the deck
     *     will be removed one at a time per suit in order of appearance,
     *     except for hearts.
     *     So by default: C2, D2, S2, C3.
     */
    public static ?array $deckComposition = null;

    /**
     * @var ?int
     *     Number of players: 4, 5 or 6.
     *     Set to null for default (4).
     */
    public static ?int $numberOfPlayers = null;

    /**
     * @var ?int
     *     Score limit, when a player reaches this score limit the game ends.
     *     Set to null for default (100).
     */
    public static ?int $scoreLimit = null;
}
