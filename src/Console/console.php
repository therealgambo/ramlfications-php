<?php

if (!$loader = include __DIR__ . '/../../vendor/autoload.php') {
    die('You must set up the project dependencies.');
}

$app = new \Cilex\Application('Cilex');
$app->command(new \TheRealGambo\Ramlfications\Console\Commands\InspectRouteCommand());
$app->command(new \TheRealGambo\Ramlfications\Console\Commands\ListRoutesCommand());
$app->command(new \TheRealGambo\Ramlfications\Console\Commands\ListSecuritySchemesCommand());
$app->command('foo', function ($input, $output) {
    $output->writeln('Example output');
});
$app->run();