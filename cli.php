<?php

declare(strict_types=1);

use Hearts\Game;

$game = Game::getInstance();

echo "\n";
echo "Hearts\n";
echo "==========\n\n";

echo "Starting a game with " . implode(', ', $game->playerNames()) . "\n\n";

waitForEnter();

while ($game->startHand()) {
    echo "Hand " . $game->currentHandNumber() . "\n";
    echo "------\n\n";

    if (!empty($game->passedCards())) {
        foreach ($game->passedCards() as $fromPlayerId => $passedCards) {
            foreach ($passedCards as $toPlayerId => $cards) {
                echo $game->players()[$fromPlayerId]->name();
                echo " passes these cards to ";
                $cards = array_map('formatCardCli', $cards);
                echo $game->players()[$toPlayerId]->name() . ": " . implode(', ', $cards) . "\n";
            }
        }
        echo "\n";
    }

    foreach ($game->players() as $player) {
        $cards = array_map('formatCardCli', $player->hand()->cards());
        echo $player->name() . " has been dealt: " . implode(', ', $cards) . "\n";
    }

    echo "\n";

    while ($trick = $game->playTrick()) {
        echo "Trick " . $game->currentTrickNumber() . ": ";
        echo $trick->leader()->name() . " starts the trick\n";

        foreach ($trick->cards() as $playerId => $card) {
            echo $game->playerNameById($playerId) . " plays: " . formatCardCli((string) $card) . "\n";
        }
        echo "\n";

        echo $trick->loser()->name() . " played " . formatCardCli((string) $trick->losingCard());
        echo ", the highest matching card of this trick and got ";
        echo $trick->points() . " point" . (($trick->points() != 1) ? 's' : '') . " ";
        echo "added to their total score. " . $trick->loser()->name() . "'s ";
        echo "total score is " . $game->scores()[$trick->loser()->id()] . " ";
        echo "point" . (($game->scores()[$trick->loser()->id()] != 1) ? 's' : '') . ".\n\n";

        waitForEnter();
    }
}

echo "\n";
echo "Game over\n";
echo "---------\n\n";

echo $game->loser()->name() . " loses the game!\n\n";

echo "Scores:\n";
foreach ($game->scores() as $playerId => $score) {
    echo $game->playerNameById($playerId) . ": " . $score . "\n";
}
