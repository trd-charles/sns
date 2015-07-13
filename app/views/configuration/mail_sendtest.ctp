<?php	//完了メッセージ
	echo $session->flash();
?>

<!-- header_End -->

<!-- contents_Start -->
<?php echo $html->css("setup","stylesheet",array('media'=>'screen'))."\n"; ?>
<?php echo $html->script("tab")."\n"; ?>
<?php echo $javascript->link('forms_mail'); ?>
<?php echo $form->create(null, array('type'=>'post','url' => array('controller' => 'Configurations', 'action'=>'mail_sendtest'), 'name' => 'AdministratorAddForm'))?>
<!-- InstanceBeginEditable name="contents" -->
<h2 class="mb20">メール設定</h2>
<ul id="tab" style="width:960px;">
			<li><?php echo  $html->link('<span>ユーザ管理</span>',array('controller' => 'administrators','action' => 'index'),array('escape' => false, 'div' => false));?></li>
			<li><?php echo  $html->link('<span>システム設定</span>',array('controller' => 'configurations','action' => 'index'),array('escape' => false, 'div' => false));?></li>
			<li><?php echo  $html->link('<span>プラグイン管理</span>',array('controller' => 'plugins'),array('escape' => false, 'div' => false));?></li>
			<li><?php echo  $html->link('<span>ログ削除</span>',array('controller' => 'administrators','action' => 'delete_log'),array('escape' => false, 'div' => false));?></li>
			<li><?php echo  $html->link('<span>アクセス制限</span>',array('controller' => 'configurations','action' => 'access'),array('escape' => false, 'div' => false)); ?></li>
			<li class="present"><?php echo  $html->link('<span>メール設定</span>',array('controller' => 'configurations','action' => 'mail'),array('escape' => false, 'div' => false));?></li>
</ul>
		<div id="contentsCenter" class="mb60"><div id="contentsCenterTop"><div id="contentsCenterBtm">
			<div class="setupArea">
				<div class="SetupMailTable">
					<table>
						<tr>
						<th class="TitleMain" colspan="3">・メール送信テスト</th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">送信先アドレス</th>
							<td style="width: 650px"><?php echo $form->input('TO_MAIL', array('type' => 'text', 'label' => false, 'legend' => false,'class'=>'join_config', 'maxLength' => '70'));?>
							　　送信先のアドレスです。未入力の場合は、管理者に送信されます。
							</td>
						</tr>
						<tr>
						<td class="FrontSpace"></td>
						<th colspan="2"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">メールの種類</th>
							<td><?php
									$template_items = array(
											'0' => 'メール送信確認メール');
// 											'1' => '新規登録_管理者宛メール',
// 											'2' => '新規登録_ユーザ宛メール',
// 											'3' => '新規登録承認メール',
// 											'4' => '新規登録拒否メール',
// 											'5' => '登録完了メール',
// 											'6' => 'ユーザ招待メール',
// 											'7' => 'グループ追加メール',
// 											'8' => 'グループ招待メール',
// 											'9' => 'パスワード再設定メール'
// 											);
									echo $form->select('MAIL_TEMPLATE', $template_items, null,array('label' => false, 'legend' => false,'class'=>'join_config', 'empty' => false));?>
							<br />
							　　メールの種類を選択してください。
							</td>
						</tr>
						<tr>
						<td class="FrontSpace"></td>
						<th colspan="2"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td>
								<?php $img_url = $html->webroot('/img/'); ?>
								<?php echo $html->image('common/bt_back.jpg', array('url' => array('controller' => 'Configurations', 'action' => 'mail'), 'onmouseover' => "this.src='$img_url/common/bt_back_on.jpg'", 'onmouseout' => "this.src='$img_url/common/bt_back.jpg'")); ?>
							<td>
								<?php echo $customHtml->hiddenToken(); ?>
								<?php echo $form->submit('common/bt_send.gif', array('name' => 'MAIL_SENDTEST', 'id' => 'SUBMIT_SENDTEST', 'onmouseover' => "this.src='$img_url/common/bt_send_on.gif'", 'onmouseout' => "this.src='$img_url/common/bt_send.gif'", 'onclick' => 'stop_button(this)')); ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</div></div></div>
		<?php echo $form->end();?>
<!-- InstanceEndEditable -->
<!-- contents_End -->

<!-- InstanceBeginEditable name="jsBtm" -->

<!-- InstanceEndEditable -->
<!-- contents_End -->