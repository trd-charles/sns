<!-- contents_Start -->
<h3>フォローされているユーザ<?php
echo "(" . h($follower_user[0]['Count']) . ")"?></h3>
<div class="sideMid">
	<div class="sideBtm">
		<table cellpadding="0" cellspacing="0" class='wordBreak'>
<?php
$i = 0;
foreach ($follower_user as $key => $val) {
	if (isset($val['Administrator'])) {
		if ($key < 5) {
			echo "<tr>";
			echo "<th>" . $html->image(array(
				'controller' => 'storages',
				'action' => 'thumbnail/' . $val['Administrator']['USR_ID']
			), array(
				'style' => 'width:40px;height:40px;'
			)) . "</th><td>" . $html->link($val["Administrator"]["NAME"], array(
				'controller' => 'profiles',
				'action' => 'index/' . $val['Administrator']['USR_ID']
			)) . "</td>";
			echo "</tr>";
		}
	}
}
?>
</table>
<?php
if ($follower_user[0]['Count'] > 5) {
	echo '<p class="moreBtnS">';
	if (isset($profile)) {
		echo $customJs->link($html->image('common/bt_more_s.gif', array(
			'alt' => 'もっと見る'
		)), array(
			'controller' => 'timelines',
			'action' => 'follower_user/' . $profile['User']['USR_ID']
		), array(
			'escape' => false,
			'update' => null,
			'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
			'method' => 'POST',
			'buffer' => false
		));
	} else {
		echo $customJs->link($html->image('common/bt_more_s.gif', array(
			'alt' => 'もっと見る'
		)), array(
			'controller' => 'timelines',
			'action' => 'follower_user/' . $user['User']['USR_ID']
		), array(
			'escape' => false,
			'update' => null,
			'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
			'method' => 'POST',
			'buffer' => false
		));
	}
	echo '</p>';
}
?>
</div>
</div>
<!-- contents_End -->