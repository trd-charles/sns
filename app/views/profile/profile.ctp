<!-- contents_Start -->
<script>
	initRollovers();
</script>
<div id="profileImg">
	<div style='position: relative;'>
		<p class="profileImgS">
	<?php
	$url = Router::url(array(
		'controller' => 'storages',
		'action' => 'thumbnail/' . $profile['User']['USR_ID'],
		"/ori"
	), true);
	echo "<a href='" . $url . "' class='gallery'>" . $html->image(array(
		'controller' => 'storages',
		'action' => 'thumbnail/' . $profile['User']['USR_ID'],
		"/pre"
	)) . "</a>";
	$script = "$('.gallery').colorbox({photo:'true'})";
	$js->buffer($script);
	echo $js->writeBuffer();
	?>
		<div
			style="position: absolute; top: 5px; left: 0px; width: 160px; display: none"
			class='change_image'></div>
		</p>
	</div>
<?php
if ($user['User']['USR_ID'] == $profile['User']['USR_ID']) {
	echo '<p class="mb10">';
	echo $customJs->link($html->image('profile/bt_profile.jpg', array(
		'alt' => 'プロフィールを変更',
		'class' => 'on'
	)), array(
		'controller' => 'profiles',
		'action' => 'edit'
	), array(
		'escape' => false,
		'div' => false,
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'method' => 'POST',
		'buffer' => false
	));
	echo '</p>';
	echo '<p>';
	echo $customJs->link($html->image("profile/bt_img.jpg", array(
		'alt' => '画像を変更',
		'class' => 'on'
	)), array(
		'controller' => 'profiles',
		'action' => 'image'
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
	echo "<span class='follow_" . h($profile['User']['USR_ID']) . "'>";
	
	if ($friend['Friend']['STATUS'] != 0) {
		echo $customJs->linkAfterConfirm($html->image('user/bt_follow_on.gif', array(
			'alt' => '＋フォロー中',
			'class' => 'on'
		)), array(
			'controller' => 'friends',
			'action' => 'follow',
			$friend['Friend']['F_USR_ID']
		), array(
			'escape' => false,
			'buffer' => false
		), array(
			'description' => 'フォロー解除しますか？',
			'type' => 'confirm'
		), array(
			'complete' => "function(data,textStatus,xhr) {follows(xhr," . $friend['Friend']['F_USR_ID'] . ", '0')}"
		));
	} else {
		echo $customJs->link($html->image('user/bt_follow.gif', array(
			'alt' => '＋フォローする'
		)), array(
			'controller' => 'friends',
			'action' => 'follow',
			$profile['User']['USR_ID']
		), array(
			'escape' => false,
			'method' => 'POST',
			'update' => null,
			'complete' => "follows(XMLHttpRequest," . $profile['User']['USR_ID'] . ", '0');",
			'buffer' => false
		));
	}
	echo "</span>";
	echo "<p class='mb10'></p>";
	echo $form->create('Message');
	echo $form->hidden('S_USR_NAME', array(
		'value' => $profile['User']['NAME'],
		'div' => false
	));
	echo $form->hidden('USR_ID', array(
		'value' => $profile['User']['USR_ID'],
		'div' => false
	));
	echo $js->submit('message/bt_message.gif', array(
		'url' => array(
			'controller' => 'messages',
			'action' => 'create'
		),
		'before' => "message_clear();",
		'update' => null,
		'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
		'style' => 'width:100px;'
	));
	echo $js->writeBuffer();
	echo $form->end();
}
?>
</div>
<div id="profileTxt">
	<table cellpadding="0" cellspacing="0" class='wordBreak'>
		<tr>
			<th>名前</th>
			<td colspan="3"><?php
			echo ($profile['User']['NAME']) ? $customHtml->ht2br($profile['User']['NAME']) : "&nbsp";
			?></td>
		</tr>
		<tr>
			<th>所属名</th>
			<td colspan="3"><?php
			echo ($profile['User']['UNIT']) ? $customHtml->ht2br($profile['User']['UNIT']) : "&nbsp";
			?></td>
		</tr>
		<tr>
			<th>郵便番号</th>
			<td colspan="3"><?php
			echo ($profile['User']['POSTCODE1'] && $profile['User']['POSTCODE2']) ? h($profile['User']['POSTCODE1']) . "-" . h($profile['User']['POSTCODE2']) : "&nbsp";
			?></td>
		</tr>
		<tr>
			<th>都道府県</th>
			<td colspan="3"><?php
			echo ($profile['User']['CNT_ID']) ? h($countys[$profile['User']['CNT_ID']]) : "&nbsp";
			?></td>
		</tr>
		<tr>
			<th>住所</th>
			<td colspan="3"><?php
			echo ($profile['User']['ADDRESS']) ? $customHtml->ht2br($profile['User']['ADDRESS']) : "&nbsp";
			?></td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td><?php
			echo ($profile['User']['PHONE_NO1'] && $profile['User']['PHONE_NO2'] && $profile['User']['PHONE_NO3']) ? h($profile['User']['PHONE_NO1']) . "-" . h($profile['User']['PHONE_NO2']) . "-" . h($profile['User']['PHONE_NO3']) : "&nbsp";
			?></td>
			<th>携帯番号</th>
			<td><?php
			echo ($profile['User']['M_PHONE_NO1'] && $profile['User']['M_PHONE_NO2'] && $profile['User']['M_PHONE_NO3']) ? h($profile['User']['M_PHONE_NO1']) . "-" . h($profile['User']['M_PHONE_NO2']) . "-" . h($profile['User']['M_PHONE_NO3']) : "&nbsp";
			?></td>
		</tr>
		<tr>
			<th>自己紹介</th>
			<td colspan="3"><?php
			echo ($profile['User']['DESCRIPTION']) ? $customHtml->text_cut($profile['User']['DESCRIPTION'], null, null, 50, true, false) : "&nbsp";
			?></td>
		
		
		<tr>
	
	</table>
</div>
<!-- contents_End -->