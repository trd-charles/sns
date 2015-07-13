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
	'action' => 'access_edit',
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
						<tr>
							<th style="width: 120px; padding-left: 50px">メンテナンスモード</th>
							<td style="width: 150px;"><?php
							echo $form->radio('MAINTE_FLG', $mainte_flg, array(
								'label' => false,
								'legend' => false,
								'class' => 'join_config'
							));
							?>
							</td>
							<td>「ON」にするとメンテナンスモードになります</td>
						</tr>
						<tr>
							<th style="width: 100px; padding-left: 50px">IPホスト制限</th>
							<td style="width: 140px; height: 40px;"><?php
							echo $form->textarea('IPHOST', array(
								'label' => false,
								'legend' => false,
								'class' => ''
							));
							?>
							</td>
							<td>複数のIPアドレス、ホストを登録することができます。<br /> IPアドレス、ホストごとに改行してください。
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
