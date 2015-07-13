<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php
echo $html->css("profile", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<h2 class="mb10"><?php
echo h($group['Group']['NAME']);
?></h2>
<div id="contentsLeft">
	<div id="profileArea" class="clearfix">
	<?php
	echo $this->element("../group/profile", $group, $edit_auth, $group_admin_name)?>
	</div>
	<?php
	if ($g_auth) {
		echo $this->element("timeline/post_form");
	}
	?>
	<div class='newpost'></div>
	<ul id="tab">
		<li class="present"><span><p class='tabmoji'>タイムライン</p></span></li>
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
				'action' => 'select/' . $group['Group']['GRP_ID']
			), true) . '";var model="group";var order = $(".order").val();whiles = $(".while").val();select(url,model,order,whiles,null,null,null);');
			$js->get('.while')->event('change', 'var url = "' . Router::url(array(
				'controller' => 'timelines',
				'action' => 'select/' . $group['Group']['GRP_ID']
			), true) . '";var model="group";var order = $(".order").val();whiles = $(".while").val();select(url,model,order,whiles,null,null,null);');
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
		echo $this->element("timeline/timeline", $list)?>
			</div>
		</div>
	</div>
	<p class="pageTop">
		<a href="#top">上に戻る</a>
	</p>
</div>
<div id="contentsRight">
	<!-- #BeginLibraryItem "/Library/contentsRight.lbi" -->
	<div class="calendarSide">
		<h3>日にちで発言を絞る</h3>
		<div class="sideMid">
			<div class="sideBtm">
				<div class='maincalender'><?php
				echo $this->element("calender")?></div>
				<p class="cancelBtn"></p>
			</div>
		</div>
	</div>
	<div class="userSide"><?php
	echo $this->element("../group/join_user", $join_user)?></div>
	<p class="paySide">
	<?php
	echo $html->link($html->image('common/bnr_pay.jpg', array(
		'alt' => '抹茶シリーズ有償サービスのご案内',
		'class' => 'on'
	)), 'http://oss.icz.co.jp/', array(
		'escape' => false,
		'target' => '_blank'
	));
	?>
</p>
</div>
<!-- contents_End -->