<!-- contents_Start -->
<?php
if ($list[0]['READ']['MINE']) {
	echo $customJs->link('読んだ!を取り消す', array(
		'controller' => 'timelines',
		'action' => 'read/' . $list[0]['Timeline']['TML_ID']
	), array(
		'div' => false,
		'method' => 'POST',
		'update' => null,
		'buffer' => false,
		'complete' => "read(XMLHttpRequest," . $list[0]['Timeline']['TML_ID'] . ")"
	));
} else {
	echo $customJs->link('読んだ!', array(
		'controller' => 'timelines',
		'action' => 'read/' . $list[0]['Timeline']['TML_ID']
	), array(
		'div' => false,
		'method' => 'POST',
		'complete' => "read(XMLHttpRequest," . $list[0]['Timeline']['TML_ID'] . ")",
		'update' => null,
		'buffer' => false
	));
}

if ($list[0]['READ']['Count'] > 0 || $list[0]['READ']['MINE']) {
	echo "　";
	if ($list[0]['READ']['MINE']) {
		echo $customJs->link(($list[0]['READ']['Count'] + 1) . '人', array(
			'controller' => 'timelines',
			'action' => 'read_user/' . $list[0]['Timeline']['TML_ID']
		), array(
			'div' => false,
			'update' => null,
			'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
			'method' => 'POST',
			'buffer' => false
		));
	} else {
		echo $customJs->link(($list[0]['READ']['Count']) . '人', array(
			'controller' => 'timelines',
			'action' => 'read_user/' . $list[0]['Timeline']['TML_ID']
		), array(
			'div' => false,
			'update' => null,
			'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
			'method' => 'POST',
			'buffer' => false
		));
	}
}
?>
<!-- contents_End -->