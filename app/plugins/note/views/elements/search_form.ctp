<div id="searchArea">
<?php
	if(isset($this->params['named']['tab']) && $this->params['named']['tab'] == 'All'){
		$form_action = 'tab:All';
	}else{
		$form_action = 'tab:OnlyMe';
	}
?>
<?php echo $form->create(null ,array('type'=>'post','action' => './index/'.$form_action, 'name' => 'NoteIndexForm'))?>
<p>
	<?php echo $form->text('KEYWORD',array('class'=>'_input'));?>
	<?php echo $customHtml->hiddenToken(); ?>
	<?php echo $form->submit('user/bt_search_user.jpg',array('div'=>false,'name'=>'submit','alt'=>'検索する','class'=>'on')); ?>
</p>
<?php echo $form->end();?>
</div>