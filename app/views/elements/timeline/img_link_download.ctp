<?php

// 拡張子によって表示アイコン分け
if (in_array($val['Storage']['EXTENSION'], Configure::read('FILE_EXTENSIONS_WORD'))) {
	
	$img_name = 'word.png';
} else 
	if (in_array($val['Storage']['EXTENSION'], Configure::read('FILE_EXTENSIONS_EXCEL'))) {
		
		$img_name = 'excel.png';
	} else 
		if ($val['Storage']['EXTENSION'] == 'zip') {
			
			$img_name = 'zip.png';
		} else 
			if ($val['Storage']['EXTENSION'] == 'txt') {
				
				$img_name = 'txt.png';
			} else {
				
				$img_name = 'other.png';
			}

// ダウンロードリンク(アイコン)表示
echo $html->link($html->image('common/thumb/' . $img_name, array(
	'class' => 'fileImg',
	'width' => '40px',
	'height' => '40px'
)), array(
	'controller' => 'storages',
	'action' => 'download/' . $val['Storage']['RAND_NAME']
), array(
	'escape' => false
));
?>