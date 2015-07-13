<!-- contents_Start -->
<?php

function week1($m, $y)
{
	$d = mktime(0, 0, 0, $m, 1, $y);
	$w = date("w", $d);
	return $w;
}

function month_days($m, $y)
{
	if ($m == 4 || $m == 6 || $m == 9 || $m == 11)
		return 30;
	if ($m != 2)
		return 31;
	if ($y % 400 == 0)
		return 29;
	if ($y % 100 == 0)
		return 28;
	if ($y % 4 == 0)
		return 29;
	return 28;
}
?>
<table cellpadding="0" cellspacing="0" class="calendarTable">
	<tr>
<?php
if ($m_class == 'Profile') {
	$userid = $profile['User']['USR_ID'];
} elseif ($m_class == 'Group') {
	$userid = $group['Group']['GRP_ID'];
} else {
	$userid = $user['User']['USR_ID'];
}
echo '<td class="prevMonth">';
if (($calender['mon'] - 1) < 1) {
	echo $customJs->link("＜", array(
		'controller' => 'timelines',
		'action' => 'calender/' . ($calender['year'] - 1) . "/12/" . $m_class . "/" . $userid
	), array(
		'update' => '.maincalender',
		'method' => 'POST',
		'buffer' => false
	));
} else {
	echo $customJs->link("＜", array(
		'controller' => 'timelines',
		'action' => 'calender/' . $calender['year'] . "/" . sprintf("%02d", ($calender['mon'] - 1)) . "/" . $m_class . "/" . $userid
	), array(
		'update' => '.maincalender',
		'method' => 'POST',
		'buffer' => false
	));
}
echo '</td>';
echo '<th class="nowMonth" colspan="5">';
echo h($calender['year']) . "年" . h($calender['mon']) . "月";
echo '</th>';
echo '<td class="nextMonth">';
if (($calender['mon'] + 1) > 12) {
	echo $customJs->link("＞", array(
		'controller' => 'timelines',
		'action' => 'calender/' . ($calender['year'] + 1) . "/01/" . $m_class . "/" . $userid
	), array(
		'update' => '.maincalender',
		'method' => 'POST',
		'buffer' => false
	));
} else {
	echo $customJs->link("＞", array(
		'controller' => 'timelines',
		'action' => 'calender/' . $calender['year'] . "/" . sprintf("%02d", ($calender['mon'] + 1)) . "/" . $m_class . "/" . $userid
	), array(
		'update' => '.maincalender',
		'method' => 'POST',
		'buffer' => false
	));
}
echo '</td>';
?>
</tr>
	<tr>
		<th class="sun">日</th>
		<th>月</th>
		<th>火</th>
		<th>水</th>
		<th>木</th>
		<th>金</th>
		<th class="sat">土</th>
	</tr>
<?php
$k = 0;
$d = 1;
for ($i = 0; $i < 6; $i ++) {
	echo "<tr>";
	for ($j = 0; $j < 7; $j ++) {
		if ($j == 0) {
			echo "<td class='leftLine'>";
		} else {
			echo "<td>";
		}
		if ($k >= week1($calender['mon'], $calender['year']) && $d <= month_days($calender['mon'], $calender['year'])) {
			if ($is_data[$d]) {
				if ($m_class == 'Home') {
					echo $customJs->link($d, array(
						'controller' => 'timelines',
						'action' => 'calender'
					), array(
						'complete' => 'var url = "' . Router::url(array(
							'controller' => 'timelines',
							'action' => 'select/' . $userid
						), true) . '"; var model="home";var year=' . $calender['year'] . ';mon=' . $calender['mon'] . ';day=' . $d . ';var order = $(".order").val();whiles = $(".while").val();select(url,model,order,whiles,year,mon,day, "FOLLOW");',
						'update' => null,
						'method' => 'POST',
						'buffer' => false
					));
				} elseif ($m_class == 'Profile') {
					echo $customJs->link($d, array(
						'controller' => 'timelines',
						'action' => 'calender'
					), array(
						'complete' => 'var url = "' . Router::url(array(
							'controller' => 'timelines',
							'action' => 'select/' . $userid
						), true) . '"; var model="profile";var year=' . $calender['year'] . ';mon=' . $calender['mon'] . ';day=' . $d . ';var order = $(".order").val();whiles = $(".while").val();select(url,model,order,whiles,year,mon,day,null);',
						'update' => null,
						'method' => 'POST',
						'buffer' => false
					));
				} elseif ($m_class == 'Group') {
					echo $customJs->link($d, array(
						'controller' => 'timelines',
						'action' => 'calender'
					), array(
						'complete' => 'var url = "' . Router::url(array(
							'controller' => 'timelines',
							'action' => 'select/' . $userid
						), true) . '"; var model="group";var year=' . $calender['year'] . ';mon=' . $calender['mon'] . ';day=' . $d . ';var order = $(".order").val();whiles = $(".while").val();select(url,model,order,whiles,year,mon,day,null);',
						'update' => null,
						'method' => 'POST',
						'buffer' => false
					));
				}
			} else {
				echo h($d);
				;
			}
			$d ++;
		} else {
			echo "　";
		}
		$k ++;
		echo "</td>\n";
	}
	echo "</tr>\n";
}
echo "</table>\n";
?>

<!-- contents_End -->