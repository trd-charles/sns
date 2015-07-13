<ul id="tab">
	<?php
		if(isset($this->params['named']['tab']) && $this->params['named']['tab']=='OnlyMe'){
			echo '<li class="present">'.$html->link("<span>自分のノート</span>",array('controller'=>'note','action'=>'index', 'tab' => 'OnlyMe', 'plugin' => 'note'),array('escape'=>false,'div'=>false)).'</li>';
			echo '<li>'.$html->link("<span>全てのノート</span>",array('controller'=>'note','action'=>'index','tab' => 'All', 'plugin' => 'note'),array('escape'=>false,'div'=>false))."</li>";
		}else{
			echo '<li>'.$html->link("<span>自分のノート</span>",array('controller'=>'note','action'=>'index', 'tab' => 'OnlyMe', 'plugin' => 'note'),array('escape'=>false,'div'=>false)).'</li>';
			echo '<li class="present">'.$html->link("<span>全てのノート</span>",array('controller'=>'note','action'=>'index', 'tab' => 'All', 'plugin' => 'note'),array('escape'=>false,'div'=>false)).'</li>';
		}
	?>
	<li><?php echo $html->link($html->image('/note/img/bt_note.gif',array('alt'=>'ファイルをアップロードする')), array('controller' => 'note', 'action' => 'add', 'plugin' => 'note'),array('id'=>'file_up', 'escape'=>false,'div'=>false));?></li>
</ul>