<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_group_edit {
	width: 460px;
	padding: 10px;
}

#popup_group_edit table {
	width: 460px;
}

#popup_group_edit table th {
	width: 90px;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_group_edit table th img {
	border: 1px solid #CCC;
}

#popup_group_edit table td {
	padding: 0 15px 10px 0;
}

#popup_group_edit ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_group_edit ul li {
	display: inline;
	padding-right: 5px;
}

.pop_center {
	padding-left: 190px;
}
-->
</style>
<?php
echo $html->script("jquery.cookie") . "\n";
?>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">グループ設定</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div id="popup_group_edit">
	<?php
	echo $form->create('Group', array(
		'type' => 'post',
		'action' => 'edit',
		'name' => 'GroupCreateForm'
	))?>
		<div class='indexlists'>
			<table>
				<tr>
					<th>グループ名<span class='required'>*</span></th>
					<td colspan='2'>
					<?php
					echo $form->text('NAME', array(
						'style' => 'width:230px',
						'class' => $form->error('NAME') ? 'f_errors' : ''
					));
					?>
					<?php
					echo $form->error('NAME', array(
						'class' => 'errors'
					));
					?>
					</td>
				</tr>
				<tr>
					<th>管理者<span class='required'>*</span></th>
					<td>
					<?php
					echo $form->text('ORNER', array(
						'style' => 'width:230px',
						'disabled' => "disabled",
						'value' => $names,
						'class' => $form->error('ORNER') ? 'f_errors' : ''
					));
					?>
					<?php
					echo $form->error('ORNER', array(
						'class' => 'errors'
					));
					?>
					<?php
					echo $form->hidden('USR_ID', array(
						'value' => $ids
					));
					?>
					</td>
					<td>
					<?php
					echo $customJs->linkAfterConfirm('
							参照', array(
						'controller' => 'groups',
						'action' => 'join_user',
						h($this->data['Group']['GRP_ID']),
						"own"
					), array(
						'escape' => false,
						'buffer' => false
					), array(
						'description' => 'グループの管理者を変更しますか？',
						'type' => 'confirm',
						'close' => false
					), array(
						'complete' => "function(data,textStatus,xhr) {popupclass.popup_view(xhr);$('#popup').show();popupclass.popup_open();}",
						'before' => "function() {save_g_status();}"
					));
					?>
						</td>
				</tr>
				<tr>
					<th>公開設定</th>
					<td colspan='2'><?php
					echo $form->radio('TYPE', $group_type, array(
						'label' => false,
						'legend' => false,
						'style' => 'width:30px'
					));
					?>
					<?php
					echo $form->error('TYPE');
					?>
					</td>
				</tr>
				<tr>
					<th>グループ概要</th>
					<td colspan='2'><?php
					echo $form->textarea('DESCRIPTION', array(
						'style' => 'width:300px;height:100px;',
						'class' => $form->error('DESCRIPTION') ? 'f_errors' : ''
					));
					?>
					<?php
					echo $form->error('DESCRIPTION', array(
						'class' => 'errors'
					));
					?>
					</td>
				</tr>
			</table>
			<?php
			echo $form->hidden('GRP_ID');
			?>
			<?php
			echo $customHtml->hiddenToken();
			?>
						<span class='pop_center'>
				<?php
				echo $js->submit('profile/bt_save.gif', array(
					'url' => array(
						'controller' => 'groups',
						'action' => 'edit'
					),
					'div' => false,
					'name' => 'submit',
					'alt' => '作成する',
					'update' => 'null',
					'complete' => "group_edit(XMLHttpRequest)"
				));
				echo $js->writeBuffer();
				echo $form->end();
				?>
			</span>
		</div>

	<?php
	echo $customJs->link('メンバー管理', array(
		'controller' => 'groups',
		'action' => 'forcedWithdrawal',
		'group' => h($this->data['Group']['GRP_ID'])
	), array(
		'escape' => false,
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false,
		'style' => 'margin-left:300px'
	));
	
	echo $customJs->linkAfterConfirm('グループ削除', array(
		'controller' => 'groups',
		'action' => 'delete',
		h($this->data['Group']['GRP_ID'])
	), array(
		'escape' => false,
		'buffer' => false,
		'style' => 'margin-left:10px'
	), array(
		'description' => 'グループを削除しますか？',
		'type' => 'confirm'
	), array(
		'complete' => "function(data,textStatus,xhr) {window.location.href = '" . Router::url(array(
			'controller' => 'groups',
			'action' => 'index'
		), true) . "';}"
	));
	?>
	</div>
</div>
