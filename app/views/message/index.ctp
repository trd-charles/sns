<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php
echo $html->css("message", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<h2 class="mb20">メッセージ</h2>
<div id="contentsLeft">
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
	<ul id="tab">
		<?php
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 's') {
			echo '<li>' . $html->link("<span>受信箱</span>", array(
				'controller' => 'messages',
				'action' => 'index/r'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li class="present">' . $html->link("<span>送信箱</span>", array(
				'controller' => 'messages',
				'action' => 'index/s'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
		} else {
			echo '<li class="present">' . $html->link("<span>受信箱</span>", array(
				'controller' => 'messages',
				'action' => 'index/r'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li>' . $html->link("<span>送信箱</span>", array(
				'controller' => 'messages',
				'action' => 'index/s'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
		}
		?>
		<li><?php
		echo $customJs->link($html->image('message/bt_message.gif', array(
			'alt' => 'メッセージを作成する'
		)), array(
			'controller' => 'messages',
			'action' => 'create/new'
		), 
				array(
					'id' => 'create_msg',
					'before' => "popupclass.popup_open()",
					'update' => null,
					'complete' => "popupclass.popup_view(XMLHttpRequest);message_clear();",
					'method' => 'POST',
					'buffer' => false,
					'escape' => false,
					'div' => false
				));
		?></li>
	</ul>
	<table cellpadding="0" cellspacing="0" class="messageTable wordBreak">
		<tr>
			<?php
			echo ($status == 'r') ? "<th class='thUser'>送信者</th>" : "<th class='thUser'>送信先</th>";
			?>
			<?php
			echo ($status == 'r') ? "<th class='thRead'>ステータス</th>" : "";
			?>
			<th class="thSubject">件名</th>
			<?php
			if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 's') {
				echo '<th class="thDate">送信日時</th>';
			} else {
				echo '<th class="thDate">受信日時</th>';
			}
			?>
			<th class="thDelete">削除</th>
		</tr>
		<tr>
		<?php
		foreach ($list as $message) {
			?>
		<?php
			echo '<tr class="odd line_' . $message['Message']['MSG_G_ID'] . '">';
			?>
			<?php
			if ($status == 'r') {
				echo "<td>" . $html->image('message/user_icon.png');
				
				if ($message['S_User']['NAME'] != null) {
					echo h($message['S_User']['NAME']) . "</td>";
				} elseif ($message['S_User']['NAME'] == null) {
					echo h($message['Message']['S_NAME']) . "</td>";
				}
				
				echo "<td>";
				if ($message['Message']['RED'] == 0) {
					echo "未読";
				} else {
					echo "既読";
				}
				echo "</td>";
			} else {
				echo "<td>";
				
				foreach ($message['Users'] as $msgUser) {
					echo $html->image('message/user_icon.png');
					
					if ($msgUser['R_User']['NAME'] != null) {
						echo h($msgUser['R_User']['NAME']);
					} elseif ($msgUser['R_User']['NAME'] == null) {
						echo h($msgUser['Message']['R_NAME']);
					}
					?><br>
				<?php
				}
				echo "</td>";
			}
			?>

			<td><?php
			echo $html->link($message['Message']['SUBJECT'], array(
				'action' => 'check/' . $message['Message']['MSG_G_ID'] . "/" . $status
			));
			?></td>
			<td><?php
			echo $customHtml->date_split($message['Message']['INSERT_DATE']);
			?></td>
			<td><?php
			
			if ($status == 's') {
				echo $customJs->linkAfterConfirm($html->image('file/bt_delete.gif', array(
					'alt' => '削除'
				)), array(
					'controller' => 'messages',
					'action' => 'delete_snd',
					$message['Message']['MSG_G_ID']
				), array(
					'escape' => false,
					'buffer' => false
				), array(
					'description' => '削除してよろしいですか？',
					'type' => 'confirm',
					'close' => false
				), array(
					'complete' => "function(data,textStatus,xhr) { _delete(xhr); }"
				));
			} else {
				echo $customJs->linkAfterConfirm($html->image('file/bt_delete.gif', array(
					'alt' => '削除'
				)), array(
					'controller' => 'messages',
					'action' => 'delete_rcv',
					$message['Message']['MSG_G_ID']
				), array(
					'escape' => false,
					'buffer' => false
				), array(
					'description' => '削除してよろしいですか？',
					'type' => 'confirm',
					'close' => false
				), array(
					'complete' => "function(data,textStatus,xhr) { _delete(xhr); }"
				));
			}
			?></td>
		</tr>
		<?php }?>
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
<?php
echo $this->element('right_menu');
?>
<!-- #EndLibraryItem -->
<!-- InstanceEndEditable -->
<!-- contents_End -->