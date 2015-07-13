<!-- contents_Start -->
<?php
if (isset($err_comment)) {
	echo "<span class=\"search_result\">「" . $customHtml->ht2br($keyword) . " 」の検索結果　　</span>";
	echo "<span class=\"match_result errors\">" . h($err_comment) . "";
	echo "</span>";
} else {
	if (isset($keyword) && isset($match_post) && isset($match_comment)) {
		echo "<span class=\"search_result\">「" . $customHtml->ht2br($keyword) . " 」の検索結果　　</span>";
		echo "<span class=\"match_result\">投稿 : " . h($match_post) . "件　";
		echo "コメント : " . h($match_comment) . "件　";
		echo "</span>";
	}
}

// 検索条件が入力されていない場合、$keyword_flgに「1」を格納
if (isset($keyword)) {
	$keyword_flg = '1';
}

mb_regex_encoding('UTF-8');

if (isset($list)) {
	foreach ($list as $key => $val) {
		echo "<div class='timeline_" . h($val['Timeline']['TML_ID']) . " wordBreak'>";
		echo "<dt class='clearfix'>";
		echo "<p class='sendUser'>" . $html->link($html->image(array(
			'controller' => 'storages',
			'action' => 'thumbnail/' . $val['User']['USR_ID']
		), array(
			'style' => 'width:60px;height:60px'
		)), array(
			'controller' => 'profiles',
			'action' => 'index/' . $val['User']['USR_ID']
		), array(
			'escape' => false
		)) . "</p>";
		if (($val['Timeline']['ACT_ID'] == 5 || $val['Timeline']['ACT_ID'] == 4)) {
			echo "<div class='sendMid'><div class='sendTop'><div class='sendBtm file clearfix'>";
			if ($val['Timeline']['ACT_ID'] == 5) {
				// echo "<p class='UserName'>".$html->link(nl2br($val["User"]["NAME"]),array('controller'=>'profiles','action'=>'index/'.$val['User']['USR_ID']))."->".$html->link($val["Group"]["NAME"],array('controller'=>'groups','action'=>'main/'.$val['Group']['GRP_ID']))."</p>";
				echo "<p class='UserName'>" . $html->image('group/group_icon.gif', array(
					'style' => 'margin-bottom:3px;margin-right:10px'
				)) . $html->link($val["Group"]["NAME"], array(
					'controller' => 'groups',
					'action' => 'main/' . $val['Group']['GRP_ID']
				), array(
					'style' => 'font-weight:bold'
				)) . ' ‐ ' . $html->link(nl2br($val["User"]["NAME"]), array(
					'controller' => 'profiles',
					'action' => 'index/' . $val['User']['USR_ID']
				)) . "</p>";
			} else {
				echo "<p class='UserName'>" . $html->link(nl2br($val["User"]["NAME"]), array(
					'controller' => 'profiles',
					'action' => 'index/' . $val['User']['USR_ID']
				)) . "</p>";
			}
			echo "<div>";
			echo "<p class='txtMain'>" . $customHtml->text_cut($val['Timeline']['MESSAGE'], $val['Timeline']['TML_ID'], $val['Timeline']['TML_ID'], null, true, false, $keyword) . "<p>";
			if (preg_match('/image/', $val['Storage']['F_TYPE']) && ($val['Storage']['EXTENSION'] != 'bmp')) {
				
				if (file_exists(APP . "files/user/" . $val['User']['DIRECTORY1'] . "/" . $val['User']['DIRECTORY2'] . "/storage/" . $val['Storage']['RAND_NAME'])) {
					$url = Router::url(array(
						'controller' => 'storages',
						'action' => 'preview/' . $val['Storage']['RAND_NAME']
					), true);
					echo "<p class='fileImg'>";
					echo "<a href='" . h($url) . "' class='gallery_" . h($val['Timeline']['TML_ID']) . "'>" . $html->image(array(
						'controller' => 'storages',
						'action' => 'preview/' . h($val['Storage']['RAND_NAME']) . "/thumb"
					)) . "</a>";
					echo "</p>";
					$script = "$('.gallery_" . h($val['Timeline']['TML_ID']) . "').colorbox({photo:'true'})";
					$js->buffer($script);
					echo $js->writeBuffer();
				} else {
					echo $this->element("timeline/img_link_download", array(
						'val' => $val
					));
				}
			} else {
				echo $this->element("timeline/img_link_download", array(
					'val' => $val
				));
			}
			echo "</div>";
			echo "<div class='fileTxt'>";
			if (! file_exists(APP . "files/user/" . $val['User']['DIRECTORY1'] . "/" . $val['User']['DIRECTORY2'] . "/storage/" . $val['Storage']['RAND_NAME'])) {
				echo "<p class='txtRedNotice'>ファイルは削除されました</p>";
			} else {
				echo "<p>" . h($val['User']['NAME']) . "さんがファイルを投稿しました<br />" . h($val['Storage']['ORIGINAL_NAME']) . "</p>";
			}
			echo "</div>";
			echo "<div class='txtSub clearfix'>";
			echo "<p>" . $html->link($val["Timeline"]["INSERT_DATE"], array(
				'controller' => 'homes',
				'action' => 'one/' . $val['Timeline']['TML_ID']
			)) . "</p>";
			echo "<ul>";
			echo "<li class='watchBtn watch_" . h($val['Timeline']['TML_ID']) . "'>";
			if ($val['Watch']['MINE']) {
				if (! isset($watch)) {
					echo $customJs->link('ウォッチリストから取り消す', array(
						'controller' => 'timelines',
						'action' => 'watch/' . $val['Timeline']['TML_ID']
					), array(
						'method' => 'POST',
						'update' => null,
						'buffer' => false,
						'complete' => "watch(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")"
					));
				} else {
					echo $customJs->link('ウォッチリストから取り消す', array(
						'controller' => 'timelines',
						'action' => 'watch/' . $val['Timeline']['TML_ID']
					), array(
						'method' => 'POST',
						'update' => null,
						'buffer' => false,
						'complete' => "watch(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ",1)"
					));
				}
			} else {
				echo $customJs->link('ウォッチリストに追加', array(
					'controller' => 'timelines',
					'action' => 'watch/' . $val['Timeline']['TML_ID']
				), array(
					'method' => 'POST',
					'complete' => "watch(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")",
					'update' => null,
					'buffer' => false
				));
			}
			echo "</li>";
			echo "<li class='readBtn read_" . h($val['Timeline']['TML_ID']) . "'>";
			if ($val['READ']['MINE']) {
				echo $customJs->link('読んだ!を取り消す', array(
					'controller' => 'timelines',
					'action' => 'read/' . $val['Timeline']['TML_ID']
				), array(
					'div' => false,
					'method' => 'POST',
					'update' => null,
					'buffer' => false,
					'complete' => "read(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")"
				));
			} else {
				echo $customJs->link('読んだ!', array(
					'controller' => 'timelines',
					'action' => 'read/' . $val['Timeline']['TML_ID']
				), array(
					'div' => false,
					'method' => 'POST',
					'complete' => "read(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")",
					'update' => null,
					'buffer' => false
				));
			}
			
			if ($val['READ']['Count'] > 0 || $val['READ']['MINE']) {
				echo "　";
				if ($val['READ']['MINE']) {
					echo $customJs->link(($val['READ']['Count'] + 1) . '人', array(
						'controller' => 'timelines',
						'action' => 'read_user/' . $val['Timeline']['TML_ID']
					), array(
						'div' => false,
						'update' => null,
						'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
						'method' => 'POST',
						'buffer' => false
					));
				} else {
					echo $customJs->link(($val['READ']['Count']) . '人', array(
						'controller' => 'timelines',
						'action' => 'read_user/' . $val['Timeline']['TML_ID']
					), array(
						'div' => false,
						'update' => null,
						'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
						'method' => 'POST',
						'buffer' => false
					));
				}
			}
			echo "</li>";
			echo "</ul>";
			echo "</div>";
			echo "</div></div></div>";
		} else {
			echo "<div class='sendMid'><div class='sendTop'><div class='sendBtm txt clearfix'>";
			if ($val['Timeline']['ACT_ID'] == 3) {
				// echo "<p class='UserName'>".$html->link($val["User"]["NAME"],array('controller'=>'profiles','action'=>'index/'.$val['User']['USR_ID']))."->".$html->link($val["Group"]["NAME"],array('controller'=>'groups','action'=>'main/'.$val['Group']['GRP_ID']))."</p>";
				echo "<p class='UserName'>" . $html->image('group/group_icon.gif', array(
					'style' => 'margin-bottom:3px;margin-right:10px'
				)) . $html->link($val["Group"]["NAME"], array(
					'controller' => 'groups',
					'action' => 'main/' . $val['Group']['GRP_ID']
				), array(
					'style' => 'font-weight:bold'
				)) . ' ‐ ' . $html->link(nl2br($val["User"]["NAME"]), array(
					'controller' => 'profiles',
					'action' => 'index/' . $val['User']['USR_ID']
				)) . "</p>";
			} else {
				echo "<p class='UserName'>" . $html->link($val["User"]["NAME"], array(
					'controller' => 'profiles',
					'action' => 'index/' . $val['User']['USR_ID']
				)) . "</p>";
			}
			echo "<p class='txtMain'>" . $customHtml->text_cut($val['Timeline']['MESSAGE'], $val['Timeline']['TML_ID'], $val['Timeline']['TML_ID'], null, true, false, $keyword) . "<p>";
			echo "<div class='txtSub clearfix'>";
			echo "<p>" . $html->link($val["Timeline"]["INSERT_DATE"], array(
				'controller' => 'homes',
				'action' => 'one/' . $val['Timeline']['TML_ID']
			)) . "</p>";
			echo "<ul>";
			echo "<li class='watchBtn watch_" . h($val['Timeline']['TML_ID']) . "'>";
			if ($val['Watch']['MINE']) {
				if (! isset($watch)) {
					echo $customJs->link('ウォッチリストから取り消す', array(
						'controller' => 'timelines',
						'action' => 'watch/' . $val['Timeline']['TML_ID']
					), array(
						'method' => 'POST',
						'update' => null,
						'buffer' => false,
						'complete' => "watch(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")"
					));
				} else {
					echo $customJs->link('ウォッチリストから取り消す', array(
						'controller' => 'timelines',
						'action' => 'watch/' . $val['Timeline']['TML_ID']
					), array(
						'method' => 'POST',
						'update' => null,
						'buffer' => false,
						'complete' => "watch(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ",1)"
					));
				}
			} else {
				echo $customJs->link('ウォッチリストに追加', array(
					'controller' => 'timelines',
					'action' => 'watch/' . $val['Timeline']['TML_ID']
				), array(
					'method' => 'POST',
					'update' => null,
					'buffer' => false,
					'complete' => "watch(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")"
				));
			}
			echo "</li>";
			echo "<li class='readBtn read_" . h($val['Timeline']['TML_ID']) . "'>";
			if ($val['READ']['MINE']) {
				echo $customJs->link('読んだ!を取り消す', array(
					'controller' => 'timelines',
					'action' => 'read/' . $val['Timeline']['TML_ID']
				), array(
					'div' => false,
					'method' => 'POST',
					'update' => null,
					'buffer' => false,
					'complete' => "read(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")"
				));
			} else {
				echo $customJs->link('読んだ!', array(
					'controller' => 'timelines',
					'action' => 'read/' . $val['Timeline']['TML_ID']
				), array(
					'div' => false,
					'method' => 'POST',
					'complete' => "read(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")",
					'update' => null,
					'buffer' => false
				));
			}
			
			if ($val['READ']['Count'] > 0 || $val['READ']['MINE']) {
				echo "　";
				if ($val['READ']['MINE']) {
					echo $customJs->link(($val['READ']['Count'] + 1) . '人', array(
						'controller' => 'timelines',
						'action' => 'read_user/' . $val['Timeline']['TML_ID']
					), array(
						'div' => false,
						'update' => null,
						'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
						'method' => 'POST',
						'buffer' => false
					));
				} else {
					echo $customJs->link(($val['READ']['Count']) . '人', array(
						'controller' => 'timelines',
						'action' => 'read_user/' . $val['Timeline']['TML_ID']
					), array(
						'div' => false,
						'update' => null,
						'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
						'method' => 'POST',
						'buffer' => false
					));
				}
			}
			echo "</li>";
			echo "</ul>";
			echo "</div>";
			echo "</div></div></div>";
		}
		if ($val['Timeline']['USR_ID'] == $user['User']['USR_ID'] || $user['User']['AUTHORITY'] == User::AUTHORITY_TRUE) {
			echo "<p class='delete'>";
			echo $customJs->linkAfterConfirm($html->image('timeline/bt_delete.gif'), array(
				'controller' => 'timelines',
				'action' => 'delete/' . $val['Timeline']['TML_ID']
			), array(
				'escape' => false,
				'buffer' => false,
				'id' => 'delete_' . $val['Timeline']['TML_ID']
			), array(
				'description' => '削除してよろしいですか？',
				'type' => 'confirm',
				'close' => false
			), array(
				'complete' => "function(data,textStatus,xhr){timeline_delete(xhr)}"
			));
			echo "</p>";
		} elseif ($val['Timeline']['ACT_ID'] == '3' || $val['Timeline']['ACT_ID'] == '5') {
			if ($val['Group']['USR_ID'] == $user['User']['USR_ID']) {
				echo "<p class='delete'>";
				echo $customJs->linkAfterConfirm($html->image('timeline/bt_delete.gif'), array(
					'controller' => 'timelines',
					'action' => 'delete/' . $val['Timeline']['TML_ID']
				), array(
					'escape' => false,
					'buffer' => false,
					'id' => 'delete_' . $val['Timeline']['TML_ID']
				), array(
					'description' => '削除してよろしいですか？',
					'type' => 'confirm',
					'close' => false
				), array(
					'complete' => "function(data,textStatus,xhr){timeline_delete(xhr)}"
				));
				echo "</p>";
			}
		}
		echo "</dt>";
		if (isset($val['Timeline']['COMMENT'])) {
			$count = count($val['Timeline']['COMMENT']);
			$first = 0;
			if ($count > 2) {
				$first = $count - 2;
			}
			foreach ($val['Timeline']['COMMENT'] as $k => $v) {
				if ($v['Timeline']['DEL_FLG'] != 1) {
					echo "<dd class='timeline_" . h($v['Timeline']['TML_ID']) . " comment_area'>";
					echo '<p class="repUser">' . $html->link($html->image(array(
						'controller' => 'storages',
						'action' => 'thumbnail/' . h($v['User']['USR_ID'])
					), array(
						'style' => 'width:40px;height:40px'
					)), array(
						'controller' => 'profiles',
						'action' => 'index/' . h($v['User']['USR_ID'])
					), array(
						'escape' => false
					)) . '</p>';
					echo '<div class="repMid"><div class="repTop"><div class="repBtm clearfix">';
					echo "<p class='UserName_rep'>" . $html->link($v["User"]["NAME"], array(
						'controller' => 'profiles',
						'action' => 'index/' . $v['User']['USR_ID']
					)) . "</p>";
					echo '<p class="txtMain">' . $customHtml->text_cut($v['Timeline']['MESSAGE'], $v['Timeline']['TML_ID'], $val['Timeline']['TML_ID'], null, true, false, $keyword) . '</p>';
					echo '<div class="txtSub clearfix">';
					echo "<p>" . $html->link($v["Timeline"]["INSERT_DATE"], array(
						'controller' => 'homes',
						'action' => 'one/' . $val['Timeline']['TML_ID']
					)) . "</p>";
					echo '<ul>';
					echo "<li class='readBtn read_" . h($v['Timeline']['TML_ID']) . "'>";
					if ($v['READ']['MINE']) {
						echo $customJs->link('読んだ！を取り消す', array(
							'controller' => 'timelines',
							'action' => 'read/' . $v['Timeline']['TML_ID']
						), array(
							'div' => false,
							'method' => 'POST',
							'update' => null,
							'buffer' => false,
							'complete' => "read(XMLHttpRequest," . $v['Timeline']['TML_ID'] . ")"
						));
					} else {
						echo $customJs->link('読んだ！', array(
							'controller' => 'timelines',
							'action' => 'read/' . $v['Timeline']['TML_ID']
						), array(
							'div' => false,
							'method' => 'POST',
							'complete' => "read(XMLHttpRequest," . $v['Timeline']['TML_ID'] . ")",
							'update' => null,
							'buffer' => false
						));
					}
					if ($v['READ']['Count'] > 0 || $v['READ']['MINE']) {
						echo "　";
						if ($v['READ']['MINE']) {
							echo $customJs->link(($v['READ']['Count'] + 1) . '人', array(
								'controller' => 'timelines',
								'action' => 'read_user/' . $v['Timeline']['TML_ID']
							), array(
								'div' => false,
								'update' => null,
								'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
								'method' => 'POST',
								'buffer' => false
							));
						} elseif ($v['READ']['Count'] == 3) {
							echo $customJs->link(($v['READ']['Count'] + 1) . '人', array(
								'controller' => 'timelines',
								'action' => 'read_user/' . $v['Timeline']['TML_ID']
							), array(
								'div' => false,
								'update' => null,
								'complete' => "popupclass.popup_view(XMLHttpRequest);popupclass.popup_open()",
								'method' => 'POST',
								'buffer' => false
							));
						}
					}
					echo "</li></ul></div></div></div></div>";
					if ($v['Timeline']['USR_ID'] == $user['User']['USR_ID'] || $val['Timeline']['USR_ID'] == $user['User']['USR_ID'] || $user['User']['AUTHORITY'] == User::AUTHORITY_TRUE) {
						echo "<p class='delete'>";
						echo $customJs->linkAfterConfirm($html->image('timeline/bt_delete.gif'), array(
							'controller' => 'timelines',
							'action' => 'delete/' . $v['Timeline']['TML_ID']
						), array(
							'escape' => false,
							'buffer' => false,
							'id' => 'delete_' . $v['Timeline']['TML_ID']
						), array(
							'description' => '削除してよろしいですか？',
							'type' => 'confirm',
							'close' => false
						), array(
							'complete' => "function(data,textStatus,xhr){timeline_delete(xhr)}"
						));
						echo "</p>";
					} elseif ($val['Timeline']['ACT_ID'] == '3' || $val['Timeline']['ACT_ID'] == '5') {
						if ($val['Group']['USR_ID'] == $user['User']['USR_ID']) {
							echo "<p class='delete'>";
							echo $customJs->linkAfterConfirm($html->image('timeline/bt_delete.gif'), array(
								'controller' => 'timelines',
								'action' => 'delete/' . $v['Timeline']['TML_ID']
							), array(
								'escape' => false,
								'buffer' => false,
								'id' => 'delete_' . $v['Timeline']['TML_ID']
							), array(
								'description' => '削除してよろしいですか？',
								'type' => 'confirm',
								'close' => false
							), array(
								'complete' => "function(data,textStatus,xhr){timeline_delete(xhr)}"
							));
							echo "</p>";
						}
					}
					echo "</dd>";
				} else {
					echo "<dd class='timeline_" . h($v['Timeline']['TML_ID']) . " comment_area'>";
					echo '<p class="repUser">' . $html->link($html->image("common/original", array(
						'style' => 'width:40px;height:40px'
					)), array(
						'controller' => 'profiles',
						'action' => 'index/' . $v['User']['USR_ID']
					), array(
						'escape' => false
					)) . '</p>';
					echo '<div class="repMid"><div class="repTop"><div class="repBtm clearfix">';
					echo '<p class="txtMain" style="margin-top:10px;padding-bottom:10px;color:red">このコメントは削除されました。</p>';
					echo "</div></div></div>";
					echo "</dd>";
				}
			}
		}
		echo "<span class='commentBtn disp_com_" . h($val['Timeline']['TML_ID']) . "'>" . $html->link('コメント', array(
			'#'
		), array(
			'onclick' => 'return disp_com(' . $val['Timeline']['TML_ID'] . ');'
		));
		echo "</span>";
		echo "<div class='comment_" . h($val['Timeline']['TML_ID']) . "'>";
		echo "<div class='commentBtn display_" . h($val['Timeline']['TML_ID']) . "' style='display:none;padding-top:5px;'>" . $form->create('Timeline') . $form->textarea('COMMENT', array(
			'value' => false,
			'label' => false,
			'style' => 'width:520px;'
		)) . $form->hidden('TML_ID', array(
			'value' => $val['Timeline']['TML_ID']
		)) . $customHtml->hiddenToken() . $js->submit('timeline/bt_share.gif', array(
			'class' => 'CommentSMT',
			'url' => array(
				'controller' => 'timelines',
				'action' => 'comment'
			),
			'div' => false,
			'update' => null,
			'complete' => "comment(XMLHttpRequest," . $val['Timeline']['TML_ID'] . ")"
		)) . $js->writeBuffer() . $form->end() . "</div>";
		echo "</div>";
		echo "</div>";
	}
}
?>
<?php

if (isset($paginator) && $paginator->hasNext()) {
	echo $form->create("Timeline", array(
		'class' => 'load'
	));
	echo $form->hidden('LAST_ID', array(
		'value' => $lastid,
		'label' => false,
		'div' => false,
		'style' => 'width:500px;height:50px;'
	));
	echo $form->hidden('GRP_ID', array(
		'value' => isset($groupid) ? $groupid : '',
		'label' => false,
		'div' => false
	));
	echo $form->hidden('M_CLASS', array(
		'value' => $m_class,
		'label' => false,
		'div' => false
	));
	echo $form->hidden('ORDER', array(
		'value' => isset($select['order']) ? $select['order'] : ''
	));
	echo $form->hidden('WHILE', array(
		'value' => isset($select['while']) ? $select['while'] : ''
	));
	echo $form->hidden('P_KEYWORD', array(
		'value' => $keyword
	));
	echo $form->hidden('P_KEYWORD_FLG', array(
		'value' => $keyword_flg
	));
	echo $form->hidden('CURRENT_PAGE', array(
		'value' => $paginator->current() + 1
	));
	echo '<p class="moreBtn">';
	
	if (! $date_frag) {
		echo $js->submit('もっと読む', array(
			'div' => false,
			'id' => 'load',
			'url' => array(
				'controller' => 'timelines',
				'action' => 'getmessage/calender/' . $calender['year'] . "/" . sprintf("%02d", $calender['mon']) . "/" . sprintf("%02d", $calender['day'])
			),
			'update' => 'null',
			'complete' => "GetMessage(XMLHttpRequest)"
		));
	} else {
		echo $js->submit('もっと読む', array(
			'div' => false,
			'id' => 'load',
			'url' => array(
				'controller' => 'timelines',
				'action' => 'getmessage'
			),
			'update' => 'null',
			'complete' => "GetMessage(XMLHttpRequest);Highlight($('.keyword').val());"
		));
	}
	echo '</p>';
	echo $js->writeBuffer();
	echo $form->end();
}

?>
</dl>

<!-- contents_End -->
