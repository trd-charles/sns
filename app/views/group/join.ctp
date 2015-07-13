<!-- contents_Start -->
<?php
if ($result == 2) {
	// 脱退
	$message = 'グループを脱退しますか？';
} else 
	if ($result == 1) {
		// 申請取り消し
		$message = '申請を取り下げますか？';
	} else {
		// 参加
		$message = 'グループに参加しますか？';
	}

echo $customJs->linkAfterConfirm($group_status[$result], array(
	'controller' => 'groups',
	'action' => 'join',
	$groupid
), array(
	'buffer' => false
), array(
	'description' => $message,
	'type' => 'confirm'
), array(
	'complete' => "function(data, textStatus, xhr) { $('.join_" . $groupid . "').html(data);}"
));
?>
<!-- contents_End -->