<style>
<!--
#popup_wrap {
	border: 4px solid rgb(82, 82, 82);
}

#popup_profile_edit {
	width: 460px;
	padding: 10px;
}

#popup_profile_edit table {
	width: 460px;
}

#popup_profile_edit table th {
	width: 90px;
	text-align: left;
	font-weight: bold;
	padding: 0 10px 10px 5px;
}

#popup_profile_edit table th img {
	border: 1px solid #CCC;
}

#popup_profile_edit table td {
	padding: 0 15px 10px 0;
}

#popup_profile_edit ul {
	width: 460px;
	margin: 5px 0 0 5px;
}

#popup_profile_edit ul li {
	display: inline;
	padding-right: 5px;
}
-->
</style>
<div id="popup_wrap">
	<div id="TB_title">
		<div id="TB_ajaxWindowTitle">プロフィール編集</div>
		<div id="TB_closeAjaxWindow">
			<a id="TB_closeWindowButton" title="Close" href="javascript:void(0)"
				onClick="popupclass.popup_close();" style='color: #fff;'>閉じる</a>
		</div>
	</div>
	<div id="popup_profile_edit">
		<?php
		echo $form->create()?>
			<table>
			<tr>
				<th>名前<span class='required'>*</span></th>
				<td>
				<?php
				echo $form->text('NAME', array(
					'class' => $form->error('NAME') ? 'f_errors' : ''
				));
				?>
				<?php
				echo $form->error('NAME', array(
					'class' => 'errors'
				));
				?>
				</td>
			</tr>
			<tr>
				<th>所属名</th>
				<td>
				<?php
				echo $form->text('UNIT', array(
					'class' => $form->error('UNIT') ? 'f_errors' : ''
				));
				?>
				<?php
				echo $form->error('UNIT', array(
					'class' => 'errors'
				));
				?>
				</td>
			</tr>
			<tr>
				<th>郵便番号</th>
				<td>
					<?php
					echo $form->text('POSTCODE1', array(
						'style' => 'width:100px;',
						'class' => $form->error('POSTCODE1') || $form->error('POSTCODE2') ? 'f_errors' : ''
					))?>
					<span class="pl5 pr5">-</span>
					<?php
					echo $form->text('POSTCODE2', array(
						'style' => 'width:100px;',
						'class' => $form->error('POSTCODE1') || $form->error('POSTCODE2') ? 'f_errors' : ''
					))?>
					<?php
					if ($form->error('POSTCODE1')) {
						echo $form->error('POSTCODE1', array(
							'class' => 'errors'
						));
					} else {
						echo $form->error('POSTCODE2', array(
							'class' => 'errors'
						));
					}
					?>
				</td>
			</tr>
			<tr>
				<th>都道府県</th>
				<td>
					<?php
					echo $form->input('CNT_ID', array(
						'label' => false,
						'options' => $countys
					));
					?>
				</td>
			</tr>
			<tr>
				<th>住所</th>
				<td>
				<?php
				echo $form->text('ADDRESS', array(
					'style' => 'width:300px;',
					'class' => $form->error('ADDRESS') ? 'f_errors' : ''
				));
				?>
				<?php
				echo $form->error('ADDRESS', array(
					'class' => 'errors'
				));
				?>

				</td>
			</tr>
			<tr>
				<th>電話番号</th>
				<td>
					<?php
					echo $form->text("PHONE_NO1", array(
						'size' => 8,
						'class' => $form->error('PHONE_NO1') || $form->error('PHONE_NO2') || $form->error('PHONE_NO3') ? 'f_errors' : ''
					));
					?>
			 		<span class="pl5 pr5">-</span>
					<?php
					echo $form->text("PHONE_NO2", array(
						'size' => 8,
						'class' => $form->error('PHONE_NO1') || $form->error('PHONE_NO2') || $form->error('PHONE_NO3') ? 'f_errors' : ''
					));
					?>
			 		<span class="pl5 pr5">-</span>
			 		<?php
						echo $form->text("PHONE_NO3", array(
							'size' => 8,
							'class' => $form->error('PHONE_NO1') || $form->error('PHONE_NO2') || $form->error('PHONE_NO3') ? 'f_errors' : ''
						));
						?>
					<?php
					if ($form->error('PHONE_NO1')) {
						echo $form->error('PHONE_NO1', array(
							'class' => 'errors'
						));
					} elseif ($form->error('PHONE_NO2')) {
						echo $form->error('PHONE_NO2', array(
							'class' => 'errors'
						));
					} else {
						echo $form->error('PHONE_NO3', array(
							'class' => 'errors'
						));
					}
					?>
			 	</td>
			</tr>
			<tr>
				<th>携帯番号</th>
				<td>
					<?php
					echo $form->text("M_PHONE_NO1", array(
						'size' => 8,
						'class' => $form->error('M_PHONE_NO1') || $form->error('M_PHONE_NO2') || $form->error('M_PHONE_NO3') ? 'f_errors' : ''
					));
					?>
			 		<span class="pl5 pr5">-</span>
					<?php
					echo $form->text("M_PHONE_NO2", array(
						'size' => 8,
						'class' => $form->error('M_PHONE_NO1') || $form->error('M_PHONE_NO2') || $form->error('M_PHONE_NO3') ? 'f_errors' : ''
					));
					?>
			 		<span class="pl5 pr5">-</span>
					<?php
					echo $form->text("M_PHONE_NO3", array(
						'size' => 8,
						'class' => $form->error('M_PHONE_NO1') || $form->error('M_PHONE_NO2') || $form->error('M_PHONE_NO3') ? 'f_errors' : ''
					));
					?>
					<?php
					if ($form->error('M_PHONE_NO1')) {
						echo $form->error('M_PHONE_NO1', array(
							'class' => 'errors'
						));
					} elseif ($form->error('M_PHONE_NO2')) {
						echo $form->error('M_PHONE_NO2', array(
							'class' => 'errors'
						));
					} else {
						echo $form->error('M_PHONE_NO3', array(
							'class' => 'errors'
						));
					}
					?>
			 	</td>
			</tr>
			<tr>
				<th>自己紹介</th>
				<td>
				<?php
				echo $form->textarea('DESCRIPTION', array(
					'style' => 'width:300px;height:100px',
					'class' => $form->error('DESCRIPTION') ? 'f_errors' : ''
				));
				?>
				<?php
				echo $form->error('DESCRIPTION', array(
					'class' => 'errors'
				));
				?>

				</td>
			</tr>
		</table>
		<span style="text-align: center">
			<?php
			echo $customHtml->hiddenToken();
			?>
			<?php
			echo $js->submit('profile/bt_save.gif', array(
				'url' => array(
					'controller' => 'profiles',
					'action' => 'edit',
					'plugin' => null
				),
				'update' => 'null',
				'complete' => "profile(XMLHttpRequest)"
			));
			echo $js->writeBuffer();
			echo $form->end();
			?>
		</span>
	</div>
</div>
