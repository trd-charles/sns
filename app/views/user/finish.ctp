<!-- contents_Start -->
<div id="contentsArea" class="clearfix">
<?php
echo $html->css("setup", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<?php

echo $html->script("tab") . "\n";
?>
<h2 class="mb20">登録完了</h2>

	<!-- InstanceBeginEditable name="contents" -->

	<div id="contentsCenter" class="mb60">
		<div id="contentsCenterTop">
			<div id="contentsCenterBtm">

				<div class="setupArea">
					<!-- page1_Start -->
					<div id="page1" class="tabBox">
						<div class="userEntryTable">
							<p>
						<?php
						if (isset($stat) && $stat == 1) {
							echo "抹茶SNSへの申請が完了しました。<br />\n
									管理者からの認証の後、ログインページから始めることができます。";
						} else {
							echo "抹茶SNSへの登録が完了しました。<br />\n
									ログインページから始めることができます。";
						}
						?></p>
						</div>
					</div>

					<br />
		<?php
		echo $html->link('トップヘ戻る', array(
			'controller' => 'users',
			'action' => 'login'
		), array(
			'style' => 'padding-top:40px;'
		));
		?>
		</div>
			</div>
		</div>
	</div>

</div>


