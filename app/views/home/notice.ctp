<!-- contents_Start -->
<?php
echo $html->css("timeline", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<div id="timelineName" class="clearfix">
	<p><?php
	echo $html->image(array(
		'controller' => 'storages',
		'action' => 'thumbnail/' . $user['User']['USR_ID']
	));
	?></p>
	<h2><?php
	echo h($user['User']['NAME']) . "さん宛の通知メッセージ";
	?></h2>
</div>
<div id="contentsLeft">
	<ul id="tab">
	</ul>
	<div id="tabBoxIndex">
		<div id="tabBoxIndexBtm">
			<div id="tabBoxIndexArea">
	<?php
	echo $form->create();
	?>
	<div>
	<?php
	// print_r($this->data);
	echo $form->checkbox('Action.selectAll', array(
		'class' => 'chk_all',
		'onclick' => 'select_all(checked);',
		'checked' => ''
	)) . '<span style="margin-right:40px;"> すべて選択</span>';
	// echo $form->input( 'Action.read', array( 'type' => 'select', 'options' => array('0' => '選択したものを既読にする', '1' => 'すべて既読にする'), 'style' => 'display:inline;'));
	echo $form->select('Action.read', array(
		'0' => '選択したものを既読にする',
		'1' => 'すべて既読にする'
	), null, array(
		'empty' => false
	));
	echo $form->submit('　実行　', array(
		'style' => 'display:inline; margin-left: 15px;',
		'div' => false
	));
	echo $customHtml->hiddenToken();
	?>
	</div>
				<table>
	<?php
	foreach ($list as $key => $val) {
		if ($key === 'Count') {
			continue;
		}
		echo "<tr class=notice_" . h($key) . " style='height:50px;'>";
		echo "<th>";
		if ($val['Notice']['STATUS'] == 0)
			echo $form->checkbox('Notice.select_' . $key, array(
				'class' => 'chk',
				'style' => 'margin-bottom:12px',
				'value' => $val['Notice']['NTC_ID']
			));
		echo $html->image(array(
			'controller' => 'storages',
			'action' => 'thumbnail/' . $val['P_User']['USR_ID']
		), array(
			'style' => 'width:40px;height:40px'
		)) . "</th>";
		echo "<td>";
		if ($val['Notice']['ACT_ID'] == 0) {
			if ($val['Notice']['STATUS'] != 0) {
				echo $html->link($val['P_User']['NAME'] . "さんがあなたの投稿にコメントしました。", array(
					'controller' => 'homes',
					'action' => 'one/' . $val['Notice']['TML_ID']
				), array(
					'class' => 'no_red_red'
				));
			} else {
				echo $html->link($val['P_User']['NAME'] . "さんがあなたの投稿にコメントしました。", array(
					'controller' => 'homes',
					'action' => 'one/' . $val['Notice']['TML_ID']
				), array(
					'class' => 'no_red'
				));
			}
		} elseif ($val['Notice']['ACT_ID'] == 2) {
			
			if ($val['Timeline']['NAME'] == '自分') {
				if ($val['Notice']['STATUS'] != 0) {
					echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "の自身の投稿にコメントしました。", array(
						'controller' => 'homes',
						'action' => 'one/' . $val['Notice']['TML_ID']
					), array(
						'class' => 'no_red_red'
					));
				} else {
					echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "の自身の投稿にコメントしました。", array(
						'controller' => 'homes',
						'action' => 'one/' . $val['Notice']['TML_ID']
					), array(
						'class' => 'no_red'
					));
				}
			} elseif ($val['Timeline']['NAME'] == 'あなた') {
				if ($val['Notice']['STATUS'] != 0) {
					echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "の" . $val['Timeline']['NAME'] . "の投稿にコメントしました。", array(
						'controller' => 'homes',
						'action' => 'one/' . $val['Notice']['TML_ID']
					), array(
						'class' => 'no_red_red'
					));
				} else {
					echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "の" . $val['Timeline']['NAME'] . "の投稿にコメントしました。", array(
						'controller' => 'homes',
						'action' => 'one/' . $val['Notice']['TML_ID']
					), array(
						'class' => 'no_red'
					));
				}
			} else {
				if ($val['Notice']['STATUS'] != 0) {
					echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "の" . $val['Timeline']['NAME'] . "さんの投稿にコメントしました。", array(
						'controller' => 'homes',
						'action' => 'one/' . $val['Notice']['TML_ID']
					), array(
						'class' => 'no_red_red'
					));
				} else {
					echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "の" . $val['Timeline']['NAME'] . "さんの投稿にコメントしました。", array(
						'controller' => 'homes',
						'action' => 'one/' . $val['Notice']['TML_ID']
					), array(
						'class' => 'no_red'
					));
				}
			}
		} else {
			if ($val['Notice']['STATUS'] != 0) {
				echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "に投稿しました。", array(
					'controller' => 'homes',
					'action' => 'one/' . $val['Notice']['TML_ID']
				), array(
					'class' => 'no_red_red'
				));
			} else {
				echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "に投稿しました。", array(
					'controller' => 'homes',
					'action' => 'one/' . $val['Notice']['TML_ID']
				), array(
					'class' => 'no_red'
				));
			}
		}
		echo "<br />" . "<p class='n_date'>" . h($val['Notice']['INSERT_DATE']) . "</p>";
		echo "</td>";
		echo "</tr>";
	}
	?>
	</table>
			</div>
		</div>
	</div>
	<p class="pageTop">
		<a href="#top">上に戻る</a>
	</p>
	<div id='pagination'>
		<?php
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
		?>
	</div>
</div>
<div id="contentsRight">
	<!-- #BeginLibraryItem "/Library/contentsRight.lbi" -->
</div>
<!-- #EndLibraryItem -->
<!-- InstanceEndEditable -->
<!-- contents_End -->