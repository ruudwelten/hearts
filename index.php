<?php

declare(strict_types=1);

require_once(__DIR__ . '/vendor/autoload.php');

if (isset($argv)) {
    require_once('cli.php');
} else {
    require_once('web.php');
}
