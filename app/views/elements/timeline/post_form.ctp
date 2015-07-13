<!-- contents_Start -->
<div class="share">
	<div class="shareTop">
		<div class="shareBtm clearfix">
<?php
if ($this->name == 'Group') {
	echo $form->create('Timeline', array(
		'url' => array(
			'controller' => 'groups',
			'action' => 'main',
			$this->params['pass'][0]
		),
		'enctype' => 'multipart/form-data',
		'action' => 'upload'
	));
} else {
	echo $form->create('Timeline', array(
		'url' => array(
			'controller' => 'homes',
			'action' => 'index'
		),
		'enctype' => 'multipart/form-data',
		'action' => 'upload'
	));
}
echo "<div class='indexlist'>";
echo $form->textarea('MESSAGE', array(
	'value' => false,
	'label' => false,
	'div' => false
));
echo "<br />";
echo $form->hidden('FIRST_ID', array(
	'value' => $first,
	'label' => false,
	'div' => false
));
echo $form->hidden('GRP_ID', array(
	'value' => $groupid,
	'label' => false,
	'div' => false
));
echo $form->hidden('M_CLASS', array(
	'value' => $m_class,
	'label' => false,
	'div' => false
));
echo $form->error('MESSAGE', array(
	'class' => 'errors ml20'
));
echo $customHtml->hiddenToken();
echo "<div class='upload_link'><p class='btnFile'>" . $customJs->link($html->image('timeline/bt_file.gif', array(
	'alt' => 'ファイルを送付'
)), array(
	'controller' => 'timelines',
	'action' => 'file/' . h($m_class) . '/' . h($groupid)
), array(
	'escape' => false,
	'complete' => "$('#upload_options').show();$('.upload_link').hide();",
	'update' => '.sub_form',
	'method' => 'POST',
	'buffer' => false
)) . "</p></div>";
echo "<span class='sub_form' style='float:left;padding:0px 0px 10px 20px;'></span>";
echo "</div>";
echo "<p class='btnShare'>";
echo $form->submit('timeline/bt_share.gif', array(
	'div' => false,
	'alt' => '共有する',
	'class' => "on upload",
	'onclick' => '$(".btnShare").hide();'
));
echo "</p>";
?>
<?php echo $form->end();?>
</div>
	</div>
</div>
<!-- contents_End -->
