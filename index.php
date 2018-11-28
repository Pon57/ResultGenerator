<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use API\SmashggAPI;
use API\ChallongeAPI;
use Classes\Challonge;
use Classes\Smashgg;
use Classes\Functions;

//require
$autoloader = require_once __DIR__.'/vendor/autoload.php';

$url = 'https://challonge.com/toryumon_40';
$url_array = explode('/', $url);

if (isset($url_array[2])) {
    switch ($url_array[2]) {
        case 'challonge.com':
            $dotenv = new Dotenv(__DIR__);
            $dotenv->load();
            $challonge_api = getenv('CHALLONGE_API');

            $tournament = new Challonge($challonge_api, $url);
            break;
        case 'smash.gg':
            $tournament = new Smashgg($url);
            break;
    }
    $results = $tournament->getResultsList();
    $title = $tournament->getTournamentTitle();
    $total_entrants = $tournament->getTotalEntrants();
}

$functions = new Functions();
$functions->sortArrayByKey($results, 'rank');

$height = 50 * count($results);

function json_safe_encode(array $data): string
{
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

include('form.html');
