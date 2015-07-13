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

echo $form->create('Configuration', array(
	'type' => 'post',
	'action' => 'edit',
	'name' => 'AdministratorAddForm'
))?>
<!-- InstanceBeginEditable name="contents" -->
<h2 class="mb20">設定</h2>

<div id="contentsCenter" class="mb60">
	<div id="contentsCenterTop">
		<div id="contentsCenterBtm">
			<div class="setupArea">
				<div class="userEntryTable">
					<table>
						<th>参加設定</th>
						<tr>
							<th style="width: 80px; padding-left: 50px">承認</th>
							<td style="width: 150px;"><?php
							echo $form->radio('APPOROVAL', $apporoval, array(
								'label' => false,
								'legend' => false,
								'class' => 'join_config'
							));
							?>
							</td>
							<td>承認を「必要」に設定すると、ユーザがSNSに参加する際に管理者の承認が必要となります。</td>
						</tr>
						<tr>
							<th style="width: 80px; padding-left: 50px">招待</th>
							<td style="width: 150px;"><?php
							echo $form->radio('INVITE', $invite, array(
								'label' => false,
								'legend' => false,
								'class' => 'join_config'
							));
							?>
							</td>
							<td>招待を「あり」に設定すると、一般ユーザがSNSへの招待メールを送ることができるようになります。</td>
						</tr>
						<tr>
							<th style="width: 80px; padding-left: 50px">一般登録</th>
							<td style="width: 150px;"><?php
							echo $form->radio('GENERAL', $general, array(
								'label' => false,
								'legend' => false,
								'class' => 'join_config'
							));
							?>
							</td>
							<td>一般登録を「あり」に設定すると、新規ユーザが自分で登録を行えるようになります。</td>
						</tr>
						<th>ユーザ設定</th>
						<tr>
							<th style="width: 80px; padding-left: 50px">退会</th>
							<td style="width: 150px;"><?php
							echo $form->radio('WITHDRAWAL', $withdrawal, array(
								'label' => false,
								'legend' => false,
								'class' => 'join_config'
							));
							?>
							</td>
							<td>退会を「あり」に設定すると、ユーザが自ら退会を行えるようになります。<br />
								「なし」に設定した場合は、管理者側の操作でしか退会が行えなくなります。
							</td>
						</tr>
						<th>禁止ワード</th>
						<tr>
							<th style="width: 80px; padding-left: 50px">文字列</th>
							<td style="height: 100px;"><?php
							echo $form->textarea('NGWORD', array(
								'style' => 'height:100px;width:100px;',
								'label' => false,
								'class' => ''
							));
							?>
							</td>
							<td>複数の文字列を登録することができます。<br /> 文字列ごとに改行してください。
							</td>
						</tr>
					</table>
					<?php
					echo $customHtml->hiddenToken();
					?>
					<?php
					echo $form->submit('profile/bt_save.gif', array(
						'escape' => false,
						'class' => 'on',
						'div' => false,
						'name' => 'submit',
						'alt' => '変更する'
					));
					?>
					<?php
					echo $form->submit('common/bt_back.jpg', array(
						'div' => false,
						'name' => 'can',
						'class' => 'on',
						'escape' => false,
						'style' => 'margin-left:30px'
					));
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
echo $form->end();
?>

<!-- InstanceEndEditable -->
<!-- contents_End -->

<!-- InstanceBeginEditable name="jsBtm" -->

<!-- InstanceEndEditable -->
<!-- contents_End -->
