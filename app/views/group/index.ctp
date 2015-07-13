<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php
echo $html->css("user", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>

<h2 class="mb20">グループー覧</h2>
<div id="searchArea">
	<?php
	if (isset($keyword)) {
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
			$paginator->options(array(
				'url' => 'all/NAME:' . urlencode($keyword)
			));
		} else {
			$paginator->options(array(
				'url' => 'NAME:' . urlencode($keyword)
			));
		}
	}
	echo $form->create('Group', array(
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
	<ul id="tab">
		<?php
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
			echo '<li><span>' . $html->link("<span>参加グループ</span>", array(
				'controller' => 'groups',
				'action' => 'index'
			), array(
				'escape' => false,
				'div' => false
			)) . '</span></li>' . '<li class="present">' . $html->link("<span>すべてのグループ</span>", array(
				'controller' => 'groups',
				'action' => 'index/all'
			), array(
				'escape' => false,
				'div' => false
			)) . '</span></li>';
		} else {
			echo '<li class="present">' . $html->link("<span>参加グループ</span>", array(
				'controller' => 'groups',
				'action' => 'index'
			), array(
				'escape' => false,
				'div' => false
			)) . '</span></li>' . '<li>' . $html->link("<span>すべてのグループ</span>", array(
				'controller' => 'groups',
				'action' => 'index/all'
			), array(
				'escape' => false,
				'div' => false
			)) . '</span></li>';
		}
		?>
		<li><?php
		echo $html->link($html->image('group/bt_group.gif', array(
			'alt' => 'グループを作成する'
		)), array(
			'controller' => 'groups',
			'action' => 'create'
		), array(
			'escape' => false,
			'div' => false,
			'id' => 'create_group'
		));
		?></li>
	</ul>
	<div id="tabBoxIndex">
		<!-- page1_Start -->
		<div id="page1" class="tabBox">
			<table cellpadding="0" cellspacing="0" class='wordBreak'>
				<?php
				foreach ($index_list as $key => $val) {
					?>
				<tr class='line_<?php
					echo h($val['Group']['GRP_ID']);
					?>'>
					<th><?php
					echo $html->image(array(
						'controller' => 'storages',
						'action' => 'group_thumbnail/' . $val['Group']['GRP_ID']
					));
					?></th>
					<td class="userTxt wid400"><strong>
					<?php
					if ($val['Group']['TYPE'] == Group::TYPE_PRIVATE) {
						if ($val['Join']['STATUS'] == Join::STATUS_JOINED || $val['Join']['STATUS'] == Join::STATUS_ADMINISTRATOR) {
							echo $html->link($val["Group"]["NAME"], array(
								'controller' => 'groups',
								'action' => 'main/' . $val['Group']['GRP_ID']
							));
						} else {
							echo "</strong>";
							echo (h($val['Group']['NAME'])) ? $customHtml->text_cut($val['Group']['NAME'], null, null, 50, true, false) : "&nbsp";
							echo "<strong>";
						}
					} else {
						echo $html->link($val["Group"]["NAME"], array(
							'controller' => 'groups',
							'action' => 'main/' . $val['Group']['GRP_ID']
						));
					}
					?>
					</strong><br />
					<?php
					echo (h($val['Group']['DESCRIPTION'])) ? $customHtml->text_cut($val['Group']['DESCRIPTION'], null, null, 50, true, false) : "&nbsp";
					?>
					</td>
					<td><p class="ml5"><?php
					echo h($group_type[$val['Group']['TYPE']])?>
						<p></td>
					<?php
					echo "<td><div class='join_" . h($val['Group']['GRP_ID']) . "'>";
					echo "<p class='ml5'>";
					if ($val['Join']['STATUS'] != 0) {
						if ($val['Join']['STATUS'] == 2) {
							// 脱退
							$message = 'グループを脱退しますか？';
						} else 
							if ($val['Join']['STATUS'] == 1) {
								// 申請取り消し
								$message = '申請を取り下げますか？';
							} else {
								// 参加
								if ($val['Group']['TYPE'] == 0) {
									$message = 'グループに参加しますか？';
								} elseif ($val['Group']['TYPE'] == 1) {
									$message = 'グループに参加申請しますか？';
								}
							}
						
						echo $customJs->linkAfterConfirm($group_status[$val['Join']['STATUS']], array(
							'controller' => 'groups',
							'action' => 'join',
							$val['Group']['GRP_ID']
						), array(
							'buffer' => false
						), array(
							'description' => $message,
							'type' => 'confirm'
						), array(
							'complete' => "function(data, textStatus, xhr) { $('.join_" . $val['Group']['GRP_ID'] . "').html(data); " . ((isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') ? '' : 'withdrawal_g(' . $val['Group']['GRP_ID'] . ')') . "}"
						));
					} else {
						echo $group_status[$val['Join']['STATUS']];
					}
					echo "<p></div></td>";
					?>
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
<!-- contents_End -->
