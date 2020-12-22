<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>Hearts simulator</title>

    <link rel="shortcut icon" href="/resources/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/resources/favicon.ico" type="image/x-icon">
    <link href="resources/style.css" rel="stylesheet">
</head>

<body>

<header>
    <h1>Hearts simulator</h1>
</header>

<main>

<p>Starting a game with <?= implode(', ', $result['playerNames']) ?></p>

<?php foreach ($result['hands'] as $handNumber => $hand) : ?>
    <div class="stickyHeader"><h2>Hand <?= $handNumber ?></h2></div>

    <?php if (isset($hand['passedCards'])): ?>
        <table>
            <?php foreach ($hand['passedCards'] as $pass): ?>
                <tr>
                    <td><?= $pass['from'] ?> passes these cards to <?= $pass['to'] ?>:</td>
                    <td><?= implode(', ', $pass['cards']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <table>
        <?php foreach ($hand['deal'] as $playerName => $cards) : ?>
            <tr>
                <td><?= $playerName ?> has been dealt:</td>
                <td><?= implode(', ', $cards) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <?php foreach ($hand['tricks'] as $trickNumber => $trick) : ?>
        <div class="trick"
            <?php foreach ($trick['scores'] as $key => $score): ?>
                data-score<?= $key ?>="<?= $score ?>"
            <?php endforeach; ?>
        >
            <h3>Trick <?= $trickNumber ?>: <?= $trick['leaderName'] ?> starts the trick</h3>

            <table>
                <?php foreach ($trick['turns'] as $turn) : ?>
                    <tr>
                        <td><?= $turn['playerName'] ?> plays:</td>
                        <td><?= $turn['card'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <p>
                <?= $trick['loserName'] ?> played <?= $trick['losingCard'] ?>, the
                highest matching card of this trick and got <?= $trick['points'] ?>
                point<?= (($trick['points'] != 1) ? 's' : '') ?> added to their total
                score. <?= $trick['loserName'] ?>'s total score is <?= $trick['loserScore'] ?>
                point<?= (($trick['loserScore'] != 1) ? 's' : '') ?>.
            </p>
        </div>
    <?php endforeach; ?>
<?php endforeach; ?>

<div class="stickyHeader"><h2>Game over</h2></div>

<p><?= $result['loserName'] ?> loses the game!</p>

</main>

<footer>
    <h3>Scores:</h3>
    <table>
        <?php foreach ($result['scores'] as $playerName => $score) : ?>
            <tr>
                <td><?= $playerName ?>:</td>
                <td class="score"><?= $score ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</footer>

<script src="resources/stickyheaders.js" type="text/javascript"></script>
<script src="resources/scrollingscores.js" type="text/javascript"></script>

</body>

</html>
