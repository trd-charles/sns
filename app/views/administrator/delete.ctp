<?php
// 完了メッセージ
echo $session->flash();
?>

<!-- header_End -->
<script>
window.onload = user_opn;
function user_opn(){
	if($('.check_com:checked').val()==1){
		if($('.check_file:checked').val()==1){
			$('.check_user_tr').show();
		}else{
			$('.check_user_tr').hide();
			$('.check_user_val').val(['0']);
		}
	}else{
		$('.check_user_val').val(['0']);
		$('.check_user_tr').hide();
	}
}
</script>
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

$eventCode = <<<EOF
	if($('.check_com:checked').val()==1){
		if($('.check_file:checked').val()==1){
			$('.check_user_tr').show();
			$('.check_user_val').val(['0']);
		}else{
			$('.check_user_tr').hide();
			$('.check_user_val').val(['0']);
		}
	}else{
		$('.check_user_tr').hide();
		$('.check_user_val').val(['0']);
	}
EOF;
$js->get('.check_com')->event('change', $eventCode);
$js->get('.check_file')->event('change', $eventCode);
echo $js->writeBuffer();
?>
<!-- InstanceBeginEditable name="contents" -->
<h2 class="mb20">ユーザ削除</h2>
<div id="contentsCenter" class="mb60">
	<div id="contentsCenterTop">
		<div id="contentsCenterBtm">
			<div class="setupArea">
				<div class="userEntryTable">
		<?php
		if ($index_list) {
			?>
			<p class='mb20' style='color: red'>
						以下のグループの管理者のなっているためユーザデータは削除できません。<br />
						削除する場合は、グループに遷移し、グループを削除するか、管理者を譲渡した後もう一度削除して下さい。
					</p>
					<table>
			<?php
			foreach ($index_list as $key => $val) {
				echo "<tr>";
				echo '<th style="width:80px;">グループ名</th>';
				echo '<td>' . $html->link($val['Group']['NAME'], array(
					'controller' => 'groups',
					'action' => 'main/' . $val['Group']['GRP_ID']
				)) . '</td>';
			}
			?>
			</table>
			<?php
			$form->end();
			?>
		<?php }else{?>
			<p class='mb20' style='color: red'>項目にチェックを入れ削除してください。</p>
		<?php
			echo $form->create('Administrator', array(
				'type' => 'post',
				'action' => 'delete/' . $id,
				'name' => 'AdministratordeleteForm'
			))?>
			<table>
						<tr>
							<th style="width: 80px; padding-left: 50px">コメント</th>
							<td style="width: 180px;"><?php
			echo $form->radio('COMMENT', array(
				0 => '論理削除',
				1 => '物理削除'
			), array(
				'class' => 'check_com',
				'legend' => false,
				'label' => false,
				'value' => 0,
				'div' => false,
				'style' => 'width:30px'
			));
			?></td>
							<td>論理削除の場合、タイムライン上のコメントは「このコメントは削除されました。」と表示されるようになります。<br />
								物理削除の場合はデータベース上から完全に削除され、タイムラインには表示されなくなります。
							</td>
						</tr>
						<tr>
							<th style="width: 80px; padding-left: 50px">ファイル</th>
							<td style="width: 180px;"><?php
			echo $form->radio('FILE', array(
				0 => '論理削除',
				1 => '物理削除'
			), array(
				'class' => 'check_file',
				'legend' => false,
				'label' => false,
				'value' => 0,
				'div' => false,
				'style' => 'width:30px'
			));
			?></td>
							<td>論理削除の場合、サーバー上にファイルは残ったままですが、管理者以外のユーザはファイル一覧からファイルを参照できなくなります。<br />
								物理削除の場合はサーバー上からファイルが完全に削除されます。
							</td>
						</tr>
						<tr class='check_user_tr'>
							<th style="width: 80px; padding-left: 50px">ユーザデータ</th>
							<td style="width: 180px;"><?php
			echo $form->radio('Users', array(
				0 => '削除しない',
				1 => '削除する'
			), array(
				'class' => 'check_user_val',
				'legend' => false,
				'label' => false,
				'value' => 0,
				'div' => false,
				'style' => 'width:30px'
			));
			?></td>
							<td>「削除する」を選択すると、ユーザデータはデータベース上から完全に削除されます。</td>
						</tr>
					</table>
			<?php
			echo $customHtml->hiddenToken();
			?>
			<?php
			echo $js->submit('common/delete_log.jpg');
			?>
			<?php $form->end();?>
		<?php }?>
		</div>
			</div>
		</div>
	</div>
</div>
<!-- InstanceEndEditable -->
<!-- contents_End -->

<!-- InstanceBeginEditable name="jsBtm" -->

<!-- InstanceEndEditable -->
<!-- contents_End -->