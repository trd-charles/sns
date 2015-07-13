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
	width: 460px;
}

#popup_select_user table th {
	width: 40px;
	padding: 0 10px 10px 5px;
}

#popup_select_user table th img {
	border: 1px solid #CCC;
}

#popup_select_user table td {
	width: 170px;
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
			echo $paginator->numbers() . ' | ' . $paginator->next(__('次へ', true) . ' >>', array(), null, 
					array(
						'tag' => 'span',
						'class' => 'disabled'
					));
			echo $js->writeBuffer();
			?>
		</div>
		<table class='wordBreak'>
			<?php
			$i = 0;
			foreach ($list as $key => $val) {
				if ($i % 2 == 0)
					echo "<tr>";
				
				echo "<th>" . $html->image(array(
					'controller' => 'storages',
					'action' => 'thumbnail/' . $val['User']['USR_ID']
				), array(
					'style' => 'width:40px;height:40px;margin-top:5px;',
					'class' => 'thumbnail'
				)) . "</th>";
				echo "<td>" . $customJs->link($customHtml->text_cut($val["User"]["NAME"], null, null, 25, false, false), 
						array(
							'controller' => 'messages',
							'action' => 'create',
							'user_name' . $no => $val['User']["NAME"],
							'user_id' . $no => $val['User']['USR_ID'],
							'no' => $no
						), 
						array(
							'escape' => false,
							'update' => null,
							'complete' => "popupclass.popup_view(XMLHttpRequest)",
							'method' => 'POST',
							'buffer' => false
						)) . "</td>";
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
			echo $paginator->numbers() . ' | ' . $paginator->next(__('次へ', true) . ' >>', array(), null, 
					array(
						'tag' => 'span',
						'class' => 'disabled'
					));
			echo $js->writeBuffer();
			?>
			<div style='float: right'>
				<?php
				echo $customJs->link('戻る', array(
					'controller' => 'messages',
					'action' => 'create'
				), 
						array(
							'escape' => false,
							'update' => null,
							'complete' => "user_add(XMLHttpRequest,null,null,null);",
							'method' => 'POST',
							'buffer' => false
						));
				?>
			</div>
		</div>
		<div style='clear: both'></div>
	</div>
</div>