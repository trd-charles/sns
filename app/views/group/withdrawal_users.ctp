<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#join_g_u {
	width: 460px;
	padding: 10px;
}

#join_g_u table {
	width: 460px;
}

#join_g_u table th {
	width: 40px;
	padding: 0 10px 10px 5px;
}

#join_g_u table th img {
	border: 1px solid #CCC;
}

#join_g_u table td {
	width: 170px;
	padding: 0 15px 10px 0;
}

#join_g_u ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#join_g_u ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<!-- contents_Start -->
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">メンバー管理</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div class='user' id='join_g_u'>
		<p>退会させるユーザを選択してください。</p>
		<table cellpadding="0" cellspacing="0">
			<?php
			$i = 0;
			foreach ($list as $key => $val) {
				if ($i % 2 == 0)
					echo "<tr>";
				
				echo "<th>" . $html->image(array(
					'controller' => 'storages',
					'action' => 'thumbnail/' . $val["Administrator"]['USR_ID']
				), array(
					'style' => 'width:40px;height:40px;',
					'class' => 'thumbnail'
				)) . "</th>";
				echo "<td>";
				
				echo $customJs->linkAfterConfirm(h($val["Administrator"]["NAME"]), array(
					'controller' => 'groups',
					'action' => 'forcedWithdrawal',
					'group' => $grpid,
					'user' => $val["Administrator"]['USR_ID']
				), array(
					'escape' => false,
					'buffer' => false,
					'style' => ''
				), array(
					'description' => $val["Administrator"]["NAME"] . 'さんをこのグループから退会させてよろしいですか？',
					'type' => 'confirm',
					'close' => false
				), array(
					'complete' => "function(data,textStatus,xhr) {popupclass.confirm_open(data, 'alert');}"
				));
				
				echo "</td>";
				
				if ($i % 2 == 1)
					echo "</tr>";
				$i ++;
			}
			?>
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
					if (isset($own)) {
						echo $customJs->link('戻る', array(
							'controller' => 'groups',
							'action' => 'edit/' . $grpid
						), array(
							'escape' => false,
							'update' => null,
							'complete' => "own_insert(XMLHttpRequest,null);",
							'method' => 'POST',
							'buffer' => false
						));
					}
					?>
				</div>
		</div>
		<div style='clear: both'></div>
	</div>
</div>
<!-- contents_End -->
