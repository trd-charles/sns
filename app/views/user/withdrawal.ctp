<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

.desc {
	color: red;
	width: 420px;
	margin-left: 50px;
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
	<div>
	<?php
	if ($index_list) {
		?>
		<p class='mb20 desc'>
			以下のグループの管理者になっているため退会できません。<br />
			退会する場合は、グループの管理者を誰かに譲渡するか、グループを削除したうえで、もう一度退会してください。
		</p>
		<table>
		<?php
		foreach ($index_list as $key => $val) {
			echo "<tr>";
			echo '<th style="width:80px;">グループ名</th>';
			echo '<td>' . $html->link($val['Group']['NAME'], array(
				'controller' => 'groups',
				'action' => 'main/' . $val['Group']['GRP_ID']
			)) . '</td>';
			echo "</tr>";
		}
		?>
		</table>
	<?php }else{?>
		<p class='mb20 desc'>退会すると、ログインすることはできなくなります。よろしければ退会を押して下さい。</p>
		<span style="text-align: center">
		<?php
		echo $form->create();
		echo $form->hidden("hide_with");
		echo $customHtml->hiddenToken();
		echo $customJs->submitAfterConfirm('退会する', array(
			'buffer' => false
		), array(
			'description' => '本当に退会しますか？',
			'type' => 'confirm'
		), array(
			'complete' => "function(data, textStatus, xhr) { window.location.href = '" . Router::url(array(
				'controller' => 'users',
				'action' => 'logout'
			), true) . "'} "
		));
		echo $js->writeBuffer();
		echo $form->end();
		?>
		</span>
	<?php }?>
	<p class='mb20'>
	
	</div>
</div>