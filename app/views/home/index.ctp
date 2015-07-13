<!-- contents_Start -->
<?php
echo $session->flash();
?>
<?php

echo $html->css("timeline", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<div id="timelineName" class="clearfix">
	<p><?php
	echo $html->link($html->image(array(
		'controller' => 'storages',
		'action' => 'thumbnail/' . $user['User']['USR_ID']
	)), array(
		'controller' => 'profiles'
	), array(
		'escape' => false
	));
	?></p>
	<h2><?php
	echo h($user['User']['NAME']) . "さんのタイムライン";
	?></h2>

</div>
<div id="contentsLeft">
<?php
echo $this->element("timeline/post_form")?>
<div class='newpost'></div>
	<ul id="tab">
		<li class="present protimefollow">
		<?php
		echo $customJs->link('<span>ホーム</span>', array(
			'controller' => 'homes',
			'action' => 'follow'
		), array(
			'escape' => false,
			'div' => false,
			'update' => null,
			'complete' => 'TlReload_tf(XMLHttpRequest);',
			'buffer' => false,
			'method' => 'POST'
		));
		?>
		</li>
		<li class="protimeonly">
		<?php
		echo $customJs->link('<span>アクション</span>', array(
			'controller' => 'homes',
			'action' => 'only'
		), array(
			'escape' => false,
			'div' => false,
			'update' => null,
			'complete' => 'TlReload_to(XMLHttpRequest);',
			'buffer' => false,
			'method' => 'POST'
		));
		?>
		</li>
		<li class="protimegroup">
		<?php
		echo $customJs->link('<span>グループ</span>', array(
			'controller' => 'homes',
			'action' => 'group'
		), array(
			'escape' => false,
			'div' => false,
			'update' => null,
			'complete' => 'TlReload_tg(XMLHttpRequest);',
			'buffer' => false,
			'method' => 'POST'
		));
		?>
		</li>
		<li class="prowatch"><?php
		echo $customJs->link('<span>ウォッチリスト</span>', array(
			'controller' => 'timelines',
			'action' => 'watch_list'
		), array(
			'escape' => false,
			'div' => false,
			'update' => null,
			'complete' => 'TlReload_tw(XMLHttpRequest);',
			'buffer' => false,
			'method' => 'POST'
		));
		?></li>
		<li class="protime">
		<?php
		echo $customJs->link('<span>オール</span>', array(
			'controller' => 'homes',
			'action' => 'all'
		), array(
			'escape' => false,
			'div' => false,
			'update' => null,
			'complete' => 'TlReload_tt(XMLHttpRequest);',
			'buffer' => false,
			'method' => 'POST'
		));
		?>
		</li>
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
			echo $form->hidden('TAB_NAME', array(
				'value' => $tab_name,
				'label' => false,
				'div' => false
			));
			?>
			<?php
			$js->get('.order')->event('change', 'var url = "' . Router::url(array(
				'controller' => 'timelines',
				'action' => 'select/' . $user['User']['USR_ID']
			), true) . '";var model="home";var order = $(".order").val();var whiles = $(".while").val();tab_name = $("#TAB_NAME").val(); select(url,model,order,whiles,null,null,null,tab_name);');
			$js->get('.while')->event('change', 'var url = "' . Router::url(array(
				'controller' => 'timelines',
				'action' => 'select/' . $user['User']['USR_ID']
			), true) . '";var model="home";var order = $(".order").val();var whiles = $(".while").val();tab_name = $("#TAB_NAME").val(); select(url,model,order,whiles,null,null,null,tab_name);');
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
					<div class='hidden_newid' style='display: none'><?php
					echo h($first)?></div>
		<?php
		echo $this->element("timeline/timeline", $list)?>
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

