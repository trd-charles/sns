<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_select_user {
	width: 460px;
	padding: 10px;
}

#popup_select_user table {
	border-spacing: 0px;
	width: 460px;
}

#popup_select_user table th {
	border-bottom: 1px solid #CCC;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_select_user table th img {
	border: 1px solid #CCC;
}

#popup_select_user table td {
	border-left: 1px solid #CCC;
	border-right: 1px solid #CCC;
	border-bottom: 1px solid #CCC;
	padding: 0 15px 10px 0;
}

#popup_select_user ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_select_user ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">ユーザ選択</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>

	<div id="popup_select_user">
		<div id='pagination'>
		<?php
		echo $paginator->options(array(
			'update' => '#popup'
		));
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
			echo $js->writeBuffer();
			?>
		</div>
		<table class='wordBreak'>
			<tr>
				<th>&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<?php
			foreach ($list as $key => $val) {
				echo "<tr>";
				echo "<td style='text-align:center'>" . $html->image(array(
					'controller' => 'storages',
					'action' => 'thumbnail/' . $val['User']['USR_ID']
				), array(
					'style' => 'width:40px;height:40px;',
					'class' => 'thumbnail'
				)) . "</td>";
				echo "<td>" . $customJs->link($val["User"]["NAME"], array(
					'controller' => 'groups',
					'action' => 'invite/' . $groupid
				), array(
					'update' => null,
					'before' => "popupclass.popup_open();",
					'complete' => "var id = '" . $val["User"]["USR_ID"] . "';var name = '" . $val["User"]["NAME"] . "';var no='" . $no . "';group_invite(XMLHttpRequest,name,no,id)",
					'method' => 'POST',
					'buffer' => false
				)) . "</td>";
				echo "</tr>";
			}
			?>
			<tr>
			
			
			<tr></tr>
			<td style='border: none'>&nbsp;</td>
			</tr>
		</table>
		<div id='pagination'>
		<?php
		echo $paginator->options(array(
			'update' => '#popup'
		));
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
			echo $js->writeBuffer();
			?>
			<div style='float: right'>
				<?php
				echo $customJs->link('戻る', array(
					'controller' => 'groups',
					'action' => 'invite/' . $groupid
				), array(
					'escape' => false,
					'update' => null,
					'complete' => "group_invite(XMLHttpRequest,null,null,null);",
					'method' => 'POST',
					'buffer' => false
				));
				?>
			</div>
		</div>
	</div>
</div>
