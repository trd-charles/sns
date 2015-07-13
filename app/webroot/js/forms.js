var FormClass = function()
{

	//メンバ変数
	this.maintype;

	//初期化
	this.f_init = function(){
		//正規表現の設定
		this.maintype = $('form').attr('class');


		this.textarea =0;

		//IEかそれ以外か判定
		var userAgent = window.navigator.userAgent.toLowerCase();
		var appVersion = window.navigator.appVersion.toLowerCase();
		if (userAgent.indexOf("msie") > -1) {
			if (appVersion.indexOf("msie 6.0") > -1) {
				this.IEcheck = true;
			}
			else if (appVersion.indexOf("msie 7.0") > -1) {
				this.IEcheck = true;
			}
			else if (appVersion.indexOf("msie 8.0") > -1) {
				this.IEcheck = true;
			}
			else if (appVersion.indexOf("msie 9.0") > -1) {
				this.IEcheck = true;
			}
			else {
				this.IEcheck = false;
			}
		}
		else{
			this.IEcheck = false;
		}

		$('textarea:not(".markItUp")').keyup(function() {
			changeTextAreaSize(this);
		});
	}
}


//テキストエリアの幅を自動で変更する
changeTextAreaSize = function(obj) {
	var text = $(obj).val();
	num = text.match(/\n|\r\n/g);
	if(num==null){
		$(obj).css('height','45px');
	}else{
		if(num.length>1){
			$(obj).css('height',(45+(15*num.length-1))+'px');
		}
	}
};


//削除の確認
function del() {
	if (confirm("本当に削除をしてもよろしいですか？")){

		//削除
		return true;
	} else {

		//キャンセル
		return false;
	}
}

//すべて選択
function select_all(checked) {
	if(checked) {
		$(".chk").attr("checked", $(".chk_all").attr("checked"));
	}else {
		$(".chk").removeAttr("checked");
	}
}