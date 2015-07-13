<!-- contents_Start -->
<div id="contentsArea" class="clearfix">
<?php
echo $html->css("setup", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<h2 class="mb20"><?php
echo h($title_text);
?></h2>

	<!-- InstanceBeginEditable name="contents" -->

	<div id="contentsCenter" class="mb60">
		<div id="contentsCenterTop">
			<div id="contentsCenterBtm">

				<div class="setupArea">
					<!-- page1_Start -->
					<div id="page1" class="tabBox">
						<div class="userEntryTable">
							<p>パスワード変更完了しました。</p>
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
