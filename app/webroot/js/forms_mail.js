$(function(){
	var smtp_auth_selected = $(".smtp_auth_setting:checked");
	smtp_auth_set(smtp_auth_selected.val());

	var smtp_selected = $(".smtp_setting:checked");
	smtp_set(smtp_selected.val());

	$(".smtp_setting").click(function () {
		smtp_auth_selected = $(".smtp_auth_setting:checked");
		smtp_auth_set(smtp_auth_selected.val());
		smtp_set($(this).val());
	});

	$(".smtp_auth_setting").click(function () {
		smtp_auth_set($(this).val());
	});
});


//　SMTP設定
function smtp_set(type) {
	switch(type){
		case "0"://なし
			$(".smtp_set").attr("disabled", true);
			$(".smtp_auth_set").attr("disabled", true);
			$(".i_smtp_set").hide();
			$(".i_smtp_auth_set").hide();
			break;
		case "1"://あり
			$(".smtp_set").removeAttr('disabled');
			$(".i_smtp_set").show();
			break;
	}
}

//　SMTP_AUTHの設定
function smtp_auth_set(type) {
	switch(type){
		case "0"://SMTP
			$(".smtp_auth_set").attr("disabled", true);
			$(".i_smtp_auth_set").hide();
			break;
		case "1"://SMTP_AUTH
			$(".smtp_auth_set").removeAttr('disabled');
			$(".i_smtp_auth_set").show();
			break;

	}
}