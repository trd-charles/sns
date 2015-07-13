<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_judge {
	width: 460px;
	padding: 10px;
}

#popup_judge table {
	border-spacing: 0px;
	width: 460px;
}

#popup_judge table th {
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_judge table th img {
	border: 1px solid #CCC;
}

#popup_judge table td {
	padding: 0 15px 10px 0;
}

#popup_judge ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_judge ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">メッセージ確認</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>

	<div id="popup_judge">
		<table class='wordBreak'>
			<tr>
				<?php
				echo "<th width=100px>申請者</th>";
				?>
				<td><?php
				echo h($list['User']['NAME']);
				?></td>
			</tr>
			<tr>
				<?php
				echo "<th width=100px>メールアドレス</th>";
				?>
				<td><?php
				echo h($list['User']['MAIL']);
				?></td>
			</tr>
			<tr>
				<th>メッセージ</th>
				<td><?php
				echo h($list['Request']['MESSAGE']);
				?></td>
			</tr>
		</table>
		<span class='submit_btn'
			style="text-align: center; margin-left: 160px;">
		<?php
		echo $form->create();
		echo $form->hidden("REQ_ID", array(
			'value' => $list['Request']['REQ_ID']
		));
		echo $form->hidden("no", array(
			'value' => $no
		));
		echo $customHtml->hiddenToken();
		if ($list['Request']['TYPE'] == 2 || $list['Request']['TYPE'] == 0) {
			echo $js->submit('common/bt_consent.gif', array(
				'style' => 'margin-top:10px',
				'url' => array(
					'action' => 'judge/p'
				),
				'buffer' => 'true',
				'div' => false,
				'name' => 'submit',
				'alt' => 'permit',
				'update' => 'null',
				'complete' => "judge_finish(XMLHttpRequest);"
			));
			echo $js->submit('common/bt_reject.gif', array(
				'style' => 'margin-left:30px;margin-top:10px',
				'url' => array(
					'action' => 'judge/r'
				),
				'buffer' => 'true',
				'div' => false,
				'name' => 'submit',
				'alt' => 'rejection',
				'update' => 'null',
				'complete' => "judge_finish(XMLHttpRequest);"
			));
		} else {
			echo $js->submit('common/bt_check.gif', array(
				'url' => array(
					'action' => 'judge/c'
				),
				'buffer' => 'true',
				'div' => false,
				'name' => 'submit',
				'alt' => 'rejection',
				'update' => 'null',
				'complete' => 'judge_finish(XMLHttpRequest);location.href = "' . Router::url(array(
					'controller' => 'groups',
					'action' => 'main/' . $list['Request']['GRP_ID']
				), true) . '"'
			));
		}
		echo $js->writeBuffer();
		echo $form->end();
		?>
		</span>
	</div>
</div>