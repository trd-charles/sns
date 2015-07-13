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
	'type' => 'get',
	'action' => 'access',
	'name' => 'AdministratorAddForm'
))?>
<!-- InstanceBeginEditable name="contents" -->
<h2 class="mb20">アクセス制限</h2>

<ul id="tab"
	style="border-bottom: 5px solid #3C6937; margin-bottom: 0px; width: 960px">
	<li><?php
	echo $html->link('<span>ユーザ管理</span>', array(
		'controller' => 'administrators',
		'action' => 'index'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>システム設定</span>', array(
		'controller' => 'configurations',
		'action' => 'index'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>プラグイン管理</span>', array(
		'controller' => 'plugins',
		'action' => 'index',
		'plugin' => null
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>ログ削除</span>', array(
		'controller' => 'administrators',
		'action' => 'delete_log',
		'plugin' => null
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li class="present"><a href="javascript:void(0);"
		data-tor-smoothScroll="noSmooth"><span>アクセス制限</span> </a></li>
	<li><?php
	echo $html->link('<span>メール設定</span>', array(
		'controller' => 'configurations',
		'action' => 'mail'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
</ul>

<div id="contentsCenter" class="mb60">
	<div id="contentsCenterTop">
		<div id="contentsCenterBtm">
			<div class="setupArea">
				<div class="userEntryTable">
					<table>
						<tr>
							<th>メンテナンスモード</th>
							<td><?php
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
							<th>IPホスト制限</th>
							<td><?php
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
						<tr>
							<th></th>
							<td>
							<?php
							echo $customHtml->hiddenToken();
							?>
							<?php
							echo "<p class='delete'>";
							?>
							<?php
							echo $customJs->submitAfterConfirm('profile/bt_save.gif', array(
								'buffer' => true
							), array(
								'description' => '設定を変更しますか？',
								'type' => 'confirm',
								'close' => true
							), array(
								'url' => array(
									'controller' => 'configurations',
									'action' => 'access',
									'plugin' => null
								),
								'complete' => "function(data, textStatus, xhr) {if(data) window.location.href = '" . Router::url(array(
									'controller' => 'configurations',
									'action' => 'access'
								)) . "'} "
							));
							?>
							<?php
							echo "</p>";
							?>
							</td>
						</tr>
					</table>
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
