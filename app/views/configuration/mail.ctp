<?php	//完了メッセージ
	echo $session->flash();
?>

<!-- header_End -->

<!-- contents_Start -->
<?php echo $html->css("setup","stylesheet",array('media'=>'screen'))."\n"; ?>
<?php echo $html->script("tab")."\n"; ?>
<?php echo $javascript->link('forms_mail'); ?>
<?php echo $form->create(null, array('type'=>'post','url' => array('controller' => 'Configurations', 'action'=>'mail'), 'name' => 'AdministratorAddForm'))?>
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
						<p style="text-align:right;">
						<?php echo $html->link('送信テスト', array('controller' => 'Configurations', 'action' => 'mail_sendtest')); ?>
						</p>
					<table>
						<tr>
						<th class="TitleMain" colspan="4">・SMTP設定</th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">SMTP使用</th>
							<td></td>
							<td style="width: 650px"><?php echo $form->radio('SMTP_STATUS', $mail_radio['SMTP_STATUS'], array('label' => true, 'legend' => false, 'class' => 'join_config smtp_setting'));?>
							<br />
							　　SMTPサーバーを「あり」に設定すると、SMTPサーバーを利用して送信されます。
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">送信者名</th>
							<td><?php echo $html->image('common/i_must.jpg', array('id' => 'i_must_from_name', 'class' => 'i_smtp_set')); ?></td>
							<td><?php echo $form->input('FROM_NAME', array('type' => 'text', 'label' => false, 'class' => 'smtp_set', 'maxLength' => '70'));?>
							　　メールを送信する際の発信者名です。
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">送信者アドレス</th>
							<td><?php echo $html->image('common/i_must.jpg', array('id' => 'i_must_from_mail', 'class' => 'i_smtp_set')); ?></td>
							<td><?php echo $form->input('FROM_MAIL', array('type' => 'text', 'label' => false, 'class' => 'smtp_set', 'maxLength' => '70'));?>
							　　メールを送信する際の発信者メールアドレスです。
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">プロトコル</th>
							<td></td>
							<td><?php echo $form->radio('SMTP_PROTOCOL', $mail_radio['SMTP_PROTOCOL'], array('label' => true, 'legend' => false, 'class' => 'join_config smtp_set smtp_auth_setting'));?>
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">SMTPセキュリティ</th>
							<td></td>
							<td><?php echo $form->radio('SMTP_SECURITY', $mail_radio['SMTP_SECURITY'], array('label' => true, 'legend' => false,'class' => 'join_config smtp_set'));?>
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">SMTPサーバ</th>
							<td><?php echo $html->image('common/i_must.jpg', array('id' => 'i_must_smtp_host', 'class' => 'i_smtp_set')); ?></td>
							<td><?php echo $form->input('SMTP_HOST', array('type' => 'text', 'label' => false, 'class'=>'smtp_set', 'maxLength' => '70'));?>
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">ポート番号</th>
							<td><?php echo $html->image('common/i_must.jpg', array('id' => 'i_must_smtp_port', 'class' => 'i_smtp_set')); ?></td>
							<td><?php echo $form->input('SMTP_PORT', array('type' => 'text', 'label' => false, 'class' => 'smtp_set', 'maxLength' => '70'));?>
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">SMTPユーザ</th>
							<td><?php echo $html->image('common/i_must.jpg', array('id' => 'i_must_smtp_user', 'class' => 'i_smtp_auth_set')); ?></td>
							<td><?php echo $form->input('SMTP_USER', array('type' => 'text', 'label' => false, 'class'=>'smtp_auth_set', 'maxLength' => '70'));?>
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">SMTPパスワード</th>
							<td><?php echo $html->image('common/i_must.jpg', array('id' => 'i_must_smtp_pass', 'class' => 'i_smtp_auth_set')); ?></td>
							<td><?php echo $form->input('SMTP_PASS', array('type' => 'text', 'label' => false, 'class' => 'smtp_auth_set', 'maxLength' => '70'));?>
							</td>
						</tr>
						<tr>
						<td></td>
						<th colspan="3"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>
						<tr>
						<td colspan="4">
							<?php echo $customHtml->hiddenToken(); ?>
							<?php $img_url = $html->webroot('/img/'); ?>
							<?php echo $form->submit('profile/bt_save.gif', array('name' => 'CONFIRM', 'onmouseover' => "this.src='$img_url/profile/bt_save_on.gif'", 'onmouseout' => "this.src='$img_url/profile/bt_save.gif'")); ?>
						</td>
					</table>
					<?php //echo $customHtml->hiddenToken(); ?>
					<?php //echo $form->submit('profile/bt_save.gif', array('name' => 'CONFIRM')); ?>
				</div>
			</div>
		</div></div></div>
		<?php echo $form->end();?>
<!-- InstanceEndEditable -->
<!-- contents_End -->

<!-- InstanceBeginEditable name="jsBtm" -->

<!-- InstanceEndEditable -->
<!-- contents_End -->