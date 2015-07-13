<style type="text/css"><!--
.contents_box {
	padding-botom:20px;
	padding-top:50px;
	padding-left:150px;

}
--></style>

<div class="contents_box mb20">
		<div class="contents_area">
		<?php
			echo $html->link(__('クリックすると、テーブル・初期データを作成します。', true), array(
				'plugin' => 'install',
				'controller' => 'install',
				'action' => 'data',
				'run' => 1,
			));
		?>
		</div>
</div>