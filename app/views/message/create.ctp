<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_message {
	width: 460px;
	padding: 10px;
}

#popup_message table {
	width: 460px;
}

#popup_message table th {
	width: 50px;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_message table th img {
	border: 1px solid #CCC;
}

#popup_message table td {
	padding: 0 15px 0px 0;
}

#popup_message ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_message ul li {
	display: inline;
	padding-right: 5px;
}

#popup_message table td .MsgSubject {
	width: 365px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">メッセージ作成</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div id="popup_message">
		<?php
		echo $form->create('Message');
		?>
		<table class='wordBreak'>
			<tr>
				<th>名前</th>
				<td style="width: 200px;"><?php
				echo $form->text('NAME1', array(
					'class' => 'name1',
					'style' => 'width:260px;',
					'disabled' => "disabled"
				));
				?>
				<?php
				echo $form->hidden('ID_1', array(
					'class' => 'id1'
				));
				?>
				<?php
				echo $form->error('NAME1');
				?>
				</td>
				<td style="width: 30px;">
				<?php
				echo $customJs->link("参照", array(
					'controller' => 'messages',
					'action' => 'user/1'
				), 
						array(
							'before' => "popupclass.popup_open();message_save();",
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false
						));
				?>
				</td>
				<td style="width: 30px;">
				<?php
				echo $customJs->link("消去", array(
					'controller' => 'messages',
					'action' => 'create/del/1'
				), 
						array(
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false,
							'div' => false
						));
				?></td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME2', array(
					'class' => 'name2',
					'style' => 'width: 260px',
					'disabled' => "disabled"
				));
				?>
				<?php
				echo $form->hidden('ID_2', array(
					'class' => 'id2'
				));
				?>
				<?php
				echo $form->error('NAME2');
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("参照", array(
					'controller' => 'messages',
					'action' => 'user/2'
				), 
						array(
							'before' => "popupclass.popup_open();message_save();",
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false
						));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", array(
					'controller' => 'messages',
					'action' => 'create/del/2'
				), 
						array(
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false,
							'div' => false
						));
				?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME3', array(
					'class' => 'name3',
					'style' => 'width: 260px',
					'disabled' => "disabled"
				));
				?>
				<?php
				echo $form->hidden('ID_3', array(
					'class' => 'id3'
				));
				?>
				<?php
				echo $form->error('NAME3');
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("参照", array(
					'controller' => 'messages',
					'action' => 'user/3'
				), 
						array(
							'before' => "popupclass.popup_open();message_save();",
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false
						));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", array(
					'controller' => 'messages',
					'action' => 'create/del/3'
				), 
						array(
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false,
							'div' => false
						));
				?></td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME4', array(
					'class' => 'name4',
					'style' => 'width: 260px',
					'disabled' => "disabled"
				));
				?>
				<?php
				echo $form->hidden('ID_4', array(
					'class' => 'id5'
				));
				?>
				<?php
				echo $form->error('NAME4');
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("参照", array(
					'controller' => 'messages',
					'action' => 'user/4'
				), 
						array(
							'before' => "popupclass.popup_open();message_save();",
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false
						));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", array(
					'controller' => 'messages',
					'action' => 'create/del/4'
				), 
						array(
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false,
							'div' => false
						));
				?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME5', array(
					'class' => 'name5',
					'style' => 'width: 260px',
					'disabled' => "disabled"
				));
				?>
				<?php
				echo $form->hidden('ID_5', array(
					'class' => 'id5'
				));
				?>
				<?php
				echo $form->error('NAME5');
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("参照", array(
					'controller' => 'messages',
					'action' => 'user/5'
				), 
						array(
							'before' => "popupclass.popup_open();message_save();",
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false
						));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", array(
					'controller' => 'messages',
					'action' => 'create/del/5'
				), 
						array(
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false,
							'div' => false
						));
				?>
				</td>
			</tr>
			<?php
			if ($form->error('USER')) {
				echo '<tr>
						<th>&nbsp;</th>';
				echo "<td row='3'>" . $form->error('USER', array(
					'class' => 'errors'
				)) . "</td>
					</tr>";
			}
			?>
			<tr>
				<th>件名<span class='required'>*</span></th>
				<td colspan="3">
					<?php
					if ($form->error('SUBJECT') != NULL) {
						echo $form->text('SUBJECT', array(
							'style' => 'width:320px',
							'class' => 'subject f_errors'
						));
					} else {
						echo $form->text('SUBJECT', array(
							'style' => 'width:320px',
							'class' => 'subject'
						));
					}
					?>
					<?php
					echo $form->error('SUBJECT', array(
						'class' => 'errors'
					));
					?>
				</td>
			</tr>
			<tr>
				<th>送信内容</th>
				<td colspan="3">
					<?php
					if ($form->error('MESSAGE')) {
						echo $form->textarea('MESSAGE', 
								array(
									'rows' => '10',
									'style' => 'width:320px',
									'class' => 'message f_errors'
								));
					} else {
						echo $form->textarea('MESSAGE', 
								array(
									'rows' => '10',
									'style' => 'width:320px',
									'class' => 'message'
								));
					}
					?>
					<?php
					echo $form->error('MESSAGE', array(
						'class' => 'errors'
					));
					?>
				</td>
			</tr>
		</table>
		<span style="text-align: center; margin-left: 200px;">
			<?php
			echo $customJs->submitAfterConfirm('message/bt_submit.gif', array(
				'name' => 'send',
				'buffer' => false
			), array(
				'description' => '送信しますか？',
				'type' => 'confirm',
				'close' => false
			), 
					array(
						'url' => array(
							'controller' => 'messages',
							'action' => 'create',
							'plugin' => null
						),
						'complete' => "function(data,textStatus,xhr) {message_send(xhr)}"
					));
			echo $js->writeBuffer();
			?>
			<?php
			echo $customHtml->hiddenToken();
			?>
			<?php
			echo $form->end();
			?>
		</span>
	</div>
</div>
