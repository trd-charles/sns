<!-- contents_Start -->
<?php
$count = $join_user_num;
?>
<h3>グループ参加ユーザ<?php echo "(".h($count).")"?></h3>
<div class="sideMid">
	<div class="sideBtm">
		<table cellpadding="0" cellspacing="0" class='wordBreak'>
<?php
foreach ($join_user as $key => $val) {
	if ($key < 5) {
		echo "<tr>";
		echo "<th>" . $html->image(array(
			'controller' => 'storages',
			'action' => 'thumbnail/' . $val['User']['USR_ID']
		), array(
			'style' => 'width:40px;height:40px'
		)) . "</th><td>" . $html->link($val["User"]["NAME"], array(
			'controller' => 'profiles',
			'action' => 'index/' . $val['User']['USR_ID']
		)) . "</td>";
		echo "</tr>";
	}
}
?>
</table>
<?php
if ($count > 5) {
	echo '<p class="moreBtnS">';
	echo $customJs->link($html->image('common/bt_more_s.gif', array(
		'alt' => 'もっと見る'
	)), array(
		'controller' => 'groups',
		'action' => 'join_user/' . $group['Group']['GRP_ID']
	), array(
		'escape' => false,
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false
	));
	echo '</p>';
}
?>
</div>
</div>
<!-- contents_End -->
