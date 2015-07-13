<!-- contents_Start -->
<?php
echo $html->css("profile", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<h2 class="mb10"><?php
echo h($name) . "さんのプロフィール";
?></h2>
<div id="contentsLeft">
	<div id="profileArea" class="clearfix">
	<?php
	echo $this->element("../profile/profile", $profile)?>
	</div>
	<ul id="tab">
		<?php
		if ($user['User']['USR_ID'] == $profile['User']['USR_ID']) {
			?>
			<li class="present protime">
			<?php
			echo $customJs->link('<span>タイムライン</span>', array(
				'controller' => 'profiles',
				'action' => 'all'
			), array(
				'escape' => false,
				'div' => false,
				'update' => null,
				'complete' => 'TlReload_t(XMLHttpRequest);',
				'buffer' => false,
				'method' => 'POST'
			));
			?>
			</li>
		<?php }?>
	</ul>
	<div id="tabBoxIndex">
		<div id="tabBoxIndexBtm">
			<div id="tabBoxIndexArea">
				<div class="sort clearfix">
					<p class="sort_ref">表示順
			<?php
			echo $form->input('ORDER', array(
				'div' => false,
				'label' => false,
				'options' => array(
					'新しい発言/コメント',
					'新しい発言',
					'古い発言',
					'読んだが多い'
				),
				'class' => 'order',
				'size' => "1"
			));
			?>
			絞込
			<?php
			echo $form->input('WHILE', array(
				'div' => false,
				'label' => false,
				'options' => array(
					'全て表示',
					'発言',
					'ファイル'
				),
				'class' => 'while',
				'size' => "1"
			));
			?>
			<?php
			$js->get('.order')->event('change', 'var url = "' . Router::url(array(
				'controller' => 'timelines',
				'action' => 'select/' . $profile['User']['USR_ID']
			), true) . '";var model="profile";var order = $(".order").val();whiles = $(".while").val();select(url,model,order,whiles,null,null,null);');
			$js->get('.while')->event('change', 'var url = "' . Router::url(array(
				'controller' => 'timelines',
				'action' => 'select/' . $profile['User']['USR_ID']
			), true) . '";var model="profile";var order = $(".order").val();whiles = $(".while").val();select(url,model,order,whiles,null,null,null);');
			echo $js->writeBuffer();
			?>
		</p>
					<p class="noComment"><?php
					echo $customJs->link($html->image('timeline/bt_no_comment.gif', array(
						'alt' => 'コメントを表示しない',
						'class' => 'no_com_btn'
					)), array(), array(
						'escape' => false,
						'class' => 'com_disp off',
						'complete' => 'coment_disp();return false;',
						'method' => 'POST',
						'buffer' => false
					));
					?></p>
				</div>
				<dl class="timeline">
					<div class='hideden_newid' style='display: none'><?php
					echo h($first)?></div>
				<?php
				echo $this->element("timeline/timeline", $list);
				?>
			</div>
		</div>
	</div>
	<p class="pageTop">
		<a href="#top">上に戻る</a>
	</p>
</div>
<?php
echo $this->element('right_menu_timeline');
?>
<!-- #EndLibraryItem -->
<!-- InstanceEndEditable -->
<!-- contents_End -->