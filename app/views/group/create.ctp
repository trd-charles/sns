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
<h2 class="mb20">グループ作成</h2>
<div id="contentsLeft">

	<!-- InstanceBeginEditable name="contents" -->

	<div id="contentsCenter2" class="mb60">
		<div id="contentsCenterTop2">
			<div id="contentsCenterBtm2">

				<div class="setupArea">
					<!-- page1_Start -->
					<div id="page1" class="tabBox">
						<div class="userEntryTable">
						<?php
						echo $form->create('Group', array(
							'type' => 'post',
							'action' => 'create',
							'name' => 'FiendIndexForm'
						))?>
						<table cellpadding="0" cellspacing="0">
								<tr>
									<th>グループ名<span class='required'>*</span></th>
									<td colspan="2">
								<?php
								echo $form->text('NAME', array(
									'style' => 'width :400px',
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
									<th>公開設定</th>
									<td colspan="2"><?php
									echo $form->radio('TYPE', $group_type, array(
										'label' => false,
										'legend' => false,
										'value' => $type,
										'class' => $form->error('TYPE') ? 'f_errors' : '',
										'style' => 'margin-right:10px;margin-left:15px'
									));
									?>
								<?php
								echo $form->error('TYPE', array(
									'class' => 'errors'
								));
								?>
								</td>
								</tr>
								<tr>
									<td></td>
									<td colspan="2">「公開」に設定すると、すべてのグループ一覧に表示されるようになり、<br />誰でもグループに参加することができます。
									</td>
								</tr>
								<tr>
									<td></td>
									<td colspan="2">「非公開」に設定すると、すべてのグループ一覧には表示されなくなり、<br />参加にはグループ管理者の承認が必要になります。
									</td>
								</tr>
								<tr>
									<th>グループ概要</th>
									<td colspan="2"><?php
									echo $form->textarea('DESCRIPTION', array(
										'style' => 'width :400px',
										'class' => $form->error('DESCRIPTION') ? 'f_errors' : ''
									));
									?>
								<?php
								echo $form->error('DESCRIPTION', array(
									'class' => 'errors'
								));
								?>
								</td>
								</tr>
							</table>
						<?php
						echo $customHtml->hiddenToken();
						?>
						<?php
						echo $form->submit('profile/bt_save.gif', array(
							'div' => false,
							'name' => 'submit',
							'alt' => '作成する',
							'onclick' => 'this.disabled = true;this.form.submit();'
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
	echo $html->link($html->image('common/bt_back.jpg', array(
		'class' => 'on',
		'alt' => '戻る'
	)), array(
		'controller' => 'groups',
		'action' => 'index'
	), array(
		'escape' => false,
		'style' => 'padding-left:10px;'
	));
	?>
</div>
<!-- InstanceEndEditable -->


<!-- contents_End -->
<!-- InstanceBeginEditable name="jsBtm" -->
<?php
echo $this->element('right_menu');
?>
<!-- InstanceEndEditable -->