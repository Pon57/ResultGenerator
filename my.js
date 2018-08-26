(function($){
	//初期化
	var init_canvas = function(){
		var $script = $('#script');
		results = JSON.parse($script.attr('data-param'));
		//確認
		logoImg = new Image();
		logoImg.src = 'karisuma.png';
		stage = new createjs.Stage('result');
		jpImg = new Image();
		jpImg.src = 'jp.png';
		bg_shape = new createjs.Shape();//shapeインスタンスを作る
		bg_shape.graphics.f("white").dr(0,0,stage.canvas.width,stage.canvas.height);//形を白い四角に決める、幅と高さはステージと同サイズにする
		genImage();
		stage.update();
	}

	var strWidth = function(str, font) {
		var canvas = document.getElementById('result');
		if (canvas.getContext) {
		  var context = canvas.getContext('2d');
		  context.font = font;
		  var metrics = context.measureText(str);
		  return metrics.width;
		}
		return -1;
	}
	
	//画像と文字を合成する処理
	var genImage = function(){
		var logo = new createjs.Bitmap(logoImg);

		logo.scaleY = 120 / logo.getBounds().height;
		logo.scaleX = logo.scaleY;
		
		logo.x = stage.canvas.width/2-logoImg.width/2*logo.scaleX;
		logo.y = 30;

		
		
		stage.addChild(bg_shape);//白い四角をステージに先に置く
		stage.addChild(logo);
		
		//合成する文字の位置情報などを定義
		//input[type="text"]のnameをキーとし、X座標、Y座標、フォントサイズ、行揃えを持たせている。
		var txt = {
			'title' : {
				'x' : stage.canvas.width/2,	
				'y' : tmp=logoImg.height*logo.scaleY+logo.y+10,
				'size': '16px',
				'align': 'center'
			},
			'info' : {
				'x' : stage.canvas.width/2,
				'y' : tmp+=16+5,
				'size': '13px',
				'align': 'center'
			}
		}

		var result_y = tmp;
		$.each(results,function(key,value){
			txt["player"+key] = {
				'x' : 50,
				'y' : result_y+=50,
				'size': '20px',
				'align': 'left'
			};
		});
		var tmp_y = 13 + 21;
		//上記の配列より文字オブジェクトを生成し、stageにaddChildする   194
		$.each(txt,function(key,value){
			//本文は入力された内容をそのまま取る
			var content = $('#' + key).val();
		
			//文字オブジェクト生成
			if(key != 'title' && key != 'info'){
				content = content + '.';
			}
			var obj = new createjs.Text(content);

			if(key == 'info'){
				style = 'italic';
			} else {
				style = 'bold';
			}
			
			//文字の属性を設定する
			obj.textAlign = value.align;
			obj.font = style + ' ' + value.size + '/1.5 Yu Gothic,sans-serif';  //CSSのfontのショートハンドと同じ
			obj.x = value.x; //X座標
			obj.y = value.y; //Y座標

			if(key != 'title' && key != 'info'){
				obj.x -= strWidth(content,obj.font);
				var region = new createjs.Bitmap(jpImg);
				//region.x = 45
				region.x = obj.x+strWidth(content,obj.font);
				region.y = tmp+tmp_y;
				tmp_y += 50;
				stage.addChild(region);

				//本文は入力された内容をそのまま取る
				var name = $('#' + key + '_name').val();
			
				//文字オブジェクト生成
				var player = new createjs.Text(name);

				//文字の属性を設定する
				player.textAlign = 'left';
				player.font = 'bold 20px/1.5 Yu Gothic,sans-serif';  //CSSのfontのショートハンドと同じ
				player.x = region.x+jpImg.width+10; //X座標
				player.y = obj.y; //Y座標
				stage.addChild(player);

				var charImg = new Image();
				charImg.src = 'stockicons/bayonetta_01.png';
				
				var character = new createjs.Bitmap(charImg);
				character.scaleY = (jpImg.height+10) / character.getBounds().height;
				character.scaleX = character.scaleY;
				character.x = player.x+strWidth(name,obj.font)-2;
				character.y = region.y-5;
				stage.addChild(character);
				

				var charImg2 = new Image();
				charImg2.src = 'stockicons/captain_03.png';
				var character2 = new createjs.Bitmap(charImg2);
				character2.scaleY = (jpImg.height+10) / character2.getBounds().height;
				character2.scaleX = character.scaleY;
				character2.x = character.x+charImg2.width*character2.scaleX-3;
				character2.y = character.y;
				stage.addChild(character2);

			}
			
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