<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_user_invite {
	width: 460px;
	padding: 10px;
}

#popup_user_invite table {
	width: 460px;
}

#popup_user_invite table th {
	width: 90px;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_user_invite table th img {
	border: 1px solid #CCC;
}

#popup_user_invite table td {
	padding: 0 15px 10px 0;
}

#popup_user_invite ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_user_invite ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">ユーザ招待</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div id="popup_user_invite">
<?php
echo $form->create('User', array(
	'type' => 'post',
	'controller' => 'users',
	'action' => 'invite',
	'name' => 'AdministratorEditForm'
))?>
<div class='indexlist_user'>
			<table>
				<tr>
					<th style='width: 100px'>メールアドレス1</th>
					<td><?php
					echo $form->text('MAIL1', array(
						'style' => 'width: 90%'
					));
					?>
		<?php
		echo $form->error('MAIL1', null, array(
			'class' => 'errors'
		));
		?>
		</td>
				</tr>
				<tr>
					<th>メールアドレス2</th>
					<td><?php
					echo $form->text('MAIL2', array(
						'style' => 'width: 90%'
					));
					?>
		<?php
		echo $form->error('MAIL2', null, array(
			'class' => 'errors'
		));
		?>
		</td>
				</tr>
				<tr>
					<th>メールアドレス3</th>
					<td><?php
					echo $form->text('MAIL3', array(
						'style' => 'width: 90%'
					));
					?>
		<?php
		echo $form->error('MAIL3', null, array(
			'class' => 'errors'
		));
		?>
		</td>
				</tr>
				<tr>
					<th>メールアドレス4</th>
					<td><?php
					echo $form->text('MAIL4', array(
						'style' => 'width: 90%'
					));
					?>
		<?php
		echo $form->error('MAIL4', null, array(
			'class' => 'errors'
		));
		?>
		</td>
				</tr>
				<tr>
					<th>メールアドレス5</th>
					<td><?php
					echo $form->text('MAIL5', array(
						'style' => 'width: 90%'
					));
					?>
		<?php
		echo $form->error('MAIL5', null, array(
			'class' => 'errors'
		));
		?>
		</td>
				</tr>
			</table>
			<span style="text-align: center">
	<?php
	echo $customHtml->hiddenToken();
	?>
	<?php
	echo $js->submit('message/bt_submit.gif', array(
		'url' => array(
			'controller' => 'users',
			'action' => 'invite'
		),
		'update' => 'null',
		'complete' => "invite(XMLHttpRequest)"
	));
	echo $js->writeBuffer();
	echo $form->end();
	?>
</span>
		</div>
	</div>