<div id='pagination'>
<?php
if(isset($keyword)) {
	$paginator->options(array('url' => array('keyword' => $keyword)));
}
//総ページ数の取得
$pages = $paginator->counter(array('format' => "%pages%"));
//ページ番号表示件数
$modules = 5;
//前後表示件数
$range = (int)($modules / 2);


//前へリンク表示
if($paginator->hasPrev()) {
	echo $paginator->link(
		'<< '.__('前へ', true),
	array('plugin' => 'note', 'controller' => 'note', 'action' => 'index', 'page' => $page - 1),
	array('class'=>'disabled', 'tag' => 'span'));
}
else {
	echo '<< '.__('前へ', true);
}

?>
 |
<?php

//表示するページ番号の設定
$min_page = 1;
//表示するページの最小値
if($paginator->hasPage(null, $page - $range) && $page - $range > 1) {
	$min_page = $page - $range;
}
else {
	$min_page = 1;
}

//表示するページの最大値
$max_page = $min_page + $modules - 1;
if(!($paginator->hasPage(null, $max_page) && $max_page <= $pages)) {
	$max_page = $pages;

	//最小値の再設定
	if($max_page - $modules + 1 < 1) {
		$min_page = 1;
	}
	else if($max_page - $modules + 1 >= 1){
		$min_page = $max_page - $modules + 1;
	}
}


//ぺージ番号表示(1ページのみの場合は番号を表示しない)
if($pages > 1) {
	for($i = $min_page; $i <= $max_page; $i++) {

		if($i == $page) {
			//現在ページはリンクにしない
			echo h($i);
		}
		else {
			echo $paginator->link(
			$i,
			array('plugin' => 'note', 'controller' => 'note', 'action' => 'index', 'page' => $i),
			array('class'=>'disabled', 'tag' => 'span'));
		}
		echo ' | ';
	}
}
else {
	echo ' | ';
}
?>
<?php
//次へリンク表示
if($paginator->hasNext()) {
	echo $paginator->link(
	__('次へ', true).' >>',
	array('plugin' => 'note', 'controller' => 'note', 'action' => 'index', 'page' => $page + 1),
	array('class'=>'disabled', 'tag' => 'span'));
}
else {
 echo __('次へ', true).' >>';
}
?>
</div>