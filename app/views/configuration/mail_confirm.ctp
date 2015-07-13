<?php	//完了メッセージ
	echo $session->flash();
?>

<!-- header_End -->

<!-- contents_Start -->
<?php echo $html->css("setup","stylesheet",array('media'=>'screen'))."\n"; ?>
<?php echo $html->script("tab")."\n"; ?>
<?php echo $form->create(null, array('type'=>'post', 'url' => array('controller' => 'Configurations', 'action'=>'mail'),'name' => 'AdministratorAddForm'))?>
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
						<th class="TitleMain" colspan="3">・SMTP設定</th>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th class="TitleSub">SMTP使用</th>
							<td style="width: 700px"><?php echo h($mail_radio['SMTP_STATUS'][$this->data['Configuration']['SMTP_STATUS']]); ?>
							<?php  echo $form->hidden('SMTP_STATUS'); ?>
							</td>
						</tr>
						<tr>
							<td class="FrontSpace"></td>
							<th colspan="2"><?php echo $html->image('common/i_line_solid.gif'); ?></th>
						</tr>

						<?php	$smtp_field_name = array('FROM_NAME' => '送信者名', 'FROM_MAIL' => '送信者アドレス', 'SMTP_PROTOCOL' => 'プロトコル', 'SMTP_SECURITY' => 'SMTPセキュリティ', 'SMTP_HOST' => 'SMTPサーバ', 'SMTP_PORT' => 'ポート番号', 'SMTP_USER' => 'SMTPユーザ', 'SMTP_PASS' => 'SMTPパスワード');
						foreach($this->data['Configuration'] as $data_fn => $data) {
							if(array_key_exists($data_fn, $smtp_field_name)) {
								echo "<tr>
									<td class=\"FrontSpace\"></td>
									<th class=\"TitleSub\">";echo h($smtp_field_name[$data_fn]);echo "</th>
									<td>";if(array_key_exists($data_fn, $mail_radio)) {
												echo h($mail_radio[$data_fn][$data]);
											}else {
												echo h($data);
											}
									echo $form->hidden($data_fn);
									echo "</td>
								</tr>
								<tr>
									<td class=\"FrontSpace\"></td>
									<td colspan=\"2\">";echo $html->image('common/i_line_solid.gif');echo "</th>
								</tr>";
							}
						}	?>
					<tr>
					<td>
					<?php $img_url = $html->webroot('/img/'); ?>
					<?php echo $form->submit('common/bt_back.jpg', array('name' => 'BACK', 'onmouseover' => "this.src='$img_url/common/bt_back_on.jpg'", 'onmouseout' => "this.src='$img_url/common/bt_back.jpg'")); ?>
					</td>
					<td>
					<?php echo $form->submit('profile/bt_save.gif', array('name' => 'SAVE', 'onmouseover' => "this.src='$img_url/profile/bt_save_on.gif'", 'onmouseout' => "this.src='$img_url/profile/bt_save.gif'")); ?>
					</td>
					</tr>
					</table>
					<?php echo $form->hidden('WEBROOT', array('value' => $html->url('/', true))); ?>
					<?php echo $customHtml->hiddenToken(); ?>
					<?php //echo $form->hidden('WEBROOT', array('value' => $html->url('/', true))); ?>
					<?php //echo $form->submit('戻る', array('name' => 'BACK')); ?>
					<?php //echo $form->submit('profile/bt_save.gif', array('name' => 'SAVE')); ?>
				</div>
			</div>
		</div></div></div>
		<?php echo $form->end();?>
<!-- InstanceEndEditable -->
<!-- contents_End -->

<!-- InstanceBeginEditable name="jsBtm" -->

<!-- InstanceEndEditable -->
<!-- contents_End -->