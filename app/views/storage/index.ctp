<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php
echo $html->css("file", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<h2 class="mb20">ファイル</h2>
<div id="searchArea">
	<?php
	if (isset($keyword)) {
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
			$paginator->options(array(
				'url' => 'all/NAME:' . urlencode($keyword)
			));
		} else {
			$paginator->options(array(
				'url' => 'NAME:' . urlencode($keyword)
			));
		}
	}
	echo $form->create('Storage', array(
		'type' => 'post',
		'action' => (isset($this->params['pass'][0])) ? 'index/' . $this->params['pass'][0] : 'index/',
		'name' => 'FileIndexForm'
	))?>
	<p>
		<?php
		echo $form->text('NAME', array(
			'class' => '_input'
		));
		?>
		<?php //echo $form->input('STATUS', array('type' => 'select','multiple' => 'checkbox','options' => $group_status_s,'label' => false, 'div' => false)); ?>
		<?php
		echo $customHtml->hiddenToken();
		?>
		<?php
		echo $form->submit('user/bt_search_user.jpg', array(
			'div' => false,
			'name' => 'submit',
			'alt' => '検索する',
			'class' => 'on'
		));
		?>
	</p>
	<?php
	echo $form->end();
	?>
</div>

<div id="contentsLeft">
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
	<ul id="tab">
		<?php
		if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
			echo '<li>' . $html->link("<span>自分のファイル</span>", array(
				'controller' => 'storages',
				'action' => 'index/'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li class="present">' . $html->link("<span>全てのファイル</span>", array(
				'controller' => 'storages',
				'action' => 'index/all'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
		} else {
			echo '<li class="present">' . $html->link("<span>自分のファイル</span>", array(
				'controller' => 'storages',
				'action' => 'index/'
			), array(
				'escape' => false,
				'div' => false
			)) . '</li>';
			echo '<li>' . $html->link("<span>全てのファイル</span>", array(
				'controller' => 'storages',
				'action' => 'index/all'
			), array(
				'escape' => false,
				'div' => false
			)) . "</li>";
		}
		?>
		<li><?php
		echo $customJs->link($html->image('file/bt_upload_2.gif', array(
			'alt' => 'ファイルをアップロードする'
		)), array(
			'controller' => 'storages',
			'action' => 'fileup/' . $groupid
		), array(
			'id' => 'file_up',
			'update' => null,
			'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
			'method' => 'POST',
			'buffer' => false,
			'escape' => false,
			'div' => false
		));
		?></li>
	</ul>
	<table cellpadding="0" cellspacing="0" class="fileTable wordBreak">
		<tr>
			<th class="thDownload">ダウンロード</th>
			<th class="thFile">ファイル名</th>
			<th class="thType">形式</th>
			<th class="thSize">サイズ</th>
			<?php
			if (isset($this->params['pass'][0])) {
				echo '<th class="thStatus">所有者</th>';
			} else {
				echo '<th class="thStatus">ステータス</th>';
			}
			?>
			<th class="thSize">日時</th>
			<th class="thDelete">削除</th>
		</tr>
		<?php
		foreach ($list as $key => $val) {
			?>
		<?php
			echo '<tr class="odd line_' . h($val['Storage']['FLE_ID']) . '">';
			?>
            <td><?php
			echo $html->link($html->image('file/bt_download.gif', array(
				'alt' => 'ダウンロード'
			)), array(
				'controller' => 'storages',
				'action' => 'download/' . $val['Storage']['RAND_NAME']
			), array(
				'escape' => false
			))?></td>
		<td><?php
			echo $html->link(h($val['Storage']['ORIGINAL_NAME']), array(
				'controller' => 'storages',
				'action' => 'download/' . $val['Storage']['RAND_NAME']
			), array(
				'escape' => false
			))?></td>
		<td><?php
			echo ($val['Storage']['EXTENSION'] != null) ? h($val['Storage']['EXTENSION']) : "&nbsp;";
			?></td>
		<td><?php
			echo $customHtml->file_size($val['Storage']['F_SIZE']);
			?></td>
			<?php
			if (isset($this->params['pass'][0])) {
				if ($val['User']['NAME'] != null) {
					echo "<td>" . h($val['User']['NAME']) . "</td>";
				} else {
					echo "<td>退会したユーザ</td>";
				}
			} else {
				if ($val['Storage']['PUBLIC'] != 0) {
					echo "<td><div class='public_" . h($val['Storage']['FLE_ID']) . "'>" . $customJs->linkAfterConfirm($files_status[$val['Storage']['PUBLIC']], array(
						'controller' => 'storages',
						'action' => 'c_public',
						$val['Storage']['FLE_ID']
					), array(
						'buffer' => false
					), array(
						'description' => 'ファイルを非公開にすると他人から見えませんがよろしいですか？',
						'type' => 'confirm'
					), array(
						'complete' => "function(data, textStatus, xhr) { $('.public_" . $val['Storage']['FLE_ID'] . "').html(data) }"
					)) . "</div></td>";
				} else {
					echo "<td><div class='public_" . h($val['Storage']['FLE_ID']) . "'>" . $customJs->linkAfterConfirm($files_status[$val['Storage']['PUBLIC']], array(
						'controller' => 'storages',
						'action' => 'c_public',
						$val['Storage']['FLE_ID']
					), array(
						'buffer' => false
					), array(
						'description' => 'ファイルを公開にすると他人から見えるようになりますが、よろしいですか？',
						'type' => 'confirm'
					), array(
						'complete' => "function(data, textStatus, xhr) { $('.public_" . $val['Storage']['FLE_ID'] . "').html(data) }"
					)) . "</div></td>";
				}
			}
			?>
			<td><?php
			echo $customHtml->date_split($val['Storage']['LAST_UPDATE']);
			?></td>
		<td><?php
			if ($user['User']['AUTHORITY'] == User::AUTHORITY_TRUE || $user['User']['USR_ID'] == $val['Storage']['USR_ID']) {
				echo $customJs->linkAfterConfirm($html->image('file/bt_delete.gif', array(
					'alt' => '削除'
				)), array(
					'controller' => 'storages',
					'action' => 'delete',
					$val['Storage']['FLE_ID']
				), array(
					'escape' => false,
					'buffer' => false
				), array(
					'description' => '削除してもよろしいですか？',
					'type' => 'confirm',
					'close' => false
				), array(
					'complete' => 'function(data, textStatus, xhr) { _delete(xhr); } '
				));
			} else {
				echo "&nbsp";
			}
			?></td>
		</tr>
		<?php }?>
	</table>
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
<?php
echo $this->element('right_menu');
?>
<!-- #EndLibraryItem -->
<!-- InstanceEndEditable -->
<!-- contents_End -->
