<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_group_invite {
	width: 460px;
	padding: 10px;
}

#popup_group_invite table {
	width: 460px;
}

#popup_group_invite table th {
	width: 30px;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_group_invite table th img {
	border: 1px solid #CCC;
}

#popup_group_invite table td {
	padding: 0 15px 10px 0;
}

#popup_group_invite ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_group_invite ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">グループ招待</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>

	<div id="popup_group_invite">
		<?php
		echo $form->create();
		?>
		<table>
			<tr>
				<th>名前</th>
				<td><?php
				echo $form->text('NAME1', array(
					'class' => 'name1',
					'style' => 'width: 300px',
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
				<td>
				<?php
				echo $customJs->link("参照", array(
					'controller' => 'groups',
					'action' => 'invite_user/' . $groupid . '/1'
				), array(
					'before' => "popupclass.popup_open();",
					'update' => null,
					'complete' => "group_inviteuser('" . Router::url(array(
						'controller' => 'groups',
						'action' => 'invite_user/' . $groupid . '/1'
					), true) . "')",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", '#', array(
					'update' => null,
					'complete' => "g_a_clear(1)",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>

			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME2', array(
					'class' => 'name2',
					'style' => 'width: 300px',
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
					'controller' => 'groups',
					'action' => 'invite_user/' . $groupid . '/2'
				), array(
					'before' => "popupclass.popup_open();",
					'update' => null,
					'complete' => "group_inviteuser('" . Router::url(array(
						'controller' => 'groups',
						'action' => 'invite_user/' . $groupid . '/2'
					), true) . "')",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", '#', array(
					'update' => null,
					'complete' => "g_a_clear(2)",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME3', array(
					'class' => 'name3',
					'style' => 'width: 300px',
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
					'controller' => 'groups',
					'action' => 'invite_user/' . $groupid . '/3'
				), array(
					'before' => "popupclass.popup_open();",
					'update' => null,
					'complete' => "group_inviteuser('" . Router::url(array(
						'controller' => 'groups',
						'action' => 'invite_user/' . $groupid . '/3'
					), true) . "')",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", '#', array(
					'update' => null,
					'complete' => "g_a_clear(3)",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME4', array(
					'class' => 'name4',
					'style' => 'width: 300px',
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
					'controller' => 'groups',
					'action' => 'invite_user/' . $groupid . '/4'
				), array(
					'before' => "popupclass.popup_open();",
					'update' => null,
					'complete' => "group_inviteuser('" . Router::url(array(
						'controller' => 'groups',
						'action' => 'invite_user/' . $groupid . '/4'
					), true) . "')",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", '#', array(
					'update' => null,
					'complete' => "g_a_clear(4)",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><?php
				echo $form->text('NAME5', array(
					'class' => 'name5',
					'style' => 'width: 300px',
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
					'controller' => 'groups',
					'action' => 'invite_user/' . $groupid . '/5'
				), array(
					'before' => "popupclass.popup_open();",
					'update' => null,
					'complete' => "group_inviteuser('" . Router::url(array(
						'controller' => 'groups',
						'action' => 'invite_user/' . $groupid . '/5'
					), true) . "')",
					'method' => 'POST',
					'buffer' => false
				));
				?>
				</td>
				<td>
				<?php
				echo $customJs->link("消去", '#', array(
					'update' => null,
					'complete' => "g_a_clear(5)",
					'method' => 'POST',
					'buffer' => false
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
				'controller' => 'groups',
				'action' => 'invite/' . $groupid
			),
			'update' => 'null',
			'complete' => "group_invite_send(XMLHttpRequest)"
		));
		echo $js->writeBuffer();
		echo $form->end();
		?>
		</span>
	</div>
</div>

