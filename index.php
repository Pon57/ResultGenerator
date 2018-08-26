<?php

	declare(strict_types=1);
	//require
	$autoloader = require_once __DIR__.'/vendor/autoload.php';
	
	/*$result = $autoloader->findFile('Api\ChallongeAPI');
	echo json_encode($result) . "\n";*/

    //for .env
	use Dotenv\Dotenv;
	
	$dotenv = new Dotenv(__DIR__);
	$dotenv->load();
	$challonge_api = getenv('CHALLONGE_API');

	use API\ChallongeAPI;

	$challonge = new ChallongeAPI($challonge_api);
	$challonge->verify_ssl = false;
	$tournament_name = 'toryumon_31';
	$tournament = $challonge->getTournament($tournament_name);
	$participants = $challonge->getParticipants($tournament_name);
	$matches = $challonge->getMatches($tournament_name, ['state'=>'complete']);

	
	$results = [];
	foreach($participants as $key => $participant){
		$results[(integer)$participant->id]['name'] = (string)$participant->name;
		/*if((integer)$participant->{'final-rank'} > 0){
			$results[(integer)$participant->id]['rank'] = (integer)$participant->{'final-rank'};
		} else {
			$results[(integer)$participant->id]['rank'] = '?';
			$results[(integer)$participant->id]['in-winner'] = 1;
			$results[(integer)$participant->id]['lost-loser'] = 0;
		}*/
		$results[(integer)$participant->id]['rank'] = '?';
		$results[(integer)$participant->id]['in-winner'] = 1;
		$results[(integer)$participant->id]['lost-loser'] = 0;
	}

	//if((string)$tournament->state !== 'complete' && $matches) {
	if($matches) {
		$rounds = [];
		foreach($matches as $m_key => $match){
			if($results[(integer)$match->{'loser-id'}]['in-winner'] === 0){
				$results[(integer)$match->{'loser-id'}]['lost-loser'] = (integer)$match->round;
				if((integer)$match->round > 0){
					$results[(integer)$match->{'loser-id'}]['lost-winner'] = (integer)$match->round;
					$results[(integer)$match->{'winner-id'}]['rank'] = 1;
					$results[(integer)$match->{'loser-id'}]['rank'] = 2;
				} else {
					$rounds[] = (integer)$match->round;
				}
			} else {
				$results[(integer)$match->{'loser-id'}]['lost-winner'] = (integer)$match->round;
				$results[(integer)$match->{'loser-id'}]['in-winner'] = 0;
			}
		}
		sortArrayByKey($results,'lost-loser',SORT_DESC);
		$rounds = array_count_values($rounds);
		$pre_round = 0;
		$sum_count = 0;
		
		foreach($results as $id => $result){
			if($result['lost-loser'] < 0){
				if($pre_round !== $result['lost-loser']){
					$pre_round = $result['lost-loser'];
					$sum_count += $rounds[$result['lost-loser']];
				}
				if($result['lost-loser'] > -4){
					unset($results[$id]);
					continue;
				}
				$results[$id]['rank'] = $tournament->{'participants-count'} - $sum_count + 1;
			}
		}
	}
	sortArrayByKey($results,'rank');
	$height = 50 * count($results);

	function sortArrayByKey(array &$array, string $sortKey, int $sortType = SORT_ASC): void {
		$tmp_array = [];
		foreach ($array as $key => $row) {
			$tmp_array[$key] = $row[$sortKey];
		}
		array_multisort($tmp_array, $sortType, $array);
		unset($tmp_array);
	}

	function json_safe_encode(array $data): string {
		return json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
	}
?>



<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>ResultGenerator</title>
<meta name="Keywords" content=" ">
<meta name="Description" content=" ">
<meta name="robots" content="noindex,nofollow">

<!-- スタイルは特に影響無いです -->
<style type="text/css">
	html {
		background: #eee;
	}
	body {
		background: #fff;
		box-shadow: 0 0 3px #ddd;
		width: 600px;
		margin: 0 auto;
		padding: 20px;
	}
	header {
		text-align: center;
		border-bottom: 1px solid #ddd;
		padding: 0 0 10px 0;
		margin-bottom: 10px;
	}
	.module {
		border-bottom: 1px solid #ddd;
		padding-bottom: 10px;
		margin-bottom: 10px;
	}
	.module h2 {
		text-align: center;
	}
	.btn {
		text-align: center;
	}
	.canvas {
		text-align: center;
	}
	footer {
		text-align: center;
		font-size: 80%;
		color: #999;
	}
</style>

<!-- 今回はEaselJSのみ使用します-->
<script src="https://code.createjs.com/easeljs-0.8.2.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script id="script" type="text/javascript" src="my.js"
 data-param='<?php echo json_safe_encode($results);?>'>
</script>

</head>
<body>
	<header>
		<h1>スマブラ大会結果ジェネレータ</h1>
	</header>
	
	<div class="module">
		<h2>入力してください</h2>
		<dl class="table">
			<dt>タイトル</dt>
			<dd><input type="text" name="title" id="title" placeholder="大会名" value="<?= $tournament->name ?>" /></dd>
			<dt>情報</dt>
			<dd><input type="text" name="info" id="info" placeholder="80 entrants - Aichi, Chubu (Japan) [8/4]" value="80 entrants - Aichi, Chubu (Japan) [8/4]" /></dd>
			<dt>参加者</dt>
<?php	  foreach($results as $key => $value){ ?>
			<dd><input type="text" name="player<?= $key ?>" id="player<?= $key ?>" placeholder="1" size="2" value="<?= $value['rank'] ?>" /><input type="text" name="player<?= $key ?>_name" id="player<?= $key ?>_name" placeholder="1" value="<?= $value['name'] ?>" /></dd>
<?php	  } ?>
		</dl>
		<div class="btn">
			<button id="update">画像生成</button>
		</div>
	</div>
	
	<div class="module">
		<h2>結果</h2>
		<div class="canvas">
			<!-- width,heightは必須 -->
			<canvas id="result" width="427" height="<?= 194+$height+30 ?>"></canvas>
		</div>
		<div class="btn">
			<form action="img.php" method="post" id="saveform">
				<button id="save">画像として保存</button>
			</form>
		</div>
	</div>
	
</body>
</html>