<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php
echo $html->css("setup", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<?php

echo $html->script("tab") . "\n";
?>
<?php

echo $form->create('Administrator', array(
	'type' => 'post',
	'action' => 'edit',
	'name' => 'AdministratorAddForm'
))?>
<div id="contents">
	<div id="contentsArea" class="clearfix">
		<!-- InstanceBeginEditable name="contents" -->
		<h2 class="mb20">ユーザ編集</h2>
		<div id="contentsCenter" class="mb60">
			<div id="contentsCenterTop">
				<div id="contentsCenterBtm">
					<div class="setupArea">
						<!-- page1_Start -->
						<div id="page1" class="tabBox">
							<div class="tabBoxIndexAreaS">
								<div id="tabBoxIndexList">
									<!-- page1_Start -->
									<table cellpadding="0" cellspacing="0" class="userEntryTable">
										<tr>
											<th>名前<span class='required'>*</span></th>
											<td class="inputArea"><?php
											echo $form->text('NAME', array(
												'class' => $form->error('NAME') ? 'f_errors' : ''
											));
											?><?php

											echo $form->error('NAME', array(
												'class' => 'errors'
											));
											?></td>
										</tr>
										<tr>
											<th>メールアドレス<span class='required'>*</span></th>
											<td class="inputArea"><?php
											echo $form->text('MAIL', array(
												'class' => $form->error('MAIL') ? 'f_errors' : ''
											));
											?><?php

											echo $form->error('MAIL', array(
												'class' => 'errors'
											));
											?></td>
										</tr>
										<tr>
											<th>パスワード変更</th>
											<td><?php
											echo $form->radio('PASS_C', array(
												0 => '変更しない',
												1 => '変更する'
											), array(
												'class' => 'pass_edit',
												'legend' => false,
												'label' => false,
												'value' => $pass_c,
												'div' => false,
												'style' => 'width:30px'
											));
											?></td>

										</tr>
										<tr class='pass' style='display: none'>
											<th>パスワード<span class='required'>*</span></th>
											<td class="inputArea"><?php
											echo $form->text('EDIT_PASSWORD', array(
												'class' => $form->error('EDIT_PASSWORD') ? 'f_errors pass_val' : 'pass_val'
											));
											?><?php

											echo $form->error('EDIT_PASSWORD', array(
												'class' => 'errors'
											));
											?></td>
										</tr>
										<script>
								window.onload = pass_c;
								function pass_c(){
									if($('.pass_edit:checked').val()==1){
										$('.pass').show();
									}else{
										$('.pass').hide();
										$('.pass_val').val(null);
									}
								}
								$('.pass_edit:radio').change(function(){
									if($('.pass_edit:checked').val()==1){
										$('.pass').show();
									}else{
										$('.pass').hide();
										$('.pass_val').val(null);
									}
								});
								</script>
									</table>
									<p class="makeBtn">
							<?php
							echo $form->hidden('USR_ID');
							?>
							<?php
							echo $customHtml->hiddenToken();
							?>
							<?php
							echo $form->submit('profile/bt_save.gif', array(
								'div' => false,
								'name' => 'submit',
								'alt' => '変更する',
								'style' => 'margin-top:0px;float:left;'
							));
							?>
							<?php
							echo $form->submit('common/bt_back.jpg', array(
								'div' => false,
								'name' => 'can',
								'escape' => false,
								'style' => 'margin-left:30px'
							));
							?>
							</p>
								</div>
							</div>
						</div>


					</div>
				</div>
			</div>
		</div>
		<!-- InstanceEndEditable -->
	</div>
</div>
<?php
echo $form->end();
?>
<!-- contents_End -->

<!-- InstanceBeginEditable name="jsBtm" -->

<!-- InstanceEndEditable -->