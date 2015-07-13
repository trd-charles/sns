<!-- contents_Start -->
<div id="profileImg">
	<div style='position: relative;'>
		<p class="profileImgS">
	<?php
	$url = Router::url(array(
		'controller' => 'storages',
		'action' => 'group_thumbnail/' . $group['Group']['GRP_ID'],
		"/ori"
	), true);
	echo "<a href='" . h($url) . "' class='gallery'>" . $html->image(array(
		'controller' => 'storages',
		'action' => 'group_thumbnail/' . $group['Group']['GRP_ID'],
		"/pre"
	)) . "</a>";
	$script = "$('.gallery').colorbox({photo:'true'})";
	$js->buffer($script);
	echo $js->writeBuffer();
	
	?>
	</p>
	</div>
<?php
if ($edit_auth) {
	echo '<p class="mb10">';
	echo $customJs->link($html->image('group/bt_group.jpg', array(
		'alt' => 'プロフィールを変更',
		'class' => 'on'
	)), array(
		'controller' => 'groups',
		'action' => 'edit/' . $group['Group']['GRP_ID']
	), array(
		'escape' => false,
		'div' => false,
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false
	));
	echo '</p>';
	echo '<p class="mb10">';
	echo $customJs->link($html->image("profile/bt_img.jpg", array(
		'alt' => '画像を変更',
		'class' => 'on'
	)), array(
		'controller' => 'groups',
		'action' => 'image/' . $group['Group']['GRP_ID']
	), array(
		'escape' => false,
		'div' => false,
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false
	));
	echo '</p>';
	echo '<p class="mb10">';
	echo $customJs->link($html->image("group/bt_invite.jpg", array(
		'alt' => '招待する',
		'class' => 'on'
	)), array(
		'controller' => 'groups',
		'action' => 'invite/' . $group['Group']['GRP_ID']
	), array(
		'escape' => false,
		'div' => false,
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false
	));
	echo '</p>';
} else {
	echo "<div class='join_" . h($group['Group']['GRP_ID']) . "'>";
	echo "<p class='ml5'>";
	if ($join['Join']['STATUS'] == 2) {
		$message = 'グループを脱退しますか？';
		
		echo $customJs->linkAfterConfirm($group_status[$join['Join']['STATUS']], array(
			'controller' => 'groups',
			'action' => 'join',
			$group['Group']['GRP_ID']
		), array(
			'buffer' => false
		), array(
			'description' => $message,
			'type' => 'confirm'
		), array(
			'complete' => "function(data, textStatus, xhr) {
					$('.join_" . $group['Group']['GRP_ID'] . "').html(data);
					window.location.href ='" . Router::url(array(
				'controller' => 'groups',
				'action' => 'index'
			), true) . "'
				}"
		));
	} else {
		$message = 'グループに参加しますか？';
		echo $customJs->linkAfterConfirm($group_status[3], array(
			'controller' => 'groups',
			'action' => 'join',
			$group['Group']['GRP_ID']
		), array(
			'buffer' => false
		), array(
			'description' => $message,
			'type' => 'confirm'
		), array(
			'complete' => "function(data, textStatus, xhr) {
					$('.join_" . $group['Group']['GRP_ID'] . "').html(data);
						window.location.reload();
				}"
		));
	}
	echo "<p></div>";
}
?>
</div>
<div id="profileTxt">
	<table cellpadding="0" cellspacing="0" class='wordBreak'>
		<tr>
			<th>グループ名</th>
			<td><?php
			if ($group['Group']['NAME'])
				echo $customHtml->ht2br($group['Group']['NAME']);
			?></td>
		</tr>
		<tr>
			<th>管理者名</th>
			<td><?php
			if ($group['User']['NAME'])
				echo $customHtml->ht2br($group_admin_name['User']['NAME']);
			?></td>
		</tr>
		<tr>
			<th>作成日</th>
			<td><?php
			if ($group['Group']['INSERT_DATE'])
				echo $customHtml->ht2br($group['Group']['INSERT_DATE']);
			?></td>
		</tr>
		<tr>
			<th>グループ概要</th>
			<td colspan="3"><?php
			echo ($group['Group']["DESCRIPTION"]) ? $customHtml->text_cut($group['Group']["DESCRIPTION"], null, null, 50, true, false) : "&nbsp";
			?></td>
		
		
		<tr>
	
	</table>
</div>
<!-- contents_End -->
