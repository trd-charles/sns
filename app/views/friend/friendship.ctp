<!-- contents_Start -->
<script>
	initRollovers();
</script>
<?php
if ($result != 0) {
	echo $customJs->linkAfterConfirm((($result != 0) ? $html->image('user/bt_follow_on.gif', array(
		'alt' => '＋フォローする',
		'class' => 'on'
	)) : $html->image('user/bt_follow.gif', array(
		'alt' => '＋フォローする'
	))), array(
		'controller' => 'friends',
		'action' => 'follow',
		$userid
	), array(
		'escape' => false,
		'buffer' => false
	), array(
		'description' => 'フォロー解除しますか？',
		'type' => 'confirm'
	), array(
		'complete' => "function(data,textStatus,xhr) {follows(xhr," . $userid . ",0)}"
	));
} else {
	echo $customJs->link($result != 0 ? $html->image('user/bt_follow_on.gif', array(
		'alt' => '＋フォローする',
		'class' => 'on'
	)) : $html->image('user/bt_follow.gif', array(
		'alt' => '＋フォローする'
	)), array(
		'controller' => 'friends',
		'action' => 'follow/' . $userid
	), array(
		'before' => ($result != null) ? "if(!window.confirm('フォロー解除しますか？')){return false}" : true,
		'escape' => false,
		'method' => 'POST',
		'update' => null,
		'complete' => "follows(XMLHttpRequest," . $userid . ",0);",
		'buffer' => false
	));
}

?>
<!-- contents_End -->