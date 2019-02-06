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

$url = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_SPECIAL_CHARS);
if (!$url) {
	$url = 'https://smash.gg/tournament/umebura-for-wii-u-final/events/wii-u-singles/brackets';	
}
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

$fighters = array(
	array('id' => '1', 'name' => 'マリオ', 'slug' => 'mario', 'color' => 8),
	array('id' => '2', 'name' => 'ドンキーコング', 'slug' => 'donkey', 'color' => 8),
	array('id' => '3', 'name' => 'リンク', 'slug' => 'link', 'color' => 8),
	array('id' => '4', 'name' => 'サムス', 'slug' => 'samus', 'color' => 8),
	//array('id' => '4d', 'name' => 'ダークサムス', 'slug' => 'dark_samus', 'color' => 8),
	array('id' => '5', 'name' => 'ヨッシー', 'slug' => 'yoshi', 'color' => 8),
	array('id' => '6', 'name' => 'カービィ', 'slug' => 'kirby', 'color' => 8),
	array('id' => '7', 'name' => 'フォックス', 'slug' => 'fox', 'color' => 8),
	array('id' => '8', 'name' => 'ピカチュウ', 'slug' => 'pikachu', 'color' => 8),
	array('id' => '9', 'name' => 'ルイージ', 'slug' => 'luigi', 'color' => 8),
	array('id' => '10', 'name' => 'ネス', 'slug' => 'ness', 'color' => 8),
	array('id' => '11', 'name' => 'キャプテン・ファルコン', 'slug' => 'captain', 'color' => 8),
	array('id' => '12', 'name' => 'プリン', 'slug' => 'purin', 'color' => 8),
	array('id' => '13', 'name' => 'ピーチ', 'slug' => 'peach', 'color' => 8),
	//array('id' => '13d', 'name' => 'デイジー', 'slug' => 'daisy', 'color' => 8),
	array('id' => '14', 'name' => 'クッパ', 'slug' => 'koopa', 'color' => 8),
	//array('id' => '15', 'name' => 'アイスクライマー', 'slug' => 'iceclimbers', 'color' => 8),
	array('id' => '16', 'name' => 'シーク', 'slug' => 'sheik', 'color' => 8),
	array('id' => '17', 'name' => 'ゼルダ', 'slug' => 'zelda', 'color' => 8),
	array('id' => '18', 'name' => 'ドクターマリオ', 'slug' => 'drmario', 'color' => 8),
	//array('id' => '19', 'name' => 'ピチュー', 'slug' => 'pichu', 'color' => 8),
	array('id' => '20', 'name' => 'ファルコ', 'slug' => 'falco', 'color' => 8),
	array('id' => '21', 'name' => 'マルス', 'slug' => 'marth', 'color' => 8),
	array('id' => '21d', 'name' => 'ルキナ', 'slug' => 'lucina', 'color' => 8),
	//array('id' => '22', 'name' => 'こどもリンク', 'slug' => 'younglink', 'color' => 8),
	array('id' => '23', 'name' => 'ガノンドロフ', 'slug' => 'ganon', 'color' => 8),
	array('id' => '24', 'name' => 'ミューツー', 'slug' => 'mewtwo', 'color' => 8),
	array('id' => '25', 'name' => 'ロイ', 'slug' => 'roy', 'color' => 8),
	//array('id' => '25d', 'name' => 'クロム', 'slug' => 'chrom', 'color' => 8),
	array('id' => '26', 'name' => 'Mr. ゲーム&ウォッチ', 'slug' => 'gamewatch', 'color' => 8),
	array('id' => '27', 'name' => 'メタナイト', 'slug' => 'metaknight', 'color' => 8),
	array('id' => '28', 'name' => 'ピット', 'slug' => 'pit', 'color' => 8),
	array('id' => '28d', 'name' => 'ダークピット', 'slug' => 'pitb', 'color' => 8),
	array('id' => '29', 'name' => 'ゼロスーツサムス', 'slug' => 'szerosuit', 'color' => 8),
	array('id' => '30', 'name' => 'ワリオ', 'slug' => 'wario', 'color' => 8),
	//array('id' => '31', 'name' => 'スネーク', 'slug' => 'snake', 'color' => 8),
	array('id' => '32', 'name' => 'アイク', 'slug' => 'ike', 'color' => 8),
	//array('id' => '33-35', 'name' => 'ポケモントレーナー', 'slug' => 'pokemontrainer', 'color' => 8),
	array('id' => '35', 'name' => 'リザードン', 'slug' => 'lizardon', 'color' => 8),
	array('id' => '36', 'name' => 'ディディーコング', 'slug' => 'diddy', 'color' => 8),
	array('id' => '37', 'name' => 'リュカ', 'slug' => 'lucas', 'color' => 8),
	array('id' => '38', 'name' => 'ソニック', 'slug' => 'sonic', 'color' => 8),
	array('id' => '39', 'name' => 'デデデ大王', 'slug' => 'dedede', 'color' => 8),
	array('id' => '40', 'name' => 'ピクミン&オリマー', 'slug' => 'pikmin', 'color' => 8),
	array('id' => '41', 'name' => 'ルカリオ', 'slug' => 'lucario', 'color' => 8),
	array('id' => '42', 'name' => 'ロボット', 'slug' => 'robot', 'color' => 8),
	array('id' => '43', 'name' => 'トゥーンリンク', 'slug' => 'toonlink', 'color' => 8),
	//array('id' => '44', 'name' => 'ウルフ', 'slug' => 'wolf', 'color' => 8),
	array('id' => '45', 'name' => 'むらびと', 'slug' => 'murabito', 'color' => 8),
	array('id' => '46', 'name' => 'ロックマン', 'slug' => 'rockman', 'color' => 8),
	array('id' => '47', 'name' => 'Wii Fit トレーナー', 'slug' => 'wiifit', 'color' => 8),
	array('id' => '48', 'name' => 'ロゼッタ&チコ', 'slug' => 'rosetta', 'color' => 8),
	array('id' => '49', 'name' => 'リトルマック', 'slug' => 'littlemac', 'color' => 16),
	array('id' => '50', 'name' => 'ゲッコウガ', 'slug' => 'gekkouga', 'color' => 8),
	array('id' => '51', 'name' => 'Miiファイター(格闘)', 'slug' => 'miifighter', 'color' => 1),
	array('id' => '52', 'name' => 'Miiファイター(剣術)', 'slug' => 'miiswordsman', 'color' => 1),
	array('id' => '53', 'name' => 'Miiファイター(射撃)', 'slug' => 'miigunner', 'color' => 1),
	array('id' => '54', 'name' => 'パルテナ', 'slug' => 'palutena', 'color' => 8),
	array('id' => '55', 'name' => 'パックマン', 'slug' => 'pacman', 'color' => 8),
	array('id' => '56', 'name' => 'ルフレ', 'slug' => 'reflet', 'color' => 8),
	array('id' => '57', 'name' => 'シュルク', 'slug' => 'shulk', 'color' => 8),
	array('id' => '58', 'name' => 'クッパJr.', 'slug' => 'koopajr', 'color' => 8),
	array('id' => '59', 'name' => 'ダックハント', 'slug' => 'duckhunt', 'color' => 8),
	array('id' => '60', 'name' => 'リュウ', 'slug' => 'ryu', 'color' => 8),
	array('id' => '61', 'name' => 'クラウド', 'slug' => 'cloud', 'color' => 8),
	array('id' => '62', 'name' => 'カムイ', 'slug' => 'kamui', 'color' => 8),
	array('id' => '63', 'name' => 'ベヨネッタ', 'slug' => 'bayonetta', 'color' => 8),
	//array('id' => '64', 'name' => 'インクリング', 'slug' => 'inkling', 'color' => 8),
	//array('id' => '65', 'name' => 'リドリー', 'slug' => 'ridley', 'color' => 8),
	//array('id' => '66', 'name' => 'シモン', 'slug' => 'simon', 'color' => 8),
	//array('id' => '66d', 'name' => 'リヒター', 'slug' => 'richter', 'color' => 8),
	//array('id' => '67', 'name' => 'キングクルール', 'slug' => 'krool', 'color' => 8),
	//array('id' => '68', 'name' => 'しずえ', 'slug' => 'sizue', 'color' => 8),
	//array('id' => '69', 'name' => 'ガオガエン', 'slug' => 'gaogaen', 'color' => 8),
	//array('id' => '70', 'name' => 'パックンフラワー', 'slug' => 'pakkun', 'color' => 8),
);

include('form.html');
