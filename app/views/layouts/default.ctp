<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Content-Style-Type" content="text/css" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta http-equiv="imagetoolbar" content="no" />
<meta name="description" content="抹茶SNS" />
<meta name="keywords" content="抹茶SNS" />
<meta name="robots" content="index,follow" />
<!-- InstanceBeginEditable name="doctitle" -->
<title><?php
echo h($main_title);
?>｜抹茶SNS</title>
<!-- InstanceEndEditable -->
<link rel="icon"
	href="<?php
	echo $this->webroot;
	?>img/common/favicon.ico"
	" type="image/x-icon" />
<?php
echo $html->css("import", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<?php

echo $html->css("print", "stylesheet", array(
	'media' => 'print'
)) . "\n";
?>
<!-- InstanceBeginEditable name="css" -->
<?php
echo $html->css("popup", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<!-- InstanceEndEditable -->
<?php
echo $html->script("common") . "\n";
?>
<?php

echo $html->css("colorbox.css", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<!-- InstanceBeginEditable name="js" -->
<?php
echo $html->script("jquery") . "\n";
?>
<?php

echo $html->script("tab") . "\n";
?>
<?php

echo $html->script("jquery.upload") . "\n";
?>
<?php

echo $html->script("thickbox") . "\n";
?>
<?php

echo $html->script("forms") . "\n";
?>
<?php

echo $html->script("jquery.colorbox-min") . "\n";
?>
<?php

echo $html->script("jquery.cookie") . "\n";
?>
<?php

echo $html->script("ajax") . "\n";
?>
<?php

echo $html->script("ready") . "\n";
?>
<?php

echo $html->script("toggle") . "\n";
?>
<!-- InstanceEndEditable -->
</head>
<body>
	<!-- wrapper_Start -->
	<div id="popup-notice"></div>
	<div id="wrap">
		<a id="top"></a>
		<!-- header_Start -->
		<div id="header">
			<div id="headerArea" class="clearfix">
				<h1><?php
				echo $html->link($html->image('common/i_logo.jpg', array(
					'alt' => '抹茶SNS"'
				)), array(
					'controller' => 'homes',
					'action' => '.',
					'plugin' => null
				), array(
					'escape' => false
				));
				?></h1>
				<div id="headerRight">
					<div id="headerSub" class="clearfix">
						<div id="search">
				<?php
				echo $form->create('Home', array(
					'url' => array(
						'controller' => 'searches',
						'action' => 'index'
					)
				));
				?>
				<?php
				echo $customHtml->hiddenToken();
				?>
				<?php
				echo $form->input("KEYWORD", array(
					'div' => false,
					'label' => false,
					'class' => 'smallKeyword'
				));
				?>
				<?php
				echo $form->input('FILTER', array(
					'div' => false,
					'label' => false,
					'class' => 'smallselectBox',
					'options' => $searchList
				));
				?>
				<?php
				echo $form->submit('common/bt_search.jpg', array(
					'div' => false,
					'name' => 'submit',
					'alt' => '検索',
					'class' => 'submit'
				));
				?>
				<?php
				echo $form->end();
				?>
				<span class="userName"><a href="#"><?php //echo "ようこそ".$user['User']['NAME']."さん";?></a></span>
						</div>
						<ul id="headerSubNav">
							<!-- InstanceBeginEditable name="new" -->
				<?php
				if (isset($notice['Count']) && $notice['Count'] > 0) {
					echo '<li class="subNotOn"><a href="javascript:ToggleNot(' . h($user['User']['USR_ID']) . ', \'' . Router::url(array(
						'controller' => 'timelines',
						'action' => 'notice_read',
						'plugin' => null
					), true) . '\')"><span class="notice_num">' . h($notice['Count']) . '</span></a></li>';
				} else {
					echo '<li class="subNot"><a href="javascript:ToggleNot(' . h($user['User']['USR_ID']) . ', \'' . Router::url(array(
						'controller' => 'timelines',
						'action' => 'notice_read',
						'plugin' => null
					), true) . '\')"></a></li>';
				}
				?>
				<li id="subNotPopup" style="display: none;">
								<h4>通知メッセージ</h4>
								<table cellpadding="0" cellspacing="0" class='wordBreak'>
						<?php
						if ($notice['Count'] > 0 || ($notice['Count'] == 0 && count($notice) > 1)) {
							foreach ($notice as $key => $val) {
								if ($key === 'Count') {
									echo "<tr><th style='border-bottom:0px;'>　</th><td style='border-bottom:0px;'>";
									echo $html->link("もっと表示する。", array(
										'controller' => 'homes',
										'action' => 'notice',
										'plugin' => null
									));
									echo "</td></tr>";
									continue;
								}
								if ($key < 5) {
									if ($key < $notice['Count']) {
										echo "<tr class=notice_" . h($key) . " style='background:#f5ffff'>";
									} else {
										echo "<tr class=notice_" . h($key) . ">";
									}
									echo "<th>" . $html->image(array(
										'controller' => 'storages',
										'action' => 'thumbnail/' . $val['P_User']['USR_ID'],
										'plugin' => null
									), array(
										'style' => 'width:40px;height:40px'
									)) . "</th>";
									echo "<td>";
									if ($val['Notice']['ACT_ID'] == 0) {
										echo $html->link($val['P_User']['NAME'] . "さんがあなたの投稿にコメントしました。", array(
											'controller' => 'homes',
											'action' => 'one/' . $val['Notice']['TML_ID'],
											'plugin' => null
										)) . "</td>";
									} elseif ($val['Notice']['ACT_ID'] == 2) {
										echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "の" . $val['Timeline']['NAME'] . "の投稿にコメントしました。", array(
											'controller' => 'homes',
											'action' => 'one/' . $val['Notice']['TML_ID'],
											'plugin' => null
										)) . "</td>";
									} else {
										echo $html->link($val['P_User']['NAME'] . "さんが" . $val['Group']['NAME'] . "に投稿しました。", array(
											'controller' => 'homes',
											'action' => 'one/' . $val['Notice']['TML_ID'],
											'plugin' => null
										)) . "</td>";
									}
									echo "</tr>";
								}
							}
						} else {
							echo "<tr><td style='border-bottom:0px;'>";
							echo $html->link("通知メッセージを表示する。", array(
								'controller' => 'homes',
								'action' => 'notice',
								'plugin' => null
							));
							echo "</td></tr>";
						}
						?>
					</table>
							</li>
				<?php
				if ((count($message)) > 0) {
					echo '<li class="subNewOn"><a href="javascript:ToggleNew()"><span>' . count($message) . '</span></a></li>';
				} else {
					echo '<li class="subNew"><a href="javascript:ToggleNew()"></a></li>';
				}
				?>
				<li id="subNewPopup" style="display: none;">
								<h4>新着メッセージ</h4>
								<table cellpadding="0" cellspacing="0" class='wordBreak'>
						<?php
						$c = 0;
						if (count($message) > 0) {
							foreach ($message as $key => $val) {
								if ($key < 5) {
									echo "<tr class= message_" . h($key) . ">";
									echo "<th>" . $html->image(array(
										'controller' => 'storages',
										'action' => 'thumbnail/' . $val['Message']['S_USR_ID'],
										'plugin' => null
									), array(
										'style' => 'width:40px;height:40px'
									)) . "</th>";
									echo "<td>" . $val['S_User']['NAME'] . "<br />\n" . $html->link($val['Message']['SUBJECT'], array(
										'controller' => 'messages',
										'action' => 'check/' . $val['Message']['MSG_ID'] . "/r",
										'plugin' => null
									)) . "</td>";
									echo "</tr>";
								}
								$c ++;
								if (count($message) == $c) {
									echo "<tr><td style='border-bottom:0px;text-align:center;' colspan='2'>";
									echo $html->link("メッセージを表示する。", array(
										'controller' => 'messages',
										'action' => 'index',
										'plugin' => null
									));
									echo "</td></tr>";
								}
							}
						} else {
							echo "<tr><td style='border-bottom:0px;'>";
							echo $html->link("メッセージを表示する。", array(
								'controller' => 'messages',
								'action' => 'index',
								'plugin' => null
							));
							echo "</td></tr>";
						}
						?>
					</table>
							</li>
				<?php
				if (count($request) > 0) {
					echo '<li class="subOkOn"><a href="javascript:ToggleOk()"><span  class="request_num">' . count($request) . '</span></a></li>';
				} else {
					echo '<li class="subOk"><a href="javascript:ToggleOk()"></a></li>';
				}
				?>
				<li id="subOkPopup" style="display: none;">
								<h4>承認メッセージ</h4>
								<table cellpadding="0" cellspacing="0" class='wordBreak'>
						<?php
						if (count($request) > 0) {
							foreach ($request as $key => $val) {
								echo "<tr class= 'request_" . h($key) . "'><td>" . $customJs->link(h($val['Request']['MESSAGE']), array(
									'controller' => 'requests',
									'action' => 'judge/' . $key . '/' . $val['Request']['REQ_ID'],
									'plugin' => null
								), array(
									'method' => 'POST',
									'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open();",
									'update' => 'null',
									'buffer' => false,
									'escape' => false
								)) . "</td></tr>";
							}
						} else {
							// echo "<tr><td style='border-bottom:0px;'>承認メッセージはありせん</td></tr>";
							echo "<tr><td style='border-bottom:0px;'>";
							// echo $html->link("承認メッセージを表示する。",array('controller'=>'messages','action'=>'index'));
							echo "</td></tr>";
						}
						?>
					</table>
							</li>
							<!-- InstanceEndEditable -->
				<?php
				if ($invite_conf['Configuration']['VALUE'] != 1 || $user["User"]["AUTHORITY"] == User::AUTHORITY_TRUE) {
					echo '<li class="subUser">';
					echo $customJs->link('ユーザを招待する', array(
						'controller' => 'users',
						'action' => 'invite',
						'plugin' => null
					), array(
						'method' => 'POST',
						'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open();",
						'update' => 'null',
						'buffer' => false
					));
					echo '</li>';
				}
				?>
				<?php
				echo "<li class='subSetup'>" . $customJs->link('設定', array(
					'controller' => 'users',
					'action' => 'edit/',
					'plugin' => null
				), array(
					'method' => 'GET',
					'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open();",
					'update' => 'null',
					'buffer' => false
				)) . "</li>";
				?>
				<?php
				echo ($user['User']['AUTHORITY'] == User::AUTHORITY_TRUE) ? "<li class='subSystem'>" . $html->link("システム管理", array(
					'controller' => 'administrators',
					'action' => 'index',
					'plugin' => null
				)) . "</li>" : '';
				?>
				<li class="subLogout"><?php echo $html->link("ログアウト", array('controller' => 'users', 'action' => 'logout', 'plugin' => null));?></li>
						</ul>
					</div>
					<ul id="headerNav" class="clearfix">
						<!-- InstanceBeginEditable name="nav" -->
						<li><?php
						echo $html->link('<span>タイムライン</span>', array(
							'controller' => 'homes',
							'action' => 'index',
							'plugin' => null
						), array(
							'class' => ($this->name == 'Home') ? 'navTimelineOn' : 'navTimeline',
							'escape' => false
						));
						?></li>
						<li><?php
						echo $html->link('<span>プロフィール</span>', array(
							'controller' => 'profiles',
							'action' => 'index',
							'plugin' => null
						), array(
							'class' => ($this->name == 'Profile') ? 'navProfileOn' : 'navProfile',
							'escape' => false
						));
						?></li>
						<li><?php
						echo $html->link('<span>ユーザ</span>', array(
							'controller' => 'friends',
							'action' => 'index',
							'plugin' => null
						), array(
							'class' => ($this->name == 'Friend') ? 'navUserOn' : 'navUser',
							'escape' => false
						));
						?></li>
						<li><?php
						echo $html->link('<span>グループ</span>', array(
							'controller' => 'groups',
							'action' => 'index',
							'plugin' => null
						), array(
							'class' => ($this->name == 'Group') ? 'navGroupOn' : 'navGroup',
							'escape' => false
						));
						?></li>
						<li><?php
						echo $html->link('<span>ファイル</span>', array(
							'controller' => 'storages',
							'action' => 'index',
							'plugin' => null
						), array(
							'class' => ($this->name == 'Storage') ? 'navFileOn' : 'navFile',
							'escape' => false
						));
						?></li>
						<li><?php
						echo $html->link('<span>ノート</span>', array(
							'controller' => 'note',
							'action' => 'index',
							'plugin' => 'note'
						), array(
							'class' => ($this->name == 'Note') ? 'navNoteOn' : 'navNote',
							'escape' => false
						));
						?></li>
						<li><?php
						echo $html->link('<span>メッセージ</span>', array(
							'controller' => 'messages',
							'action' => 'index',
							'plugin' => null
						), array(
							'class' => ($this->name == 'Message') ? 'navMessageOn' : 'navMessage',
							'escape' => false
						));
						?></li>
						<!-- InstanceEndEditable -->
					</ul>
				</div>
			</div>
		</div>
		<!-- header_End -->
		<div id="contents">
			<div id="contentsArea" class="clearfix">
			<?php
			echo $content_for_layout;
			?>
		</div>
		</div>
		<div id="popup-bg"></div>
		<div id="popup"></div>
	<?php
	echo $this->element('confirm');
	?>
<!-- footer_Start -->
		<div id="footer">
			<div id="footerArea">
				<ul id="footerNav" class="clearfix">
					<li><?php
					echo $html->link('タイムライン', array(
						'controller' => 'homes',
						'action' => 'index',
						'plugin' => null
					));
					?></li>
					<li><?php
					echo $html->link('プロフィール', array(
						'controller' => 'profiles',
						'action' => 'index',
						'plugin' => null
					));
					?></li>
					<li><?php
					echo $html->link('ユーザ', array(
						'controller' => 'friends',
						'action' => 'index',
						'plugin' => null
					));
					?></li>
					<li><?php
					echo $html->link('グループ', array(
						'controller' => 'groups',
						'action' => 'index',
						'plugin' => null
					));
					?></li>
					<li><?php
					echo $html->link('ファイル', array(
						'controller' => 'storages',
						'action' => 'index',
						'plugin' => null
					));
					?></li>
					<li><?php
					echo $html->link('ノート', array(
						'controller' => 'note',
						'action' => 'index',
						'plugin' => 'note'
					));
					?></li>
					<li><?php
					echo $html->link('メッセージ', array(
						'controller' => 'messages',
						'action' => 'index',
						'plugin' => null
					));
					?></li>
				</ul>
				<div id="copy">
					<p>
						一人ひとりにひらめきを<br />抹茶SNS <?php echo Configure::read('VERSION');?></p>
					<script type="text/javascript">copyright();</script>
				</div>
			</div>
		</div>
		<!-- footer_End -->
	</div>
	<!-- wrap_End -->
	<!-- InstanceBeginEditable name="jsBtm" -->
	<!-- <script type="text/javascript"> -->
	<!-- tab.setup = { -->
	<!-- tabs: document.getElementById('tab').getElementsByTagName('li'), -->
	<!-- pages: [ -->
	<!-- document.getElementById('page1'), -->
	<!-- ] -->
	<!-- } -->
	<!-- tab.init(); -->
	<!-- </script> -->
	<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->
</html>
