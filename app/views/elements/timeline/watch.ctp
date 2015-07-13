<!-- contents_Start -->
<?php
if ($list[0]['Watch']['MINE']) {
	echo $customJs->link('ウォッチリストから取り消す', array(
		'controller' => 'timelines',
		'action' => 'watch/' . $list[0]['Timeline']['TML_ID']
	), array(
		'method' => 'POST',
		'update' => null,
		'buffer' => false,
		'complete' => "watch(XMLHttpRequest," . $list[0]['Timeline']['TML_ID'] . ")"
	));
} else {
	echo $customJs->link('ウォッチリストに追加', array(
		'controller' => 'timelines',
		'action' => 'watch/' . $list[0]['Timeline']['TML_ID']
	), array(
		'method' => 'POST',
		'complete' => "watch(XMLHttpRequest," . $list[0]['Timeline']['TML_ID'] . ")",
		'update' => null,
		'buffer' => false
	));
}
?>
<!-- contents_End -->