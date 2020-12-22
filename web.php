<?php

declare(strict_types=1);

use Hearts\Game;

$game = Game::getInstance();

$result = [
    'playerNames' => $game->playerNames(),
    'hands' => []
];

while ($game->startHand()) {
    $handResult = ['deal' => [], 'tricks' => []];

    if (!empty($game->passedCards())) {
        $handResult['passedCards'] = [];
        foreach ($game->passedCards() as $fromPlayerId => $passedCards) {
            $handResult['passedCards'][$fromPlayerId] = [
                'from' => $game->players()[$fromPlayerId]->name()
            ];
            foreach ($passedCards as $toPlayerId => $cards) {
                $handResult['passedCards'][$fromPlayerId]['to'] = $game->players()[$toPlayerId]->name();
                $handResult['passedCards'][$fromPlayerId]['cards'] = array_map('formatCard', $cards);
            }
        }
    }

    foreach ($game->players() as $player) {
        $handResult['deal'][$player->name()] = array_map('formatCard', $player->hand()->cards());
    }

    while ($trick = $game->playTrick()) {
        $trickResult = [
            'leaderName' => $trick->leader()->name(),
            'turns' => [],
            'loserName' => $trick->loser()->name(),
            'losingCard' => formatCard((string) $trick->losingCard()),
            'points' => $trick->points(),
            'scores' => $game->scores(),
            'loserScore' => $game->scores()[$trick->loser()->id()],
        ];

        foreach ($trick->cards() as $playerId => $card) {
            $trickResult['turns'][] = [
                'playerName' => $game->playerNameById($playerId),
                'card' => formatCard((string) $card),
            ];
        }

        $handResult['tricks'][$game->currentTrickNumber()] = $trickResult;
    }

    $result['hands'][$game->currentHandNumber()] = $handResult;
}

$result['loserName'] = $game->loser()->name();

$scores = [];
foreach ($game->scores() as $playerId => $score) {
    $scores[$game->playerNameById($playerId)] = $score;
}
$result['scores'] = $scores;

require_once('resources/template.php');
