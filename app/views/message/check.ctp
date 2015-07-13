<?php
echo $html->css("message", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<h2 class="mb20">メッセージ</h2>
<div id="contentsCenter" class="mb60">
	<div id="contentsCenterTop">
		<div id="contentsCenterBtm">
			<div class="messageArea wordBreak">
				<dl>
					<dt>
				<?php
				echo ($status == 'r') ? "<td width=100px>送信者：</td>" : "<td width=100px>送信先：</td>";
				?>
			</dt>
					<dd><?php
					
					if ($status == 'r') {
						// 受信箱
						if ($detail['S_User']['NAME'] != null) {
							echo "<td>" . h($detail['S_User']['NAME']) . Configure::read('SEND_USER_SEPARATER') . "</td><td>";
						} elseif ($detail['S_User']['NAME'] == null) {
							echo "<td>" . h($detail['Message']['S_NAME']) . Configure::read('SEND_USER_SEPARATER') . "</td><td>";
						}
						
						echo "</td></dd><br />";
						echo "<dt><td width=100px>受信者： </td></dt>";
						echo "<dd>";
						echo h($detail['R_User']['NAME']) . Configure::read('SEND_USER_SEPARATER');
						foreach ($detail['Users'] as $msgUser) {
							if ($msgUser['R_User']['NAME'] != null) {
								echo h($msgUser['R_User']['NAME'] . Configure::read('SEND_USER_SEPARATER'));
							} elseif ($msgUser['R_User']['NAME'] == null) {
								echo h($msgUser['Message']['R_NAME'] . Configure::read('SEND_USER_SEPARATER'));
							}
						}
						;
					} else {
						// 送信箱
						foreach ($detail['Users'] as $msgUser) {
							if ($msgUser['R_User']['NAME'] != null) {
								echo h($msgUser['R_User']['NAME'] . Configure::read('SEND_USER_SEPARATER'));
							} elseif ($msgUser['R_User']['NAME'] == null) {
								echo h($msgUser['Message']['R_NAME'] . Configure::read('SEND_USER_SEPARATER'));
							}
						}
						;
					}
					
					?></td>
					</dd>
					<br />
					<dt>件名&emsp;：</dt>
					<dd><?php
					echo h($detail['Message']['SUBJECT']);
					?></dd>
					<br />
					<dt>
				<?php
				echo ($status == 'r') ? "<td width=100px>受信日時：</td>" : "<td width=100px>送信日時：</td>";
				?>
			</dt>
					<dd><?php
					echo ($status == 'r') ? "<td>" . h($detail['Message']['INSERT_DATE']) . "</td>" : "<td>" . h($detail['Message']['INSERT_DATE']) .
							 "</td>";
					?></dd>
					<br />
					</dd>
				</dl>
				<p class="messageTxt"><?php
				echo $customHtml->text_cut($detail['Message']['MESSAGE'], null, null, 120, false, false);
				?></p>
				<p class="sendBtn">

		<?php
		if ($status == 'r' && $detail['S_User']['NAME'] != NULL) {
			echo $form->create();
			echo $form->hidden('S_USR_NAME', array(
				'value' => $detail['S_User']['NAME']
			));
			echo $form->hidden('SUBJECT', array(
				'value' => $detail['Message']['SUBJECT']
			));
			echo $form->hidden('USR_ID', array(
				'value' => $detail['Message']['S_USR_ID']
			));
			echo $html->link($html->image('message/bt_reply.gif'), 'javascript:void(0)', 
					array(
						'escape' => false,
						'id' => 'submit_reply'
					));
			if (isset($detail['Users'][0]) == true) {
				foreach ($detail['Users'] as $key => $val) {
					echo $form->hidden('Users_NAME' . $key, array(
						'value' => $val['R_User']['NAME']
					));
					echo $form->hidden('Users_ID' . $key, array(
						'value' => $val['R_User']['USR_ID']
					));
				}
			}
			echo $form->end();
		}
		?></p>


		<?php
		// 送信者
		// $detail['S_User']['NAME']
		
		// 同時に送信された人
		// foreach($detail['Users'] as $msgUser){
		// echo h(
		// $msgUser['Message']['R_NAME'].Configure::read('SEND_USER_SEPARATER')
		// );
		// }
		?>

<script type="text/javascript">
			$("#submit_reply").click(function() {
				popupclass.popup_open();
				message_clear();
				$.post('<?php
				echo $html->webroot . "messages/create/new";
				?>', $("#MessageCheckForm").serialize(), function(XMLHttpRequest) {
					$("#popup").html(XMLHttpRequest);
				});
			})
		</script>
			</div>
		</div>
	</div>
</div>
