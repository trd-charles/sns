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
<title><?php
echo h($main_title);
?>｜抹茶SNS</title>
<!-- <link rel="shortcut icon" href="images/common/favicon.ico" /> -->
<link rel="shortcut icon"
	href="<?php
	echo $this->webroot;
	?>img/common/favicon.ico" />
<?php
echo $html->css("import", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<?php

echo $html->css("login", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<?php

echo $html->css("print", "stylesheet", array(
	'media' => 'print'
)) . "\n";
?>
<?php

echo $html->script("common") . "\n";
?>
</head>
<body>
	<!-- wrapper_Start -->
	<div id="wrap">
		<!-- header_Start -->
		<div id="header">
			<div id="headerArea" class="clearfix">
				<h1><?php
				echo $html->link($html->image('common/i_logo.jpg', array(
					'alt' => '抹茶SNS"'
				)), array(
					'controller' => 'users',
					'action' => 'login'
				), array(
					'escape' => false
				));
				?></h1>

				<!-- 	<p class="entryBtn"><a href="#"><img src="images/login/bt_entry.jpg" alt="新規登録はこちらから" class="on" /></a></p> -->
				<p class="entryBtn">
	<?php
	if ($this->action == 'login') {
		if ($general_conf["Configuration"]["VALUE"] != 1) {
			echo $html->link($html->image("login/bt_entry.jpg", array(
				'alt' => "新規登録はこちらから",
				'class' => 'on'
			)), array(
				'controller' => 'users',
				'action' => 'regist'
			), array(
				'escape' => false
			));
		}
	}
	?>

	</p>

			</div>
		</div>
		<!-- header_End -->
		<div id="contents">
			<?php
			echo $content_for_layout;
			?>
    </div>
		<!-- footer_Start -->
		<div id="footerLogin">
			<div id="footerArea">
				<div id="copy">
					<p>
						一人ひとりにひらめきを<br />抹茶SNS <?php
						echo Configure::read('VERSION');
						?> </p>
					<script type="text/javascript">copyright();</script>
				</div>
			</div>
		</div>
		<!-- footer_End -->
	</div>
	<!-- wrapper_End -->
</body>
</html>