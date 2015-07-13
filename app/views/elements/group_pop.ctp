<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_group {
	width: 460px;
	padding: 10px;
}

#popup_group table {
	width: 460px;
}

#popup_group table th {
	width: 40px;
	padding: 0 10px 10px 5px;
}

#popup_group table th img {
	border: 1px solid #CCC;
}

#popup_group table td {
	width: 170px;
	padding: 0 15px 10px 0;
}

#popup_group ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_group ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<!-- contents_Start -->
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle"><?php
		echo h($name) . "さんが参加しているグループ"?></div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div id="popup_group">
		<table cellpadding="0" cellspacing="0" class='wordBreak'>
		<?php
		$i = 0;
		foreach ($list as $key => $val) {
			if ($i % 2 == 0)
				echo "<tr>";
			echo "<th>" . $html->image(array(
				'controller' => 'storages',
				'action' => 'group_thumbnail/' . $val['Group']['GRP_ID']
			), array(
				'style' => 'width:40px;height:40px;',
				'class' => 'thumbnail'
			)) . "</th>";
			echo "<td>" . $html->link($val["Group"]["NAME"], array(
				'controller' => 'groups',
				'action' => 'main/' . $val['Group']['GRP_ID']
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
		echo $paginator->numbers() . ' | ' . $paginator->next(__('次へ', true) . ' >>', array(), null, array(
			'tag' => 'span',
			'class' => 'disabled'
		));
		echo $js->writeBuffer();
		?>
	</div>
	</div>
</div>
<!-- contents_End -->