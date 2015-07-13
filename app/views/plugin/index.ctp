<?php
// 完了メッセージ
echo $session->flash();
?>
<!-- contents_Start -->
<?php
echo $html->css(array(
	"file",
	"setup"
), "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<!-- InstanceBeginEditable name="contents" -->
<h2 class="mb20">プラグイン管理</h2>
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
	<li class="present"><?php
	echo $html->link('<span>プラグイン管理</span>', array(
		'controller' => 'plugins'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>ログ削除</span>', array(
		'controller' => 'administrators',
		'action' => 'delete_log'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
	<li><?php
	echo $html->link('<span>アクセス制限</span>', array(
		'controller' => 'configurations',
		'action' => 'access'
	), array(
		'escape' => false,
		'div' => false
	));
	?>
	</li>
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
				<div style="line-height: 20px">
					<div
						style="float: left; width: 90px; margin-left: 30px; margin-bottom: 10px">
				<?php echo $customJs->link($html->image('file/bt_upload_2.gif',array('alt'=>'ファイルをアップロードする')), array('controller' => 'plugins', 'action' => 'add'),array('id'=>'file_up', 'update'=>null,'complete'=>"popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",'method'=>'POST','buffer'=>false,'escape'=>false,'div'=>false)); ?>
			</div>

					<table cellpadding="0" cellspacing="0" class="fileTable wordBreak"
						style="width: 800px; border-top: 0px">
						<tr>
							<th class="thDownload" style="width: 160px">プラグイン名</th>
							<th class="thFile" style="width: 400px">説明</th>
							<th class="thType" style="width: 160px">作成者</th>
							<th class="thSize" style="width: 80px">利用状態</th>
						</tr>
				<?php
				if ($index_list) {
					foreach ($index_list as $key => $val) {
						echo "<tr  class='odd'>";
						echo "<td>";
						echo h($val['Plugin']['DISP_NAME']);
						echo "</td>";
						echo "<td>";
						echo $val['Plugin']['DESCRIPTION']; // システム管理者しか入力しないのでhで囲っていません。囲うと<br>が出力されます。
						echo "</td>";
						echo "<td>";
						if (! empty($val['Plugin']['AUTHOR'])) {
							if (! empty($val['Plugin']['URL'])) {
								echo $html->link(h($val['Plugin']['AUTHOR']), $val['Plugin']['URL'], array(
									'escape' => false,
									'target' => '_blank'
								));
							} else {
								echo h($val['Plugin']['AUTHOR']);
							}
						} else {
							if (! empty($val['Plugin']['URL'])) {
								echo $html->link(h($val['Plugin']['URL']), $val['Plugin']['URL'], array(
									'escape' => false,
									'target' => '_blank'
								));
							}
						}
						echo "</td>";
						echo "<td>";
						if ($val['Plugin']['NOT_STOP'] == 1) {
							echo '<p style="color:#FF0033;">利用中</p>';
						} elseif ($val['Plugin']['STATUS'] == 1) {
							echo $customJs->linkAfterConfirm('停止する', array(
								'controller' => 'plugins',
								'action' => 'install',
								$val['Plugin']['PLU_ID']
							), array(
								'escape' => false,
								'buffer' => false
							), array(
								'description' => '利用停止してもよろしいですか？',
								'type' => 'confirm',
								'close' => false
							), array(
								'complete' => "function() {window.location.href='" . Router::url(array(
									'controller' => 'plugins',
									'action' => 'index'
								)) . "'} "
							));
						} else {
							echo $customJs->linkAfterConfirm('使用する', array(
								'controller' => 'plugins',
								'action' => 'install',
								$val['Plugin']['PLU_ID']
							), array(
								'escape' => false,
								'buffer' => false
							), array(
								'description' => '利用開始してもよろしいですか？',
								'type' => 'confirm',
								'close' => false
							), array(
								'complete' => "function() {window.location.href='" . Router::url(array(
									'controller' => 'plugins',
									'action' => 'index'
								)) . "'} "
							));
						}
						echo "</td>";
						echo "</tr>";
					}
				}
				?>
			</table>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- InstanceEndEditable -->
<!-- contents_End -->
