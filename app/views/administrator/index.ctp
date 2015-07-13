<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php
echo $html->css("setup", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<!-- InstanceBeginEditable name="contents" -->
<h2 class="mb20">システム管理</h2>

<!-- ユーザ検索 -->

<ul id="tab"
	style="border-bottom: 5px solid #3C6937; margin-bottom: 0px; width: 960px">
	<li class="present"><a href="javascript:void(0);"
		data-tor-smoothScroll="noSmooth"><span>ユーザ管理</span> </a></li>
	<li><?php
	echo $html->link('<span>システム設定</span>', array(
		'controller' => 'configurations',
		'action' => 'index'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>プラグイン管理</span>', array(
		'controller' => 'plugins',
		'action' => 'index',
		'plugin' => null
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>ログ削除</span>', array(
		'controller' => 'administrators',
		'action' => 'delete_log',
		'plugin' => null
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>アクセス制限</span>', array(
		'controller' => 'configurations',
		'action' => 'access'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>メール設定</span>', array(
		'controller' => 'configurations',
		'action' => 'mail'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
</ul>

<div id="contentsCenter" class="mb60">
	<div id="contentsCenterTop">
		<div id="contentsCenterBtm">
			<div class="setupArea">

				<div style="line-height: 20px">

			<?php
			if (isset($search)) {
				$paginator->options(array(
					'url' => $search
				));
			}
			echo $form->create('Administrator', array(
				'type' => 'post',
				'action' => '.',
				'style' => 'display:inline'
			));
			?>
			<div style="float: left; width: 330px;">
						<div
							style="font-weight: bold; width: 160px; float: left; margin-bottom: 5px;">名前またはメールアドレス</div>
						<div
							style="font-weight: bold; width: 170px; float: left; margin-bottom: 5px;">
					<?php
					echo $form->text('Administrator.NAME', array(
						'class' => '_input'
					));
					?>
				</div>
						<div
							style="font-weight: bold; width: 160px; clear: both; float: left; margin-bottom: 20px;">ステータス</div>
						<div
							style="font-weight: bold; width: 170px; float: left; margin-bottom: 20px;">

					<?php
					echo $form->select('Administrator.STATUS', array(
						User::STATUS_ALL => '全て',
						User::STATUS_ENABLED => '有効',
						User::STATUS_WITHDRAWN => '退会済み',
						User::STATUS_WAITING_ACCEPTANCE => '承認待ち',
						User::STATUS_WAITING_ACTIVATION => '有効化待ち'
					), isset($status) ? $status : User::STATUS_ALL, array(
						'empty' => false,
						'style' => 'width:153px'
					), null);
					?>

				</div>
					</div>
					<div style="float: left; width: 330px;">
				<?php
				echo $form->submit('user/bt_search_user.jpg', array(
					'div' => false,
					'name' => 'submit',
					'alt' => 'ユーザ検索する',
					'class' => 'on',
					'style' => ''
				));
				?>
			</div>
			<?php
			echo $customHtml->hiddenToken();
			?>
		</div>
		<?php
		echo $form->end();
		?>
		<div align="right" style="float: left; width: 210px">
			<?php
			echo $html->link($html->image('setup/bt_register.jpg', array(
				'alt' => '新規登録',
				'class' => 'on'
			)), array(
				'action' => 'add'
			), array(
				'escape' => false,
				'div' => false
			));
			?>
		</div>

				<table cellpadding="0" cellspacing="0" class="SetupTable wordBreak"
					style="margin-top: 30px;">
					<tr>
						<th>サムネイル</th>
						<th>名前</th>
						<th>メールアドレス</th>
						<th>所属</th>
						<th>ステータス</th>
						<th>データ削除</th>
						<th>権限</th>
					</tr>
			<?php
			if ($index_list) {
				foreach ($index_list as $key => $val) {
					echo "<tr class='odd'>";
					
					/* サムネイル */
					if ($val['Administrator']['DIRECTORY1'] != null) {
						echo "<td style='text-align:center' width='80px'>" . $html->image(array(
							'controller' => 'storages',
							'action' => 'thumbnail/' . $val['Administrator']['USR_ID']
						), array(
							'width' => '60px',
							' height' => '60px'
						)) . "</td>";
					} else {
						echo "<td style='text-align:center' width='80px'>" . $html->image('common/thumbnail', array(
							'width' => '60px',
							'height' => '60px'
						)) . "</td>";
					}
					
					/* 名前 */
					if ($val['Administrator']['STATUS'] != User::STATUS_ENABLED) {
						echo "<td width='120px'>" . h($val['Administrator']['NAME']) . "</td>";
					} else {
						echo "<td width='120px'>" . $html->link($val['Administrator']['NAME'], array(
							'action' => 'edit/' . $val['Administrator']['USR_ID']
						)) . "</td>";
					}
					
					/* メールアドレス */
					echo "<td width='150px'>" . ($val['Administrator']['MAIL'] ? h($val['Administrator']['MAIL']) : "&nbsp;") . "</td>";
					
					/* 所属 */
					echo "<td width='150px'>" . ($val['Administrator']['UNIT'] ? h($val['Administrator']['UNIT']) : "&nbsp;") . "</td>";
					
					/* ステータス */
					echo "<td width='120px' class='valid_" . h($val['Administrator']['USR_ID']) . "'>";
					if ($val['Administrator']['STATUS'] == User::STATUS_ENABLED) {
						echo "<p>有効</p>";
						if ($val['Administrator']['AUTHORITY'] != 0) {
							echo $customJs->linkAfterConfirm('退会させる', array(
								'controller' => 'Administrators',
								'action' => 'valid/' . $val['Administrator']['USR_ID']
							), array(
								'escape' => false,
								'buffer' => false
							), array(
								'description' => 'ユーザを退会させてよろしいですか？',
								'type' => 'confirm'
							), array(
								'complete' => "function(data,textStatus,xhr){window.location.reload();}"
							));
						} else {
							echo "<p class='txtRedNotice'>管理者は退会させられません</p>";
						}
					} elseif ($val['Administrator']['STATUS'] == User::STATUS_WITHDRAWN) {
						echo "退会済み";
					} elseif ($val['Administrator']['STATUS'] == User::STATUS_WAITING_ACCEPTANCE) {
						echo "承認待ち";
					} elseif ($val['Administrator']['STATUS'] == User::STATUS_WAITING_ACTIVATION) {
						echo "有効化待ち";
					}
					echo "</td>";
					
					/* データ削除 */
					echo "<td width='120'>";
					if ($val['Administrator']['STATUS'] == User::STATUS_ENABLED) {
						echo "<p class='txtRedNotice'>有効なユーザはデータ削除できません</p>";
					} else {
						echo $html->link('削除', array(
							'controller' => 'administrators',
							'action' => 'delete/' . $val['Administrator']['USR_ID']
						));
					}
					echo "</td>";
					
					/* 権限 */
					echo "<td width='60'>";
					if ($val['Administrator']['STATUS'] == User::STATUS_ENABLED && $val['Administrator']['DEL_FLG'] == 0) {
						if ($val['Administrator']['AUTHORITY'] == 0 && $val['Administrator']['USR_ID'] == $user['User']['USR_ID']) {
							echo __('管理者', true);
						} elseif ($val['Administrator']['AUTHORITY'] == 0) {
							echo $customJs->linkAfterConfirm(__('管理者', true), array(
								'controller' => 'Administrators',
								'action' => 'role/' . $val['Administrator']['USR_ID'] . '/0'
							), array(
								'escape' => false,
								'buffer' => false
							), array(
								'description' => __('ユーザー権限を一般に変更しますか？', true),
								'type' => 'confirm'
							), array(
								'complete' => "function(data,textStatus,xhr){window.location.reload();}"
							));
						} else {
							echo $customJs->linkAfterConfirm(__('一般', true), array(
								'controller' => 'Administrators',
								'action' => 'role/' . $val['Administrator']['USR_ID'] . '/1'
							), array(
								'escape' => false,
								'buffer' => false
							), array(
								'description' => __('ユーザー権限を管理者に変更しますか？', true),
								'type' => 'confirm'
							), array(
								'complete' => "function(data,textStatus,xhr){window.location.reload();}"
							));
						}
					} else {
						if ($val['Administrator']['AUTHORITY'] == 0 && $val['Administrator']['USR_ID'] == $user['User']['USR_ID']) {
							echo __('管理者', true);
						} elseif ($val['Administrator']['AUTHORITY'] == 0) {
							echo __('管理者', true);
						} else {
							echo __('', true);
						}
					}
					echo "</td>";
					
					echo "</tr>";
				}
			}
			?>
		</table>

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
		</div>
	</div>
</div>
<?php
echo $form->hidden('Security.token', array(
	'value' => session_id()
));
?>
<?php

echo $form->end();
?>
<!-- InstanceEndEditable -->
<!-- contents_End -->
