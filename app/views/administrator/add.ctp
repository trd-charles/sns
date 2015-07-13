<?php
// 完了メッセージ
echo $session->flash();
?>

<!-- header_End -->

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
	'action' => 'add',
	'name' => 'AdministratorAddForm'
))?>
<div id="contents">
	<div id="contentsArea" class="clearfix">
		<!-- InstanceBeginEditable name="contents" -->
		<h2 class="mb20">ユーザ作成</h2>
		<div id="contentsCenter" class="mb60">
			<div id="contentsCenterTop">
				<div id="contentsCenterBtm">
					<div class="setupArea">
						<!-- page1_Start -->
						<div id="page1" class="tabBox">
							<div class="tabBoxIndexAreaS">
								<div>
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
											<th>パスワード<span class='required'>*</span></th>
											<td class="inputArea"><?php
											echo $form->text('EDIT_PASSWORD', array(
												'class' => $form->error('EDIT_PASSWORD') ? 'f_errors' : ''
											));
											?><?php

											echo $form->error('EDIT_PASSWORD', array(
												'class' => 'errors'
											));
											?></td>
										</tr>
									</table>
									<p class="makeBtn">
							<?php
							echo $customHtml->hiddenToken();
							?>
							<?php
							echo $form->submit('profile/bt_save.gif', array(
								'div' => false,
								'name' => 'submit',
								'alt' => '作成する',
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