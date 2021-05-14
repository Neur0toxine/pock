<?php

declare(strict_types=1);

if (
    function_exists('date_default_timezone_set')
    && function_exists('date_default_timezone_get')
) {
    date_default_timezone_set(date_default_timezone_get());
}

if (!is_file($autoloadFile = __DIR__ . '/../vendor/autoload.php')) {
    throw new RuntimeException('Did not find vendor/autoload.php. Did you run "composer install --dev"?');
}

$loader = require $autoloadFile;
$loader->add('Pock\\Tests', __DIR__ . '/src');
$loader->add('Pock\\TestUtils', __DIR__ . '/utils');
