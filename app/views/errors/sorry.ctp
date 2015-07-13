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
<title>IP｜抹茶SNS IP,ホスト禁止制限</title>
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

echo $html->css("colorbox.css", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<?php

echo $html->css("setup", "stylesheet", array(
	'media' => 'screen'
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
			</div>
		</div>

		<div id="contents">
			<!-- contents_Start -->
			<div id="contentsArea" class="clearfix">
				<!-- <h2 class="mb20" style="color:#FF0033;">このサイトは表示できません</h2>-->
				<!-- <h2 class="mb20">このサイトは表示できません</h2> -->
				<h2 class="mb20"><?php
				echo h($title);
				?></h2>
				<div id="contentsCenter" class="mb60">
					<div id="contentsCenterTop">
						<div id="contentsCenterBtm">
							<div class="setupArea">
								<!-- 	<h1 style="font-size:15px; color:#FF0033;">アクセス制限により閲覧がブロックされました</h1><br/> -->
								<h1 style="font-size: 15px; color: #FF0033;"><?php
								echo h($message);
								?></h1>
								<br />

							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- contents_End -->
		</div>

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