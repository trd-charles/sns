<?php
// 完了メッセージ
echo $session->flash();
?>
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
							'action' => 'regist' . (isset($token) ? "?token=$token" : ''),
							'name' => 'UserRegistForm'
						))?>
						<table>
								<tr>
									<th>名前<span class='required'>*</span></th>
									<td><?php
									echo $form->text('NAME', array(
										'style' => 'width: 300px',
										'class' => $form->error('NAME') ? 'f_errors' : ''
									));
									?>
								<?php
								echo $form->error('NAME', array(
									'class' => 'errors'
								));
								?>
								</td>
								</tr>
								<tr>
									<th>メールアドレス<span class='required'>*</span></th>
									<td><?php
									echo $form->text('MAIL', array(
										'style' => 'width: 300px',
										'class' => $form->error('MAIL') ? 'f_errors' : ''
									));
									?>
								<?php
								echo $form->error('MAIL', array(
									'class' => 'errors'
								));
								?>
								</td>
								</tr>
								<tr>
									<th>パスワード<span class='required'>*</span></th>
									<td><?php
									echo $form->text('EDIT_PASSWORD', array(
										'style' => 'width: 300px',
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
							</table>
						<?php
						echo $form->submit('setup/bt_make.jpg', array(
							'div' => false,
							'name' => 'submit',
							'alt' => '作成中'
						));
						?>
						<?php
						echo $form->end();
						?>
					</div>
					</div>

					<br />
				</div>
			</div>
		</div>
	</div>
	<?php
	echo $html->link($html->image('common/bt_back.jpg'), array(
		'controller' => 'users',
		'action' => 'login'
	), array(
		'class' => 'on',
		'escape' => false,
		'style' => 'padding-left:10px;'
	));
	?>
</div>
<!-- contents_End -->