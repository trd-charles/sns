<!-- contents_Start -->
<h3>参加しているグループ<?php
echo "(" . h($group[0]['Count']) . ")"?></h3>
<div class="sideMid">
	<div class="sideBtm">
		<table cellpadding="0" cellspacing="0" class='wordBreak'>
<?php
foreach ($group as $key => $val) {
	if (isset($val['Group'])) {
		echo "<tr>";
		echo "<th>" . $html->image(array(
			'controller' => 'storages',
			'action' => 'group_thumbnail/' . $val['Group']['GRP_ID']
		), array(
			'style' => 'width:40px;height:40px'
		)) . "<th>";
		echo "<td>" . $html->link($val["Group"]["NAME"], array(
			'controller' => 'groups',
			'action' => 'main/' . $val['Group']['GRP_ID']
		)) . "</td>";
		echo "</tr>";
	}
}
?>
</table>
<?php
if ($paginator->hasNext("Group")) {
	echo '<p class="moreBtnS">';
	if (isset($profile)) {
		echo $customJs->link($html->image('common/bt_more_s.gif', array(
			'alt' => 'もっと見る'
		)), array(
			'controller' => 'groups',
			'action' => 'join_group/' . $profile['User']['USR_ID']
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
			'controller' => 'groups',
			'action' => 'join_group/' . $user['User']['USR_ID']
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
