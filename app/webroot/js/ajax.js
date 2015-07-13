//画面情報取得用クラス
var WindowClass = function(){

	//現在のスクロール位置
	this.getScrollPosition = function () {
		var obj = new Object();
		obj.x = document.documentElement.scrollLeft || document.body.scrollLeft;
		obj.y = document.documentElement.scrollTop || document.body.scrollTop;
		return obj;
	};

	//横幅
	this.getBrowserWidth = function () {
		return document.documentElement.scrollWidth || document.body.scrollWidth;
	};

	//縦幅
	this.getBrowserHeight = function () {
		return document.documentElement.scrollHeight || document.body.scrollHeight;
	};

	//現在の画面サイズ
	this.getScreenSize = function () {

		var isWin9X = (navigator.appVersion.toLowerCase().indexOf('windows 98')+1);
		var isIE = (navigator.appName.toLowerCase().indexOf('internet explorer')+1?1:0);
		var isOpera = (navigator.userAgent.toLowerCase().indexOf('opera')+1?1:0);
		if (isOpera) isIE = false;
		var isSafari = (navigator.appVersion.toLowerCase().indexOf('safari')+1?1:0);
		var obj = new Object();

		if (!isSafari && !isOpera) {
			obj.x = document.documentElement.clientWidth || document.body.clientWidth || document.body.scrollWidth;
			obj.y = document.documentElement.clientHeight || document.body.clientHeight || document.body.scrollHeight;
		} else {
			obj.x = window.innerWidth;
			obj.y = window.innerHeight;
		}
		obj.mx = parseInt((obj.x)/2);
		obj.my = parseInt((obj.y)/2);
		return obj;
	};

};

//ポップアップウインドウ用クラス
var PopupClass = function(win){

	//メンバ変数
	this.win = win;
	this.form;
	this.request;

	// 初期化処理
	this.init = function (form){
		this.form = form;
		this.request = '';
		// ポップアップウィンドウの背景デザイン
		$('#popup-bg').css({
			background : '#333333',
			display    : 'none',
			position   : 'absolute',
			width      : windowclass.getBrowserWidth() + 'px',
			height     : windowclass.getBrowserHeight() + 'px',
			top        : '0px',
			left       : '0px',
			filter     : 'Alpha(opacity=80)',
			opacity    : 0.8
		});

		// ポップアップウィンドウの本体デザイン
		$('#popup').css({
			background : '#ffffff',
			padding    : '0px',
			display    : 'none',
			position   : 'absolute',
			width      : '500px'
		});

		// 確認ダイアログのデザイン
		$('#confirm-dialogbox').css({
			background : '#ffffff',
			padding    : '0px',
			display    : 'none',
			position   : 'absolute',
			width      : '300px'
		});
		$('#button_confirm').css({display : 'none'});
		$('#button_alert').css({display : 'none'});


		// お知らせポップアップ用背景
		$('#popup-notice').css({
			background : '#ffffff',
			display    : 'none',
			position   : 'absolute',
			width      : windowclass.getBrowserWidth() + 'px',
			height     : windowclass.getBrowserHeight() + 'px',
			top        : '0px',
			left       : '0px',
			filter     : 'Alpha(opacity=0)',
			opacity    : 0
		});

		// お知らせ背景部分がクリックされたらポップアップを消す
		$('#popup-notice').click(function(){
			$("#subOkPopup").hide();
			$("#subNewPopup").hide();
			$("#subNotPopup").hide();

			$('#popup-notice').hide();
		});

		// 背景部分がクリックされたらポップアップを消す
		$('#popup-bg').click(function(){
			popupclass.popup_close();
		});

		//画面サイズ時の処理
		$(window).resize(function(){

			if($('#popup-bg').is(':visible')){
				if($('#popup').is(':visible')) {
					$('#popup-bg').css({
						width  : win.getBrowserWidth() + 'px',
						height : document.body.clientHeight + 'px'
					});

					$('#popup').animate({"top" : win.getScrollPosition().y + 100 + 'px'},{duration:300, queue: false});
					$('#popup').animate({"left": win.getScrollPosition().x + win.getScreenSize().mx - $('#popup').width()/2 + 'px'},{duration:300, queue: false});
				}
				else if($('#confirm-dialogbox').is(':visible')) {
					$('#popup-bg').css({
						width  : win.getBrowserWidth() + 'px',
						height : document.body.clientHeight + 'px'
					});

					$('#confirm-dialogbox').animate({"top" : win.getScrollPosition().y + 100 + 'px'},{duration:300, queue: false});
					$('#confirm-dialogbox').animate({"left": win.getScrollPosition().x + win.getScreenSize().mx - $('#confirm-dialogbox').width()/2 + 'px'},{duration:300, queue: false});
				}
			}
		});

		//画面スクロール時の処理
		$(window).scroll(function(){
			if($('#popup-bg').is(':visible') && $('#popup').is(':visible')){
				if($('#popup').is(':visible')) {
					var heightpoint = win.getBrowserHeight() - 100 - $('#popup').height();
					if(heightpoint > win.getScrollPosition().y){
						heightpoint = win.getScrollPosition().y + 100;
					}

					$('#popup').animate({"top" : heightpoint + 'px'},{duration:300, queue: false});
					$('#popup').animate({"left": win.getScrollPosition().x + win.getScreenSize().mx - $('#popup').width()/2 + 'px'},{duration:300, queue: false});
				}
			}
			else if($('#confirm-dialogbox').is(':visible')) {
				var heightpoint = win.getBrowserHeight() - 100 - $('#popup-dialog').height();
				if(heightpoint > win.getScrollPosition().y){
					heightpoint = win.getScrollPosition().y + 100;
				}

				$('#popup-dialog').animate({"top" : heightpoint + 'px'},{duration:300, queue: false});
				$('#popup-dialog').animate({"left": win.getScrollPosition().x + win.getScreenSize().mx - $('#popup-dialog').width()/2 + 'px'},{duration:300, queue: false});
			}

		});
	};

	this.popup_open = function(){
		$('#popup select').show();
		// ポップアップ背景の表示
		if($('#popup-bg').is(':visible')) {
			$('#popup').css({
				'top'   : this.win.getScrollPosition().y + 100 + 'px',
				'left'  : win.getScrollPosition().x + win.getScreenSize().mx - $('#popup').width()/2 + 'px'
			}).show();
		}else {
			$('#popup-bg').css({
				'width' : this.win.getBrowserWidth() + 'px',
				'height': document.body.clientHeight + 'px'
			}).fadeIn("slow");

			// ポップアップの表示
			$('#popup').css({
				'top'   : this.win.getScrollPosition().y + 100 + 'px',
				'left'  : win.getScrollPosition().x + win.getScreenSize().mx - $('#popup').width()/2 + 'px'
			}).fadeIn("slow");
		}
	}

	//ポップアップ画面の終了
	this.popup_close = function(){

		$('#popup-bg').fadeOut("slow");
		$('#popup').fadeOut("slow", function(){
			//IE6対策
			$('select').each(function(){
				$(this).show();
			});
		});

		return false;
	};

	//ポップアップからダイアログに遷移したかどうか
	this.fromPopup = false;

	//サブミットするフォームを保存しておく
	this.confirmForm = null;

	/**
	 * 確認ダイアログの表示
	 * @param description ダイアログに表示する文
	 * @param type ダイアログのタイプ confirm もしくは alert
	 * @param yesAction 「はい」または「確認」押下時の動作
	 * @param noAction 「いいえ」押下時の動作
	 * @param close 背景を閉じるかどうか。続けてダイアログを表示する場合はfalse
	 */
	this.confirm_open = function(description, type, yesAction, noAction, close){

		//すでにポップアップが表示されている場合は非表示にする
		if($('#popup').is(':visible')) {
			this.fromPopup = true;
			$('#popup').hide();
		}

		//文言とボタンの設定
		$('#confirm_description').html(description);
		$('#button_' + type).show();

		//
		if (close == undefined) {
			close = true;
		}
		//強制的にクローズ
		if(close == 'force') {
			this.fromPopup = false;
			close = true;
		}

		//Confirmの場合
		if (type == 'confirm') {
			$("#confirm_yes").click(function() {
				if(typeof(yesAction) == "function") yesAction();
				popupclass.confirm_close(close);
				return true;
			});
			$("#confirm_no").click(function() {
				if(typeof(noAction) == "function") noAction();
				if (popupclass.fromPopup) {
					popupclass.confirm_close(false);
					$('#popup').show();
				}
				else {
					popupclass.confirm_close(true);
				}
				return false;
			});
		}

		//Alertの場合
		else {
			$("#confirm_okey").click(function() {
				if(typeof(yesAction) == "function") yesAction();
				if (popupclass.fromPopup) {
					popupclass.confirm_close(false);
					$('#popup').show();
				}
				else {
					popupclass.confirm_close(close);
				}
				return true;
			});
		}

		
		$('#popup select').show();

		// ポップアップが表示されていなければ背景の表示
		if(!$('#popup-bg').is(':visible')) {
			$('#popup-bg').css({
				'width' : this.win.getBrowserWidth() + 'px',
				'height': document.body.clientHeight + 'px'
			}).show();
		}

		//背景をクリックしても消えないようにする
		$('#popup-bg').unbind();

		// ダイアログの表示
		$('#confirm-dialogbox').css({
			'top'   : this.win.getScrollPosition().y + 100 + 'px',
			'left'  : win.getScrollPosition().x + win.getScreenSize().mx - $('#confirm-dialogbox').width()/2 + 'px'
		}).show();

		return false;
	};

	//確認ダイアログの終了
	this.confirm_close = function(close){
		if(close) {
			$('#popup-bg').fadeOut("slow");
			$('#confirm-dialogbox').hide();
			$('#confirm_description').html('');
			$('#button_confirm').css({display : 'none'});
			$('#button_alert').css({display : 'none'});
			$("#confirm_okey").unbind();
			$("#confirm_yes").unbind();
			$("#confirm_no").unbind();
		}
		else {
			$('#confirm-dialogbox').hide();
			$('#confirm_description').html('');
			$('#button_confirm').css({display : 'none'});
			$('#button_alert').css({display : 'none'});
			$("#confirm_okey").unbind();
			$("#confirm_yes").unbind();
			$("#confirm_no").unbind();

			$('#popup-bg').click(function(){
				popupclass.popup_close();
			});
		}

		$('select').each(function(){
			$(this).show();
		});

		this.fromPopup = false;
		this.confirmForm = null;
		return false;
	};

	this.popup_view = function(request){
	 var res = request.responseText;
	 var obj = $('#popup');
	  obj.html(res);
	}
	this.popup_save = function(html){
		this.request = html;
	}
	this.get_request = function(){
		return this.request;
	}

	this.getFiveFormsId = function(controller) {

		data = {
			'1' : $('#'+controller+'ID'+1).val(),
			'2' : $('#'+controller+'ID'+2).val(),
			'3' : $('#'+controller+'ID'+3).val(),
			'4' : $('#'+controller+'ID'+4).val(),
			'5' : $('#'+controller+'ID'+5).val()
				}
				return data;

	}

};

function disp_com(no){
	$(".disp_com_"+no).remove();
	$(".display_"+no).show();
	return false;
}

function newMessage(request){
	 var res = request.responseText;
	 var obj = $('.timeline');
	 var load = $('.load');
	 $('.hideden_newid').remove();
	 obj.prepend(res);
	 var text = $('.hideden_newid').text();
	 $('#TimelineTMLID').val(text);
	 $('#TimelineMESSAGE').val(null);
}

function GetMessage(request){
	 var res = request.responseText;
	 var obj = $('.timeline');
	 $('.load').remove();
	 obj.append(res);

}

function TlReload_w(request){
	$('.protime').removeClass('present');
	$('.prowatch').addClass('present');
	$('.sort_ref').hide();
	$('.calendarSide').hide();
	 var res = request.responseText;
	 var obj = $('.timeline');
	 obj.html(res);
}
function TlReload_t(request){
	$('.prowatch').removeClass('present');
	$('.protime').addClass('present');
	$('.sort_ref').show();
	$('.calendarSide').show();
	 var res = request.responseText;
	 var obj = $('.timeline');
	 obj.html(res);
	coment_disp_init();
}

//ウォッチリスト
function TlReload_tw(request){
	$('.protime').removeClass('present');
	$('.protimegroup').removeClass('present');
	$('.protimeonly').removeClass('present');
	$('.protimefollow').removeClass('present');
	$('.prowatch').addClass('present');
	$('.sort_ref').hide();
	$('.calendarSide').hide();
	var res = request.responseText;
	var obj = $('.timeline');
	obj.html(res);
	coment_disp_init();
}

//オール（すべてのユーザの発言）
function TlReload_tt(request){
	$('.prowatch').removeClass('present');
	$('.protimegroup').removeClass('present');
	$('.protimeonly').removeClass('present');
	$('.protimefollow').removeClass('present');
	$('.protime').addClass('present');
	$('.sort_ref').show();
	$('.calendarSide').hide();
	var res = request.responseText;
	var obj = $('.timeline');
	obj.html(res);
	$('#TAB_NAME').val('ALL');
	coment_disp_init();
}

//グループ（所属しているグループの発言）
function TlReload_tg(request){
	$('.prowatch').removeClass('present');
	$('.protime').removeClass('present');
	$('.protimefollow').removeClass('present');
	$('.protimeonly').removeClass('present');
	$('.protimegroup').addClass('present');
	$('.sort_ref').show();
	$('.calendarSide').hide();
	var res = request.responseText;
	var obj = $('.timeline');
	obj.html(res);
	$('#TAB_NAME').val('GROUP');
	coment_disp_init();
}

//アクション（自分の発言）
function TlReload_to(request){
	$('.prowatch').removeClass('present');
	$('.protime').removeClass('present');
	$('.protimegroup').removeClass('present');
	$('.protimefollow').removeClass('present');
	$('.protimeonly').addClass('present');
	$('.sort_ref').show();
	$('.calendarSide').hide();
	var res = request.responseText;
	var obj = $('.timeline');
	obj.html(res);
	$('#TAB_NAME').val('ONLY');
	coment_disp_init();
}

//ホーム（フォローしているユーザの発言）
function TlReload_tf(request){
	$('.prowatch').removeClass('present');
	$('.protime').removeClass('present');
	$('.protimegroup').removeClass('present');
	$('.protimeonly').removeClass('present');
	$('.protimefollow').addClass('present');
	$('.sort_ref').show();
	$('.calendarSide').show();
	var res = request.responseText;
	var obj = $('.timeline');
	obj.html(res);
	$('#TAB_NAME').val('FOLLOW');
	coment_disp_init();
}

function uploads(url){
$('.upload').hide();
 $('.indexlists').upload(url,function(res) {
		if(res=='true'){
			popupclass.confirm_open('アップロードに成功しました', 'alert', function() { window.location.reload(); });
		}else{
			popupclass.confirm_open(res, 'alert', function() { $('.upload').show(); });
		}
    },'html');
	}
function message_send(request){
	var res = request.responseText;
	if(res=="success"){
		message_clear();
		popupclass.confirm_open('送信に成功しました。', 'alert');
	}else{
		$('#popup').html(res);
		popupclass.popup_open();
	}
}
function comment(request,tmlid){
	var res = request.responseText;
 	if(res.length<=0){
		popupclass.confirm_open('メッセージを入力して下さい。', 'alert');
 	}else if(res=='over'){
		popupclass.confirm_open('メッセージは1000文字以内です。', 'alert');
	}else if(res=='word'){
		popupclass.confirm_open('禁止ワードが含まれているため、投稿できません。', 'alert');
	}else{
		$('.comment_'+tmlid).html(res);
	}
}
function timeline_delete(request){
	var res = request.responseText;
	if(res=='false'){
		popupclass.confirm_open('削除に失敗しました', 'alert');
	}else if(res=='not'){
		popupclass.confirm_open('削除する権限がありません。', 'alert');
	}else{
		popupclass.confirm_open('削除しました', 'alert');
		var obj =$('.timeline_'+(res));
		obj.slideUp();
		obj.remove();
	}
	return false;
}
function _delete(request){
	var res = request.responseText;
	if(res=='false'){
		popupclass.confirm_open('削除に失敗しました', 'alert');
	}else if(res=='not'){
		popupclass.confirm_open('削除する権限がありません。', 'alert');
	}else{
		popupclass.confirm_open('削除しました', 'alert');
		var obj =$('.line_'+(res));
		obj.slideUp();
		obj.remove();
	}
	return false;
}
function request(){
	if(window.confirm("参加申請を許可しますか")){

	}else{

	}
}
function join_alert(tmp){
	if(tmp==2){
		if(window.confirm("グループを脱退しますか？")){
			return true;
		}else{
			return false;
		}
	}
	else if(tmp==1){
		if(window.confirm("申請を取り下げますか？")){
			return true;
		}else{
			return false;
		}
	}
	else{
		if(window.confirm("グループに参加しますか？")){
			return true;
		}else{
			return false;
		}
	}
}
function judge_finish(request){
	var res = request.responseText;
	if(res){
		popupclass.popup_close();
		var obj = $('.request_'+res);
		var obj2 = $('.request_num');
		var text = obj2.text();
		text = text.match("[0-9]+");
		if((text-1)>0){
			obj2.text((text-1));
		}else{
			obj2.remove();
			var obj3 = $('.subOkOn');
			obj3.removeClass('subOkOn');
			obj3.addClass('subOk');
		}
		obj.slideUp();
		obj.remove();
	}
}
function open_text(no){
	$('.open_'+no).hide();
	$('.hidden_text_'+no).show();
	return false;
}
function profile(request){
	var res = request.responseText;
	if(res=='1'){
		popupclass.confirm_open('変更を保存しました。', 'alert', function (){  window.location.reload(); });
	}else if(res=='2') {
		popupclass.confirm_open('パスワードを保存しました。', 'alert', function (){  window.location.reload(); });
	}else{
		$('#popup').html(res);
		popupclass.popup_open();
	}
}
function group_edit(request){
	var res = request.responseText;
	if(res=='1'){
		popupclass.confirm_open('グループの変更を保存しました。', 'alert', function (){  window.location.reload(); });
	}else{
		popupclass.confirm_open('グループの変更に失敗しました。', 'alert', function (){$('#popup').html(res);popupclass.popup_open();});
		
	}
}
function read(request,no){
	var res = request.responseText;
	$('.read_'+no).html(res);
}

function watch(request,no,stat){
	var res = request.responseText;
	$('.watch_'+no).html(res);
	if(stat!=null){
		var obj =$('.timeline_'+(no));
		obj.slideUp();
		obj.remove();
	}
}

function coment_disp(){
	var tex = $('.com_disp').attr('class');
	var com = tex.match('off');
	if(com=="off"){
		$('.comment_area').hide();
		$('.com_disp').removeClass('off');
		var src = $('.no_com_btn').attr('src');
		src = src.replace("bt_no_comment.gif", "bt_comment.gif");
		$('.no_com_btn').attr('src',src);
		$('#DISPLAY_COMMENT').val('off');
	}else{
		$('.com_disp').addClass('off');
		$('.comment_area').show();
		var src = $('.no_com_btn').attr('src');
		src = src.replace("bt_comment.gif","bt_no_comment.gif");
		$('.no_com_btn').attr('src',src);
		$('#DISPLAY_COMMENT').val('on');

	}
}
// コメントボタンの初期化
function coment_disp_init(){
	var tex = $('.com_disp').attr('class');
	var com = tex.match('off');
	if(com!="off"){
		$('.com_disp').addClass('off');
		var src = $('.no_com_btn').attr('src');
		src = src.replace("bt_comment.gif","bt_no_comment.gif");
		$('.no_com_btn').attr('src',src);
		$('#DISPLAY_COMMENT').val('on');
	}
}
function select(url,model,order,whiles,year,mon,day,tab_name){
	var param = eval({
		"model" : model,
		"order" : order,
		"while" : whiles,
		"tab_name" : tab_name
	});
	var url_tmp = url;
	if(year!=null&&mon!=null&&day!=null){
		url_tmp += "/"+year+"/"+mon+"/"+day;
	}
	$.post(url_tmp, {params:param}, function(d){
		var obj = $('.timeline');
		obj.html(d);
	});
}
function invite(request){
	var res = request.responseText;
	if(res=='1'){
		popupclass.fromPopup = false;
		popupclass.confirm_open('ユーザを招待しました。', 'alert', null, null, 'force');

	}else if(res=='2'){
		popupclass.confirm_open('何も入力されていません。', 'alert');
	}else{
		popupclass.confirm_open('招待メールを送信できませんでした。', 'alert', function() {$('#popup').html(res);});
	}
}
function group_invite_send(request){
	var res = request.responseText;
	if(res=='1'){
        popupclass.confirm_open('ユーザを招待しました。','alert' , function() {$('#popup').html("");popupclass.popup_close();});
	}else if(res=='2'){
		popupclass.confirm_open('ユーザを招待しました。\n招待通知メールの送信に失敗しました。','alert', function() {$('#popup').html("");popupclass.popup_close();});
	}else if(res =='3'){
		popupclass.confirm_open('何も入力されていません。', 'alert', null);
	}else if(res == '0'){
		popupclass.confirm_open('ユーザの招待に失敗しました。', 'alert', null);
	}else{
		popupclass.confirm_open('エラーが発生しました', 'alert', null);
	}
}
function message_save(){
	for(i=1;i<6;i++){
		var names = $('.name'+i).val();
		var id = $('#MessageID'+i).val();
		$.cookie("name_"+i,names,{ path: '/' });
		$.cookie("id_"+i,id,{ path: '/' });
	}
	var subject =$('.subject').val();
	$.cookie("subject",subject,{ path: '/' });
	var text =$('.message').val();
	$.cookie("message",text,{ path: '/' });
}

function user_add(request,no,id,names){
	var res = request.responseText;
	var obj = $('#popup');
	obj.html(res);
	if(no!=null){
		$.cookie("name_"+no,names,{ path: '/' });
		$.cookie("id_"+no,id,{ path: '/' });
	}
	for(i=1;i<6;i++){
		$('.name'+i).val($.cookie("name_"+i));
		$('#MessageID'+i).val($.cookie("id_"+i));
	}
	$('.subject').val($.cookie("subject"));
	$('.message').val($.cookie("message"));
}
function clear_address(num){
	$.cookie("name_"+num,'',{ path: '/' });
	$.cookie("id_"+num,'',{ path: '/' });
	$('.name'+num).val(null);
	$('#MessageID'+num).val(null);
}
function message_clear(){
	for(i=1;i<6;i++){
		var names = $('.name'+i).val();
		var id = $('#MessageID'+i).val();
		$.cookie("name_"+i,names,{ path: '/' });
		$.cookie("id_"+i,id,{ path: '/' });
	}
	$.cookie("subject",'',{ path: '/' });
	$.cookie("message",'',{ path: '/' });
}

function group_invite(request,names,no,id){
	var res;
	if(request.responseText != null) {
		res = request.responseText;
	}else {
		res = request;
	}

	var obj = $('#popup');
	obj.html(res);
	if(no!=null){
		$.cookie("name_"+no,names,{ path: '/' });
		$.cookie("id_"+no,id,{ path: '/' });
	}
	for(i=1;i<6;i++){
		$('.name'+i).val($.cookie("name_"+i));
		$('#GroupID'+i).val($.cookie("id_"+i));
	}
}
function group_inviteuser(url){
	for(i=1;i<6;i++){
		var names = $('.name'+i).val();
		var id = $('#GroupID'+i).val();
		$.cookie("name_"+i,names,{ path: '/' });
		$.cookie('id_'+i,id,{ path: '/' });
	}

	data = {
		'id1' : $('#GroupID'+1).val(),
		'id2' : $('#GroupID'+2).val(),
		'id3' : $('#GroupID'+3).val(),
		'id4' : $('#GroupID'+4).val(),
		'id5' : $('#GroupID'+5).val()
	};

	$.post(url, data, function(d){
		$('#popup').html(d);
	});
}
function g_a_clear(num){
	$.cookie("name_"+num,'',{ path: '/' });
	$.cookie("id_"+num,'',{ path: '/' });
	$('.name'+num).val(null);
	$('#GroupID'+num).val(null);
}
function notice_read(url,userid){
	var param = eval({
		"userid" : userid
	});
	var url_tmp = url+"/"+userid;
	$.post(url_tmp, {params:param}, function(d){
		if(d=='1'){
			var obj1 = $('.notice_num');
			obj1.remove();
			var obj2 = $('.subNotOn');
			obj2.removeClass('subNotOn');
			obj2.addClass('subNot');
		}
	});
}
function all_coment(tmlid,count){
	if($('.show_btn_'+tmlid).hasClass("on")){
		$('.hidden_com_'+tmlid).hide();
		$('.open_parent_'+tmlid).show();
		$('.hidden_text_parent_'+tmlid).hide();
		$('.show_btn_'+tmlid).children("p").children("a").text('コメントを'+count+'件を全て表示');
		$('.show_btn_'+tmlid).removeClass("on");
	}else{
		$('.hidden_com_'+tmlid).show();
		//$('.show_btn_'+tmlid).hide();
		$('.open_parent_'+tmlid).hide();
		$('.hidden_text_parent_'+tmlid).show();
		$('.show_btn_'+tmlid).children("p").children("a").text('新しいコメントだけ表示する');
		$('.show_btn_'+tmlid).addClass("on");
	}
	return false;
}
function follows(request,userid,stat){
	var res = request.responseText;
	var obj = $('.follow_'+userid);
	if(stat==1){
		$('.line_'+userid).slideUp();
		$('.line_'+userid).remove();
	}else{
		obj.html(res);
	}
}
function save_g_status(){
	var names = $('#GroupNAME').val();
	var type = $('input[name=data["Group"]["TYPE"]]:checked').val();
	var description = $('#GroupDESCRIPTION').val();
	var orner = $('#GroupORNER').val();
	var userid = $('#GroupUSRID').val();

	$.cookie("orner",orner);
	$.cookie("orner_id",userid);
	$.cookie("g_name",names);
	$.cookie("g_type",type);
	$.cookie("g_description",description);
}
function own_insert(request,name,id){
	var res = request.responseText;
	var obj = $('#popup');
	obj.html(res);

	$('#GroupNAME').val($.cookie("g_name"));
	if(id!=null){
		$.cookie("orner",name);
		$.cookie("orner_id",id);
	}
	$('#GroupORNER').val($.cookie("orner"));
	$('#GroupUSRID').val($.cookie("orner_id"));
	var tmp = $.cookie("g_type");

	if(tmp==1){
		$('#GroupTYPE0').attr('checked',null);
		$('#GroupTYPE1').attr('checked', 'checked');
	}else{
		$('#GroupTYPE1').attr('checked',null);
		$('#GroupTYPE0').attr('checked', 'checked');
	}
	$('#GroupDESCRIPTION').val($.cookie("g_description"));
}
function withdrawal_g(grpid){
		$('.line_'+grpid).slideUp();
		$('.line_'+grpid).remove();
}
