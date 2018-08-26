<?php
	if(isset($_POST['img'])){
		//画像のダウンロード
		header('Content-Type: application/octet-stream');
		header('Content-disposition: attachment; filename="generator.png"');
		echo base64_decode(str_replace('data:image/png;base64,','',$_POST['img']));
	}
?>