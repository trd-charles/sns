<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_user_edit {
	width: 460px;
	padding: 10px;
}

#popup_user_edit table {
	width: 460px;
}

#popup_user_edit table th {
	width: 90px;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_user_edit table th img {
	border: 1px solid #CCC;
}

#popup_user_edit table td {
	padding: 0 15px 10px 0;
}

#popup_user_edit ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_user_edit ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">ユーザ管理</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div id="popup_user_edit">
<?php
echo $form->create();
?>
<div class='indexlists'>
			<table>
				<tr>
					<th>メールアドレス<span class='required'>*</span></th>
					<td><?php
					echo $form->text('MAIL', array(
						'style' => 'width: 90%',
						'class' => $form->error('MAIL') ? 'f_errors' : ''
					));
					?>
		<?php
		echo $form->error('MAIL', array(
			'class' => 'errors'
		));
		?>
		</td>
				</tr>
				<tr>
					<th>パスワード変更</th>
					<td><?php
					echo $form->radio('PASS_C', array(
						0 => '変更しない',
						1 => '変更する'
					), array(
						'class' => 'pass_edit_p',
						'legend' => false,
						'label' => false,
						'value' => $pass,
						'div' => false,
						'style' => 'width:30px'
					));
					?></td>
				</tr>
				<script>
	if($('.pass_edit_p:checked').val()==1){
		$('.pass_p').show();
	}else{
		$('.pass_p').hide();
		$('.pass_val_p').val(null);
	}
	$('.pass_edit_p:radio').change(function(){
		if($('.pass_edit_p:checked').val()==1){
			$('.pass_p').show();
		}else{
			$('.pass_p').hide();
			$('.pass_val_p').val(null);
		}
	});
	</script>
				<tr class='pass_p' style='display: none'>
					<th>パスワード<span class='required'>*</span></th>
					<td class="inputArea"><?php
					echo $form->text('EDIT_PASSWORD', array(
						'class' => $form->error('EDIT_PASSWORD') ? 'f_errors pass_val_p' : 'pass_val_p',
						'style' => 'width: 90%'
					));
					?><?php

					echo $form->error('EDIT_PASSWORD', array(
						'class' => 'errors'
					));
					?></td>
				</tr>
	<?php
	if ($user['User']['AUTHORITY'] != User::AUTHORITY_TRUE) {
		if ($withdrawal != 1) {
			echo "<td>" . $customJs->link('退会する', array(
				'controller' => 'users',
				'action' => 'withdrawal/' . h($this->data['User']['USR_ID'])
			), array(
				'escape' => false,
				'update' => null,
				'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
				'method' => 'POST',
				'buffer' => false
			)) . "</td>";
		}
	}
	?>
</table>
<?php
echo $form->hidden('STATUS', array(
	'value' => 1
));
?>
<?php

echo $customHtml->hiddenToken();
?>
		<span style="text-align: center; margin-left: 190px;">
			<?php
			echo $js->submit('profile/bt_save.gif', array(
				'url' => array(
					'controller' => 'users',
					'action' => 'edit/' . $this->data['User']['USR_ID']
				),
				'update' => 'null',
				'complete' => "profile(XMLHttpRequest)",
				'div' => false
			));
			echo $js->writeBuffer();
			?>
			<?php
			echo $form->end();
			?>
		</span>
		</div>
	</div>