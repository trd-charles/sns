<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_upload {
	width: 460px;
	padding: 10px;
}

#popup_upload table {
	width: 460px;
}

#popup_upload table th {
	width: 90px;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_upload table th img {
	border: 1px solid #CCC;
}

#popup_upload table td {
	padding: 0 15px 10px 0;
}

#popup_upload ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_upload ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">ファイルをアップロード</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div id="popup_upload">
		<?php
		echo $form->create();
		?>
		<div class='indexlists'>
			<table>
				<tr>
					<th>ファイル</th>
					<td><?php
					echo $form->file('FILE', array(
						'class' => 'filess'
					));
					?><div id="result" class="errors">
							</span>
						</div>
				
				</tr>
			</table>
		<?php
		echo $customHtml->hiddenToken();
		?>
	</div>
		<span style="text-align: center">
	<?php
	echo $form->submit('file/bt_upload.gif', array(
		'style' => 'margin-top:10px',
		'escape' => false,
		'class' => "upload",
		"onclick" => "var test='" . Router::url(array(
			'controller' => 'plugins',
			'action' => 'add'
		), true) . "';uploads(test);return false;"
	));
	?>
	<?php
	echo $form->end();
	?>
	</span>
	</div>