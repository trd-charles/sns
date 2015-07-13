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

echo $form->create('Administrator', array(
	'type' => 'post',
	'action' => 'delete_log',
	'name' => 'AdministratorDelLogForm'
))?>
<!-- InstanceBeginEditable name="contents" -->
<h2 class="mb20">ログ削除</h2>
<ul id="tab" style="width: 960px;">
	<li><?php
	echo $html->link('<span>ユーザ管理</span>', array(
		'controller' => 'administrators',
		'action' => 'index'
	), array(
		'escape' => false,
		'div' => false
	));
	?></li>
	<li><?php
	echo $html->link('<span>システム設定</span>', array(
		'controller' => 'configurations',
		'action' => 'index'
	), array(
		'escape' => false,
		'div' => false
	));
	?></li>
	<li><?php
	echo $html->link('<span>プラグイン管理</span>', array(
		'controller' => 'plugins'
	), array(
		'escape' => false,
		'div' => false
	));
	?></li>
	<li class="present"><?php
	echo $html->link('<span>ログ削除</span>', array(
		'controller' => 'administrators',
		'action' => 'delete_log'
	), array(
		'escape' => false,
		'div' => false
	));
	?></li>
	<li><?php
	echo $html->link('<span>アクセス制限</span>', array(
		'controller' => 'configurations',
		'action' => 'access'
	), array(
		'escape' => false,
		'div' => false
	));
	?></li>
	<li><?php
	echo $html->link('<span>メール設定</span>', array(
		'controller' => 'configurations',
		'action' => 'mail'
	), array(
		'escape' => false,
		'div' => false
	));
	?></li>
</ul>
<div id="contentsCenter" class="mb60">
	<div id="contentsCenterTop">
		<div id="contentsCenterBtm">
			<div class="setupArea">
				<!-- page1_Start -->
				<div id="page1" class="tabBox">
					<div class="tabBoxIndexAreaS">
						<div>
							<!-- page1_Start -->
							<table cellpadding="0" cellspacing="0" class="userEntryTable">
								<tr>
									<th>発言数</th>
									<td><?php
									echo h($postCount);
									?>件</td>
								</tr>
								<tr>
									<th>削除期間<span class='required'>*</span></th>
									<td class="inputArea"><?php
									echo $form->year('Span', date('Y') - 5, date('Y'), date('Y')) . ' / ' . $form->month('Span', date('m'), array(
										'monthNames' => false
									)) . ' / ' . $form->day('Span', date('d'));
									?>
											以前のデータ
											<?php
											echo $form->error('DEL_SPAN', array(
												'class' => 'errors'
											));
											?></td>
								</tr>
								<tr>
									<th>削除対象<span class='required'>*</span></th>
									<td class="inputArea">
												<?php
												echo $form->checkbox('DEL_POST', array(
													'div' => false,
													'style' => 'width: 30px',
													'checked' => 'checked'
												));
												?>発言　
												<?php
												echo $form->checkbox('DEL_FILE', array(
													'div' => false,
													'style' => 'width: 30px',
													'checked' => 'checked'
												));
												?>ファイル
												(紐づいているコメントも同時に削除されます)
												<?php
												echo $form->error('DEL_POST', array(
													'class' => 'errors'
												));
												?>
											</td>
								</tr>

							</table>
							<p class="makeBtn">
									<?php
									echo $customHtml->hiddenToken();
									?>
									<?php
									echo $customJs->submitAfterConfirm('common/delete_log.jpg', array(
										'buffer' => true,
										'div' => false,
										'style' => 'margin-top:0px;float:left;'
									), array(
										'description' => '削除してよろしいですか？',
										'type' => 'confirm',
										'close' => true
									), array(
										'url' => array(
											'controller' => 'administrators',
											'action' => 'delete_log',
											'plugin' => null
										),
										'complete' => "function(data, textStatus, xhr) {if(data) window.location.href = '" . Router::url(array(
											'controller' => 'administrators',
											'action' => 'delete_log'
										)) . "'} "
									));
									?>
									</p>
						</div>
					</div>
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
