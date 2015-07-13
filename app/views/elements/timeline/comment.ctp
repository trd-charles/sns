<!-- contents_Start -->
<?php
echo "<dd class='timeline_" . h($list['Timeline']['TML_ID']) . "  comment_area'>";
echo '<p class="repUser">' . $html->image(array(
	'controller' => 'storages',
	'action' => 'thumbnail/' . $list['User']['USR_ID']
), array(
	'style' => 'width:40px;height:40px'
)) . '</p>';
echo '<div class="repMid"><div class="repTop"><div class="repBtm clearfix">';
echo "<p class='UserName_rep'>" . $html->link($list["User"]["NAME"], array(
	'controller' => 'profiles',
	'action' => 'index/' . $list['User']['USR_ID']
)) . "</p>";
echo '<p class="txtMain">' . $customHtml->text_cut($list["Timeline"]["MESSAGE"], $list['Timeline']['TML_ID'], $list['Timeline']['VAL_ID']) . '</p>
	<div class="txtSub clearfix">';
echo "<p>" . $html->link($list["Timeline"]["INSERT_DATE"], array(
	'controller' => 'homes',
	'action' => 'one/' . $list['Timeline']['VAL_ID']
)) . "</p>";
echo '<ul>';
echo "<li class='readBtn read_" . h($list['Timeline']['TML_ID']) . "'>";

if (isset($list['READ']['MINE'])) {
	echo $customJs->link('読んだ!を取り消す', array(
		'controller' => 'timelines',
		'action' => 'read/' . $list['Timeline']['TML_ID']
	), array(
		'div' => false,
		'method' => 'POST',
		'update' => null,
		'buffer' => false,
		'complete' => "read(XMLHttpRequest," . $list['Timeline']['TML_ID'] . ")"
	));
} else {
	echo $customJs->link('読んだ!', array(
		'controller' => 'timelines',
		'action' => 'read/' . $list['Timeline']['TML_ID']
	), array(
		'div' => false,
		'method' => 'POST',
		'complete' => "read(XMLHttpRequest," . $list['Timeline']['TML_ID'] . ")",
		'update' => null,
		'buffer' => false
	));
}

if (isset($list['READ']['Count']) > 0 || isset($list['READ']['MINE'])) {
	echo "　";
	if ($list['READ']['MINE']) {
		echo $customJs->link(($list['READ']['Count'] + 1) . '人', array(
			'controller' => 'timelines',
			'action' => 'read_user/' . $list['Timeline']['TML_ID']
		), array(
			'div' => false,
			'update' => null,
			'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
			'method' => 'POST',
			'buffer' => false
		));
	} elseif ($list['READ']['Count'] == 3) {
		echo $customJs->link(($list['READ']['Count'] + 1) . '人', array(
			'controller' => 'timelines',
			'action' => 'read_user/' . $list['Timeline']['TML_ID']
		), array(
			'div' => false,
			'update' => null,
			'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
			'method' => 'POST',
			'buffer' => false
		));
	}
}
echo '</li>
	</ul>
	</div>
	</div></div></div>';
if ($list['Timeline']['USR_ID'] == $user['User']['USR_ID'] || $user['User']['AUTHORITY'] == User::AUTHORITY_TRUE) {
	echo "<p class='delete'>";
	echo $customJs->linkAfterConfirm($html->image('timeline/bt_delete.gif'), array(
		'controller' => 'timelines',
		'action' => 'delete/' . $list['Timeline']['TML_ID']
	), array(
		'escape' => false,
		'buffer' => false
	), array(
		'description' => '削除してよろしいですか？',
		'type' => 'confirm',
		'close' => false
	), array(
		'complete' => "function(data,textStatus,xhr){timeline_delete(xhr)}"
	));
}
echo "</dd>";
echo "<div class='comment_" . h($list['Timeline']['TML_ID']) . "'>";
echo "<div class='commentBtn display_" . h($list['Timeline']['TML_ID']) . "' style='padding-top:5px;'>" . $form->create('Timeline') . $form->textarea('COMMENT', array(
	'value' => false,
	'label' => false,
	'style' => 'width:520px;',
	'onkeyup' => 'changeTextAreaSize(this);'
)) . $form->hidden('TML_ID', array(
	'value' => $list['Timeline']['VAL_ID']
)) . $customHtml->hiddenToken() . $js->submit('timeline/bt_share.gif', array(
	'class' => 'CommentSMT',
	'url' => array(
		'controller' => 'timelines',
		'action' => 'comment'
	),
	'div' => false,
	'update' => null,
	'complete' => "comment(XMLHttpRequest," . $list['Timeline']['TML_ID'] . ")"
)) . $js->writeBuffer() . $form->end() . "</div>";
echo "</div>";
?>
<!-- contents_End -->
