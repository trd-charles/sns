<?php	//完了メッセージ
	echo $session->flash();
?>
<script type="text/javascript">
function change_draft(status) {
	if(status == 0) {
		$("#NotePUBLIC").attr('disabled', 'disabled');
		$("#NotePUBLIC > option[value='OnlyMe']").attr('selected', 'selected');
	}
	else {
		$("#NotePUBLIC").removeAttr('disabled');
		$("#NotePUBLIC > option[value='All']").attr('selected', 'selected');
	}
}

function note_image_upload(url){
	$('.upload').hide();

	 $('.indexlists').upload(url,function(res) {
			if(res.match(/^[a-f0-9]+$/)){
				popupclass.confirm_open('アップロードに成功しました', 'alert', function() {
					$.ajax({
						complete:
							function (XMLHttpRequest, textStatus) {
								$(".popup_upload_button").hide();
								$(".preview img").attr('src', '<?php echo $html->url(array('plugin' => null, 'controller' => 'storages', 'action' => 'preview')); ?>/'+res);
								$(".form-row-value input[name=src]").val('<?php echo $html->url(array('plugin' => null, 'controller' => 'storages', 'action' => 'preview'), false); ?>/'+res);
								$(".image_url").hide();
								$(".wysiwyg-dialog-modal-div").show();
								popupclass.popup_close();
								$(".wysiwyg-dialog").show();

							},
						div:false,
						type:'POST',
						data:"token=<?php echo h(session_id());?>",
						update:null,
						url:"<?php echo preg_replace('/\//','\/', $html->url(array('plugin' => 'note', 'controller' => 'note', 'action' => 'insert_image')));?>\/" + res
					});
				}, null, true);

			}else{
				popupclass.confirm_open(res, 'alert', function() { $('.upload').show(); });
			}
	    },'html');
}

$(document).ready(function() {
		$("textarea.markItUp").wysiwyg({
			rmUnusedControls: true,
			controls: {
				bold: { visible : true },
				italic: { visible : true },
				underline: { visible : true },
				insertOrderedList: { visible : true },
				insertUnorderedList: { visible : true },
				insertImage: { visible : true }
			},
			initialContent: 'ここに入力'
		});
		var status = $("#NoteSTATUS").val();
		if(status == 0) {
			$("#NotePUBLIC").attr('disabled', 'disabled');
			$("#NotePUBLIC > option[value='OnlyMe']").attr('selected', 'selected');
		}
		else {
			$("#NotePUBLIC").removeAttr('disabled');
			//$("#NotePUBLIC > option[value='All']").attr('selected', 'selected');
		}
});

function loadNoteImagePopup() {
	$(".wysiwyg-dialog-modal-div").hide();
	$.ajax({
		complete:function (XMLHttpRequest, textStatus) {
			popupclass.popup_view(XMLHttpRequest);
			popupclass.popup_open();
		},
		div:false,
		type:'POST',
		data:"token=<?php echo h(session_id());?>",
		update:null,
		url:"<?php echo preg_replace('/\//','\/', $html->url(array('plugin' => 'note', 'controller' => 'note', 'action' => 'upload', $user['User']['USR_ID'])));?>"
	});
}
</script>
<!-- header_End -->

<!-- contents_Start -->
<?php echo $html->css("setup","stylesheet",array('media'=>'screen'))."\n"; ?>
<?php echo $html->script("tab")."\n"; ?>
<?php echo $html->script("/note/js/markitup/jquery.markitup")."\n"; ?>
<?php echo $html->script("/note/js/markitup/sets/html/set")."\n"; ?>
<?php echo $html->css("/note/js/markitup/skins/markitup/style","stylesheet",array('media'=>'screen'))."\n"; ?>
<?php echo $html->css("/note/js/markitup/sets/html/style","stylesheet",array('media'=>'screen'))."\n"; ?>
<?php echo $html->script("/note/js/jwysiwyg/jquery.wysiwyg")."\n"; ?>
<?php echo $html->css("/note/js/jwysiwyg/jquery.wysiwyg")."\n"; ?>
<?php echo $html->script("/note/js/jwysiwyg/wysiwyg.image")."\n"; ?>
<div id="base_url" style="display:none"><?php echo $html->url('/'); ?></div>
<h2 class="mb20">ノート作成</h2>
<div id="contentsLeft">
<!-- InstanceBeginEditable name="contents" -->
				<!-- page1_Start -->
		<div id="page1" class="tabBox">
			<?php echo $form->create(null,array('type'=>'post','controller' => 'note', 'action'=> $this->action, 'plugin' => 'note'))?>
			タイトル<span class='required'>*</span>
			<?php echo $form->text('Note.TITLE', array('div' => false, 'style' => 'width :370px','class'=>$form->error('TITLE')?'f_errors':''));?>
			<?php echo $form->input('Note.STATUS', array('options' => $status, 'label' => false, 'div' => false, 'onchange' => 'change_draft(value)'));?>
			<?php echo $form->input('Note.PUBLIC', array('options' => $public, 'label' => false, 'div' => false));?>
			<?php echo $form->error('TITLE',array('class'=>'errors'));?>
			<?php echo $form->textarea('Note.CONTENT', array('style' => 'width:880px;', 'class' => ($form->error('CONTENT')?'f_errors':'').' markItUp'));?>
			<?php if($this->action == 'edit') echo $form->hidden('Note.NOTE_ID', array('value' => $this->params['named']['noteID']));?>

			<?php echo $customHtml->hiddenToken(); ?>
			<?php if(isset($note_status)) echo $form->hidden('Note.STATUS_OLD', array('value' => $note_status)); ?>
			<?php echo $form->submit('profile/bt_save.gif',array('class'=>'on', 'div'=>false,'name'=>'submit','alt'=>'作成する')); ?>
			<?php echo $html->link($html->image('common/bt_back.jpg',array('alt'=>'戻る', 'align' => 'top','style'=>'padding-left:10px;padding-bottom:5px')),array('controller'=>'note','action'=>'index', 'plugin' => 'note'),array('escape'=>false));?>
			<?php echo $form->end();?>
		</div>
</div>