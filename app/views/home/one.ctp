<!-- contents_Start -->
<?php
echo $html->css("timeline", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<div id="timelineName" class="clearfix">
	<p><?php
	echo $html->image(array(
		'controller' => 'storages',
		'action' => 'thumbnail/' . $user['User']['USR_ID']
	));
	?></p>
	<h2><?php
	echo h($user['User']['NAME']) . "さんのタイムライン";
	?></h2>
</div>
<div id="contentsLeft">
	<ul id="tab">
	</ul>
	<div id="tabBoxIndex">
		<div id="tabBoxIndexBtm">
			<div id="tabBoxIndexArea">
				<dl class="timeline">
		<?php
		echo $this->element("timeline/one_timeline", $list);
		?></div>
		</div>
	</div>
	<p class="pageTop">
		<a href="#top">上に戻る</a>
	</p>
</div>
<div id="contentsRight">
	<!-- #BeginLibraryItem "/Library/contentsRight.lbi" -->
	<!-- #EndLibraryItem -->
	<!-- InstanceEndEditable -->
	<!-- contents_End -->