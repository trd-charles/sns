<!-- contents_Start -->
<?php
if ($public != 0) {
	echo $customJs->linkAfterConfirm($files_status[$public], array(
		'controller' => 'storages',
		'action' => 'c_public',
		$fleid
	), array(
		'buffer' => false
	), array(
		'description' => 'ファイルを非公開にすると他人から見えませんがよろしいですか？',
		'type' => 'confirm'
	), array(
		'complete' => "function(data, textStatus, xhr) { $('.public_" . $fleid . "').html(data) }"
	));
} else {
	echo $customJs->linkAfterConfirm($files_status[$public], array(
		'controller' => 'storages',
		'action' => 'c_public',
		$fleid
	), array(
		'buffer' => false
	), array(
		'description' => 'ファイルを公開にすると他人から見えるようになりますが、よろしいですか？',
		'type' => 'confirm'
	), array(
		'complete' => "function(data, textStatus, xhr) { $('.public_" . $fleid . "').html(data) }"
	));
}
?>
<!-- contents_End -->