<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#follows {
	width: 460px;
	padding: 10px;
}

#follows table {
	width: 460px;
}

#follows table th {
	width: 40px;
	padding: 0 10px 10px 5px;
}

#follows table th img {
	border: 1px solid #CCC;
}

#follows table td {
	width: 170px;
	padding: 0 15px 10px 0;
}

#follows ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#follows ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<!-- contents_Start -->
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle"><?php
		if (isset($r)) {
			echo h($name) . "さんをフォローしているユーザ(" . $count . ")";
		} else {
			echo h($name) . "さんがフォローしているユーザ(" . $count . ")";
		}
		?></div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div class='user' id='follows'>
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
				echo "<td>" . $html->link($val["Administrator"]["NAME"], array(
					'controller' => 'profiles',
					'action' => 'index/' . $val["Administrator"]['USR_ID']
				), array(
					'div' => false
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