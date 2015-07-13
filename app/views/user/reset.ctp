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
<h2 class="mb20"><?php
echo h($title_text);
?></h2>

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
							'action' => 'reset',
							'name' => 'PasswordReminder'
						))?>
						<table>
								<tr>
									<th>パスワード<span class='required'>*</span></th>
									<td><?php
									echo $form->password('EDIT_PASSWORD', array(
										'style' => 'width: 200px',
										'class' => $form->error('EDIT_PASSWORD') ? 'f_errors' : ''
									));
									?>
									<?php
									echo $form->error('EDIT_PASSWORD', array(
										'class' => 'errors'
									));
									?>
								</td>
								</tr>
								<tr>
									<th>パスワード(確認)<span class='required'>*</span></th>
									<td><?php
									echo $form->password('EDIT_PASSWORD_CHECK', array(
										'style' => 'width: 200px',
										'class' => $form->error('EDIT_PASSWORD_CHECK') ? 'f_errors' : ''
									));
									?>
									<?php
									echo $form->error('EDIT_PASSWORD_CHECK', array(
										'class' => 'errors'
									));
									?>
								</td>
								</tr>
							</table>
						<?php
						if ($session->check('Message.flash')) {
							echo "<span class=\"must\" style=\"color: #FF0000\">{$session->flash()}</span>";
						}
						?>
						<?php
						echo $form->hidden('KEY');
						?>
						<?php
						echo $form->submit('setup/bt_make.jpg', array(
							'div' => false,
							'name' => 'submit',
							'alt' => '送信する'
						));
						?>
						<?php
						echo $form->end();
						?>
					</div>
					</div>

					<br />
		<?php
		echo $html->link('トップヘ戻る', array(
			'controller' => 'users',
			'action' => 'login'
		), array(
			'style' => 'padding-top:40px;'
		));
		?>
		</div>
			</div>
		</div>
	</div>

</div>
