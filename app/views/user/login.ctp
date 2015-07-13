<!-- contents_Start -->
<div id="contentsLogin" class="clearfix">
	<div class="loginMid">
		<div class="loginTop">
			<div class="loginBtm clearfix">
				<div class="loginLeft">
					<h2><?php
					echo $html->image('login/i_login_txt.jpg', array(
						'alt' => '抹茶SNSで社内に情報力とひらめきを 社内コミュニケーションを活性化し、企業を元気にします。'
					));
					?></h2>
					<p><?php
					echo $html->image('login/i_login_img.jpg', array(
						'alt' => '社員同士での発言 グループでの会話 ファイル共有'
					));
					?></p>
				</div>
				<div class="loginRight">
		<?php
		echo $form->create('User', array(
			'type' => 'post',
			'action' => 'login',
			'name' => 'UserLoginForm'
		))?>
		<table cellpadding="0" cellspacing="0">
		<?php
		if ($session->check('Message.flash')) {
			echo "<tr>" . "<th>&nbsp;</th>" . '<td class="txtRed">' . $session->flash() . '</td>' . "</tr>";
		} else {
			echo "<tr>" . "<th>&nbsp;</th>" . "<td></td>" . "</tr>";
		}
		?>
		<tr>
							<th>メールアドレス</th>
							<td><?php
							echo $form->text('MAIL', array(
								'class' => 'inputs'
							));
							?></td>
						</tr>
						<tr>
							<th>パスワード</th>
							<td><?php
							echo $form->password('PASSWORD', array(
								'class' => 'inputs'
							));
							?></td>
						</tr>
						<tr>
						
						
						<tr>
							<th>&nbsp;</th>
							<td><?php
							echo $form->submit('login/bt_login.jpg', array(
								'class' => 'on',
								'alt' => 'ログイン',
								'div' => false
							));
							?>&emsp;
				<?php
				echo $html->link('パスワードをお忘れの方', array(
					'controller' => 'users',
					'action' => 'reminder'
				))?>
				&nbsp;
			</td>
			<?php
			echo $form->end();
			?>
	</tr>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- contents_End -->