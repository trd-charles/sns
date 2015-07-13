<!-- contents_Start -->
<div class="share">
	<div class="shareTop">
		<div class="shareBtm clearfix">
<?php
echo $form->create('Timeline');
?>
<?php

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
echo $customHtml->hiddenToken();
echo "<div class='upload_link'><p class='btnFile'>" . $customJs->link($html->image('timeline/bt_file.gif', array(
	'alt' => 'ファイルを送付'
)), array(
	'controller' => 'timelines',
	'action' => 'file/' . h($m_class) . '/' . h($groupid)
), array(
	'escape' => false,
	'complete' => "$('.upload_link').hide();",
	'update' => '.sub_form',
	'method' => 'POST',
	'buffer' => false
)) . "</p></div>";
echo "<span class='sub_form btnFile'>";
echo "</span>";
echo "</div>";
echo "<p class='btnShare'>" . $js->submit('timeline/bt_share.gif', array(
	'div' => false,
	'alt' => '共有する',
	'class' => "on upload",
	'update' => 'null',
	'before' => "$('.upload').hide();",
	"complete" => "var date_frag='" . $date_frag . "'; var url='" . Router::url(array(
		'controller' => 'timelines',
		'action' => 'message'
	)) . "';tl_upload(url,date_frag);return false;"
)) . "</p>";

echo $js->writeBuffer();
?>
<?php

echo $form->end();
?>
</div>
	</div>
</div>
<!-- contents_End -->