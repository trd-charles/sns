<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php echo $html->css("user","stylesheet",array('media'=>'screen'))."\n"; ?>

<h2 class="mb10">ユーザ一覧</h2>
<div id="searchArea">
	<?php
	if (isset($keyword)) {
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'r') {
			$paginator->options(array(
				'url' => 'r/NAME:' . urlencode($keyword)
			));
		}
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
			$paginator->options(array(
				'url' => 'all/NAME:' . urlencode($keyword)
			));
		} else {
			$paginator->options(array(
				'url' => 's/NAME:' . urlencode($keyword)
			));
		}
	}
	echo $form->create('Friend', array(
		'type' => 'post',
		'action' => (isset($this->params['pass'][0])) ? 'index/' . $this->params['pass'][0] : 'index',
		'name' => 'FiendIndexForm'
	))?>
	<p>
		<?php
		echo $form->text('NAME', array(
			'class' => '_input'
		));
		?>
		<?php
		echo $customHtml->hiddenToken();
		?>
		<?php
		echo $form->submit('user/bt_search_user.jpg', array(
			'div' => false,
			'name' => 'submit',
			'alt' => '検索する',
			'class' => 'on'
		));
		?>
	</p>
	<?php
	echo $form->end();
	?>
</div>
<div id="contentsLeft">
	<div>
		<?php
		echo $paginator->counter(array(
			'format' => '%start%-'
		));
		echo $paginator->counter(array(
			'format' => '%end%人 / '
		));
		echo $paginator->counter(array(
			'format' => '%count%人'
		));
		?>
	</div>

	<ul id="tab">
		<?php
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'r') {
			echo '<li>' . $html->link("<span>フォローしている</span>", array(
				'action' => 'index/s'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li class="present">' . $html->link("<span>フォローされている</span>", array(
				'action' => 'index/r'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li>' . $html->link("<span>すべてのユーザを表示</span>", array(
				'action' => 'index/all'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
		} elseif (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
			echo '<li>' . $html->link("<span>フォローしている</span>", array(
				'action' => 'index/s'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li>' . $html->link("<span>フォローされている</span>", array(
				'action' => 'index/r'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li class="present">' . $html->link("<span>すべてのユーザを表示</span>", array(
				'action' => 'index/all'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
		} else {
			echo '<li class="present">' . $html->link("<span>フォローしている</span>", array(
				'action' => 'index/s'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li>' . $html->link("<span>フォローされている</span>", array(
				'action' => 'index/r'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li>' . $html->link("<span>すべてのユーザを表示</span>", array(
				'action' => 'index/all'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
		}
		?>

	</ul>
	<div id="tabBoxIndex">
		<!-- page1_Start -->
		<div id="page1" class="tabBox">
			<table cellpadding="0" cellspacing="0" class='wordBreak'>
				<?php
				foreach ($index_list as $key => $val) {
					?>
				<tr
					class="line_<?php
					echo h($val['Administrator']['USR_ID'])?>">
					<th><?php
					echo $html->image(array(
						'controller' => 'storages',
						'action' => 'thumbnail/' . $val['Administrator']['USR_ID']
					));
					?></th>
					<td class="userTxt wid400" style="padding: 0; vertical-align: top;">
						<div align="right" style="height: 22px; padding: 5px"><?php
					echo h($val['Friend']['FOLLOWED']) ? $html->image('common/followed.gif', array(
						'alt' => 'フォローされています',
						'style' => ''
					)) : '';
					?></div>
						<div style="margin-left: 22px; margin-right: 22px;">
					<?php
					echo $html->link($val["Administrator"]["NAME"], array(
						'controller' => 'profiles',
						'action' => 'index/' . $val['Administrator']['USR_ID']
					), array(
						'style' => ''
					));
					?><br />
					<?php
					echo $customHtml->text_cut($val['Administrator']['DESCRIPTION'], null, null, 105, false, false);
					?>
					</div>
						<div style="height: 22px"></div>
					</td>
					<td
						class="follow_<?php
					echo h($val['Administrator']['USR_ID'])?>"
						style='text-align: center; width: 80px'>
						<?php
					if (! isset($this->params['pass'][0]) || $this->params['pass'][0] == 's') {
						if ($val['Friend']['STATUS'] != null) {
							echo $customJs->linkAfterConfirm($html->image('user/bt_follow_on.gif', array(
								'alt' => '＋フォロー中',
								'class' => 'on'
							)), array(
								'controller' => 'friends',
								'action' => 'follow',
								h($val['Administrator']['USR_ID'])
							), array(
								'escape' => false,
								'buffer' => false
							), array(
								'description' => 'フォロー解除しますか？',
								'type' => 'confirm'
							), array(
								'complete' => "function(data,textStatus,xhr) {follows(xhr," . h($val['Administrator']['USR_ID']) . "," . h($val['Friend']['STATUS']) . ")}"
							));
						} else {
							echo $customJs->link($html->image('user/bt_follow.gif', array(
								'alt' => '＋フォローする'
							)), array(
								'controller' => 'friends',
								'action' => 'follow/' . h($val['Administrator']['USR_ID'])
							), array(
								'escape' => false,
								'method' => 'POST',
								'update' => null,
								'complete' => "follows(XMLHttpRequest," . h($val['Administrator']['USR_ID']) . "," . h($val['Friend']['STATUS']) . ");",
								'buffer' => false
							));
						}
					} else {
						if ($val['Friend']['STATUS'] != null) {
							echo $customJs->linkAfterConfirm($html->image('user/bt_follow_on.gif', array(
								'alt' => '＋フォロー中',
								'class' => 'on'
							)), array(
								'controller' => 'friends',
								'action' => 'follow',
								h($val['Administrator']['USR_ID'])
							), array(
								'escape' => false,
								'buffer' => false
							), array(
								'description' => 'フォロー解除しますか？',
								'type' => 'confirm'
							), array(
								'complete' => "function(data,textStatus,xhr) {follows(xhr, " . h($val['Administrator']['USR_ID']) . ", 0)}"
							));
						} else {
							echo $customJs->link($html->image('user/bt_follow.gif', array(
								'alt' => '＋フォローする'
							)), array(
								'controller' => 'friends',
								'action' => 'follow/' . h($val['Administrator']['USR_ID'])
							), array(
								'escape' => false,
								'method' => 'POST',
								'update' => null,
								'complete' => "follows(XMLHttpRequest," . h($val['Administrator']['USR_ID']) . ", 0);",
								'buffer' => false
							));
						}
					}
					?>
					</td>
				</tr>
				<?php }?>
			</table>
		</div>
	</div>
	<div id='pagination'>
		<?php
		echo $paginator->prev('<< ' . __('前へ', true), array(), null, array(
			'class' => 'disabled',
			'tag' => 'span'
		));
		?>
		 |
		<?php
		echo $paginator->numbers() . ' | ' . $paginator->next(__('次へ', true) . ' >>', array(), null, array(
			'tag' => 'span',
			'class' => 'disabled'
		));
		?>
	</div>
</div>
<?php
echo $this->element('right_menu');
?>
<!-- #EndLibraryItem -->
<!-- InstanceEndEditable -->
<!-- contents_End -->
