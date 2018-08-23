(function($){
	//初期化
	var init_canvas = function(){
		
		logoImg = new Image();
		logoImg.src = 'umebura.png';
		jpImg = new Image();
		jpImg.src = 'jp.png';
		stage = new createjs.Stage('result');

		bg_shape = new createjs.Shape();//shapeインスタンスを作る
		bg_shape.graphics.f("white").dr(0,0,stage.canvas.width,stage.canvas.height);//形を白い四角に決める、幅と高さはステージと同サイズにする
		genImage();
		stage.update();
	}

	var strWidth = function(str) {
		var canvas = document.getElementById('result');
		if (canvas.getContext) {
		  var context = canvas.getContext('2d');
		  context.font = "12px";
		  var metrics = context.measureText(str);
		  return metrics.width;
		}
		return -1;
	}
	
	//画像と文字を合成する処理
	var genImage = function(){
		var logo = new createjs.Bitmap(logoImg);

		logo.x = stage.canvas.width/2-logoImg.width/2;
		logo.y = 10;
		
		stage.addChild(bg_shape);//白い四角をステージに先に置く
		stage.addChild(logo);
		
		//合成する文字の位置情報などを定義
		//input[type="text"]のnameをキーとし、X座標、Y座標、フォントサイズ、行揃えを持たせている。
		var txt = {
			'title' : {
				'x' : stage.canvas.width/2,	
				'y' : tmp=logoImg.height+20,
				'size': '15px',
				'align': 'center'
			},
			'info' : {
				'x' : stage.canvas.width/2,
				'y' : tmp+=25,
				'size': '15px',
				'align': 'center'
			}
		}
		txt["test"] = {
			'x' : 30,
			'y' : 141+40,
			'size': '15px',
			'align': 'left'
		};
		txt["test2"] = {
			'x' : 30,
			'y' : txt["test"]["y"]+50,
			'size': '15px',
			'align': 'left'
		};
		var tmp_y = 20;
		//上記の配列より文字オブジェクトを生成し、stageにaddChildする
		$.each(txt,function(key,value){
			//本文は入力された内容をそのまま取る
			var content = $('#' + key).val();

			if(key != 'title' && key != 'info'){
				var region = new createjs.Bitmap(jpImg);
				region.x = 45
				region.y = 141+tmp_y;
				tmp_y += 50;
				stage.addChild(region);
			}
		
			//文字オブジェクト生成
			var obj = new createjs.Text(content);
			
			//文字の属性を設定する
			obj.textAlign = value.align;
			obj.font = 'bold ' + value.size + '/1.5 Meiryo,sans-serif';  //CSSのfontのショートハンドと同じ
			obj.x = value.x; //X座標
			obj.y = value.y; //Y座標
			
			stage.addChild(obj);
		});
		}

		
		//保存させる処理
		//ここだけはブラウザ上で完結できなかった…。
		//base64をデコードしてheaderを付加してechoするだけのPHPへPOSTしています
		var save = function(){
			var png = stage.toDataURL('image/png'); //base64エンコードした画像データ生成
			if(png){
				$('input[name="img"]').remove();
				$('#saveform').append('<input type="hidden" name="img" value="'+png+'" />');
				return true;
			} else {
				return false;
			}
		}
	
	$(function(){
		//canvasオブジェクトを事前に定義しておく
		$(document).ready( function(){
			init_canvas();
		});
		
		//画像生成ボタンが押されたときにcanvas生成
		$('#update').on('click',function(e){
			//改めてnewすることでキャッシュが残って変な感じにならない…多分。
			stage = new createjs.Stage('result');
			genImage();
			stage.update();
		});
		
		//「画像として保存」ボタンが押された時の処理
		$('#saveform').on('submit',function(){
			save();
		});
	});
})(jQuery);