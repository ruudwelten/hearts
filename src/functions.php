<?php

function formatCard(string $cardString): string
{
    $htmlEntities = [
        'H' => '&hearts;',
        'D' => '&diams;',
        'S' => '&spades;',
        'C' => '&clubs;',
    ];
    $classes = [
        'H' => 'hearts',
        'D' => 'diamonds',
        'S' => 'spades',
        'C' => 'clubs',
    ];
    $split = str_split($cardString);
    $card = $htmlEntities[$split[0]] . $split[1] . (isset($split[2]) ? $split[2] : '');
    return '<span class="card ' . $classes[$split[0]] . '">' . $card . '</span>';
}

function formatCardCli(string $cardString): string
{
    $suits = [
        'H' => "\033[31m♥",  // Red
        'S' => "♠",          // Default
        'D' => "\033[31m♦",  // Red
        'C' => "♣",          // Default
    ];
    $reset = "\033[0m";
    $split = str_split($cardString);
    return $suits[$split[0]] . $split[1] . (isset($split[2]) ? $split[2] : '') . $reset;
}

function waitForEnter(): void
{
    if (in_array('--step', $GLOBALS['argv'])) {
        fgets(STDIN);
    }
}
