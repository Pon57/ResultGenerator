<?php

	include('challonge.class.php');
	$c = new ChallongeAPI('');
	$c->verify_ssl = false;
	$t = $c->getParticipants('karisuma20_A');
	$result = [];
	$i = 0;
	foreach($t as $key => $s){
		$result[$i]['id'] = (integer)$s->id;
		$result[$i]['name'] = (string)$s->name;
		$result[$i]['final-rank'] = (integer)$s->{'final-rank'};
		$i++;
	}
	sortArrayByKey($result,'final-rank');
	
	print_r($result);

	function sortArrayByKey(array &$array, string $sortKey, int $sortType = SORT_ASC) {
		$tmp_array = [];
		foreach ($array as $key => $row) {
			$tmp_array[$key] = $row[$sortKey];
		}
		array_multisort($tmp_array, $sortType, $array);
		unset($tmp_array);
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<title>ResultGenerator</title>
<meta name="Keywords" content=" ">
<meta name="Description" content=" ">

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
<script src="my.js"></script>

</head>
<body>
	<header>
		<h1>スマブラ大会結果ジェネレータ</h1>
	</header>
	
	<div class="module">
		<h2>入力してください</h2>
		<dl class="table">
			<dt>タイトル</dt>
			<dd><input type="text" name="title" id="title" placeholder="Umebura 20" value="Umebura 20" /></dd>
			<dt>情報</dt>
			<dd><input type="text" name="info" id="info" placeholder="80 entrants - Aichi, Chubu (Japan) [8/4]" value="80 entrants - Aichi, Chubu (Japan) [8/4]" /></dd>
			<dt>テスト</dt>
			<dd><input type="text" name="test" id="test" placeholder="80 entrants - Aichi, Chubu (Japan) [8/4]" value="1." /></dd>
			<dd><input type="text" name="test2" id="test2" placeholder="80 entrants - Aichi, Chubu (Japan) [8/4]" value="2." /></dd>
		</dl>
		<div class="btn">
			<button id="update">画像生成</button>
		</div>
	</div>
	
	<div class="module">
		<h2>結果</h2>
		<div class="canvas">
			<!-- width,heightは必須 -->
			<canvas id="result" width="427" height="1145"></canvas>
		</div>
		<div class="btn">
			<form action="img.php" method="post" id="saveform">
				<button id="save">画像として保存</button>
			</form>
		</div>
	</div>
	
</body>
</html>