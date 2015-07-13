<h2 class="mb20">ノート</h2>
<div id="contentsCenter" class="mb60"><div id="contentsCenterTop"><div id="contentsCenterBtm">
<div class="messageArea wordBreak" style="padding:40px;">

<?php echo $session->flash(); ?>
<?php echo $html->css("setup","stylesheet",array('media'=>'screen'))."\n"; ?>
<?php echo $html->css('/note/css/note','stylesheet', array('media' => 'screen'))."\n"; ?>
	<div id="page1" class="tabBox">
	<div id="note_title" style="border-left: 2px solid #72936F;padding-left:20px;margin-bottom:15px;">
		<div style="font-size:17px;"><?php echo h($note['Note']['TITLE']); ?></div>
		<div class="note_user"><div style="font-weight:bold;float:left;width:70px;" >作成者</div> : <?php echo h($note['User']['NAME']); ?></div>
		<?php if($isMyNote) { ?>
		<div><div style="font-weight:bold;float:left;width:70px;" >公開範囲</div> : <?php echo h($public[$note['Note']['PUBLIC']]); ?></div>
		<div style="float:left;margin-bottom:10px;"><div style="font-weight:bold;float:left;width:70px;" >ステータス</div> : <?php echo h($status[$note['Note']['STATUS']]); ?></div>
		<div style="text-align:right;margin-bottom:10px;">
			<?php
			echo $customHtml->hiddenToken();
				echo $html->link($html->image('/note/img/note_edit.jpg', array('class' => 'on')), array('plugin' => 'note', 'controller' => 'note', 'action' => 'edit', 'noteID' => h($note['Note']['NOTE_ID'])), array('style' => 'margin-right:10px;','escape' => false)) ;
				//削除リンク
				echo $customJs->linkAfterConfirm(
						$html->image('/note/img/note_delete.jpg', array('class' => 'on')),
						array('plugin' => 'note', 'controller' => 'note', 'action' => 'delete', 'noteID' => h($note['Note']['NOTE_ID'])),
						array('escape'=>false,'buffer'=>false),
						array(
								'description' => '削除してよろしいですか？',
								'type' => 'confirm',
								'close' => false
						),
						array(
								'complete' => "function() {window.location.href='".Router::url(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'))."'} "
						)
				);
			?>
		</div>
		<?php }?>
	</div>


	<iframe id="note_content" name="note_contetnt_frame" frameborder="0" style="font:12px;border: 1px #CCCCCC solid;" scrolling=”no” src="<?php echo $html->url(array('plugin' => 'note', 'controller' => 'note', 'action' => 'renderNoteContent', 'noteID' => $note['Note']['NOTE_ID']), true);?>">
	<?php echo $note['Note']['CONTENT']?></iframe>
	</div>


</div>
</div></div></div>
