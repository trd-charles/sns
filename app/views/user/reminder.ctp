<!-- contents_Start -->
<div id="contentsArea" class="clearfix">
<?php
echo $html->css("setup", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<?php

echo $html->script("tab") . "\n";
?>
<h2 class="mb20">パスワード再設定</h2>

	<!-- InstanceBeginEditable name="contents" -->

	<div id="contentsCenter" class="mb60">
		<div id="contentsCenterTop">
			<div id="contentsCenterBtm">

				<div class="setupArea">
					<!-- page1_Start -->
					<div id="page1" class="tabBox">
						<div class="userEntryTable">
						<?php
						echo $form->create('User', array(
							'type' => 'post',
							'action' => 'reminder',
							'name' => 'PasswordReminder'
						))?>
						<table cellpadding="0" cellspacing="0">
								<tr>
									<th>メールアドレス</th>
									<td>
								<?php
								echo $form->text('MAIL', array(
									'style' => 'width :400px',
									'class' => $form->error('MAIL') ? 'f_errors' : ''
								));
								?>
								<br />
								<?php
								echo $form->error('MAIL', array(
									'class' => 'errors'
								));
								?>
								</td>
								</tr>
							</table>
						<?php
						echo $form->submit('message/bt_submit.gif', array(
							'div' => false,
							'name' => 'submit',
							'alt' => '送信する',
							'style' => 'padding-top:20px;'
						));
						?>
						<?php
						echo $form->end();
						?>
					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	echo $html->link($html->image('common/bt_back.jpg'), array(
		'controller' => 'users',
		'action' => 'login'
	), array(
		'escape' => false,
		'style' => 'padding-left:10px;'
	));
	?>
</div>


