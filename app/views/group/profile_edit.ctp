<!-- contents_Start -->
<!-- 使ってない？ -->

<?php
echo '<div style="float:left;margin-right: 1em;">';
echo $html->image("../files/" . $group['Group']['DIRECTORY1'] . "/" . $group['Group']['DIRECTORY2'] . "/thumbnail/" . $group['Group']['THUMBNAIL'], array(
	'style' => 'width:50px;height:50px;',
	'class' => 'thumbnail'
));
echo '</div>';
echo '<table>';
echo "<tr>";
echo "<td>グループ名</td>";
echo "<td>" . h($group['Group']['NAME']) . "</td>";
echo "</tr>";
echo "<tr>";
echo "<td>管理者</td>";
echo "<td>" . h($group['User']['NAME']) . "</td>";
echo "</tr>";
echo "<td>作成日</td>";
echo "<td>" . h($group['Group']['INSERT_DATE']) . "</td>";
echo "</tr>";
echo "<td>グループ概要</td>";
echo "<td>" . h($group['Group']['DESCRIPTION']) . "</td>";
echo "</tr>";
echo "</table>";
echo "<br style='clear: both;'>";
if ($user['User']['USR_ID'] == $group['Group']['USR_ID']) {
	echo $customJs->link("変更する", array(
		'controller' => 'groups',
		'action' => 'edit_open'
	), array(
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false
	));
	echo "<br />";
	echo $customJs->link("画像を変える", array(
		'controller' => 'groups',
		'action' => 'image'
	), array(
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false
	));
}
?>

<!-- contents_End -->
