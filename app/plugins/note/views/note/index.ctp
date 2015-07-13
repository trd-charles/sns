<?php echo $session->flash();?>
<?php echo $html->css('/note/css/note','stylesheet', array('media' => 'screen'))."\n"; ?>
<?php echo $html->css("file","stylesheet",array('media'=>'screen'))."\n"; ?>
<h2 class="mb20">ノート</h2>
<?php echo $this->element('search_form'); ?>
<div id="contentsLeft">
<?php echo $this->element('paginate'); ?>
<?php echo $this->element('tab'); ?>
	<table cellpadding="0" cellspacing="0" class="noteTable wordBreak">
		<tr>
			<th class="thTitle">タイトル</th>
			<th class="thUser">作成者</th>
			<th class="thDate">更新日時</th>
			<th class="thStatus">ステータス</th>
			<th class="thPublic">公開範囲</th>
			<?php if($isAdmin) echo '<th class="thPublic">削除</th>';?>
		</tr>
		<?php foreach($list as $key => $val){?>
		<tr class="odd line_<?php echo h($key);?>">
			<td><?php echo $html->link($val['Note']['TITLE'], array('plugin' => 'note', 'controller' => 'note', 'action' => 'content', 'noteID' => $val['Note']['NOTE_ID'])); ?></td>
			<td><?php echo h($val['User']['NAME']); ?></td>
			<td><?php echo h($val['Note']['UPDATE_DATE']); ?></td>
			<td><?php echo h($status[$val['Note']['STATUS']]); ?></td>
			<td><?php echo h($note_public[$val['Note']['PUBLIC']]); ?></td>
			<?php if($isAdmin) 
			echo "<td>".$customJs->linkAfterConfirm(
						'削除',
						array('plugin' => 'note', 'controller' => 'note', 'action' => 'delete', 'noteID' => $val['Note']['NOTE_ID']),
						array('escape'=>false,'buffer'=>false),
						array(
								'description' => '削除してよろしいですか？',
								'type' => 'confirm',
								'close' => false
						),
						array(
								'complete' => "function() {window.location.href='".Router::url(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'))."'} "
						)
				)."</td>";
			?>
		</tr>
		<?php }?>
	</table>
<?php echo $this->element('paginate'); ?>
</div>
<?php echo $this->element('right_menu');?>
<!-- #EndLibraryItem -->
<!-- InstanceEndEditable -->
<!-- contents_End -->
