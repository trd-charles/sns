<div id="contentsRight">
	<!-- #BeginLibraryItem "/Library/contentsRight.lbi" -->
	<div class="calendarSide">
		<h3>日にちで発言を絞る</h3>
		<div class="sideMid">
			<div class="sideBtm">
				<div class='maincalender'><?php
				echo $this->element("calender")?></div>
				<p class="cancelBtn"></p>
			</div>
		</div>
	</div>
	<div class="groupSide"><?php
	echo $this->element("join_group", $group)?></div>
	<div class="userSide"><?php
	echo $this->element("following", $following_user)?></div>
	<div class="userSide"><?php
	echo $this->element("follower", $follower_user)?></div>
	<p class="paySide">
		<?php
		echo $html->link($html->image('common/bnr_pay.jpg', array(
			'alt' => '抹茶シリーズ有償サービスのご案内',
			'class' => 'on'
		)), 'http://oss.icz.co.jp/', array(
			'escape' => false,
			'target' => '_blank'
		));
		?>
	</p>
</div>