<script type="text/javascript">
function Highlight(keyword) {
	var txtMain = $(".txtMain");
	var t, str, start, end;
	var matchWords, matchWord;
	for(var i = 0; i < txtMain.length; i++){
		t = $(txtMain[i]).html();

		matchWords = t.match(RegExp(keyword, "gi"));
		if(matchWords != null) {
			for(var j = 0; j < matchWords.length; j++) {
				str = "";
				while(t.search(/</) != -1 && t.search(/>/) != -1){
					start = t.search(/</);
					end = t.search(/>/) + 1;
					str += t.substring(0, start).replace(RegExp(matchWords[j], ""), '<span class="highlight">'+t.match(RegExp(matchWords[j], ""))+'</span>');
					str += t.substring(start, end);
					t = t.substring(end);
				}

				str += t.replace(RegExp(matchWords[j], ""), '<span class="highlight">'+t.match(RegExp(matchWords[j], ""))+'</span>');
				$(txtMain[i]).html(str);
				t = str;
			}
		}


	}
}

$(document).ready(function(){
	Highlight("<?php
	echo h($keyword);
	?>");
});

</script>
<!-- contents_Start -->
<?php
echo $html->css("timeline", "stylesheet", array(
	'media' => 'screen'
)) . "\n";
?>
<div id="timelineName" class="clearfix">
	<p><?php
	echo $html->image('common/thumb/search.png');
	?></p>
	<h2><?php
	echo h($keyword) . "の検索結果";
	?></h2>
</div>
<div id="contentsLeft">
	<ul id="tab">
	</ul>
	<div id="tabBoxIndex">
		<div id="tabBoxIndexBtm">
			<div id="tabBoxIndexArea">
				<dl class="timeline">
		<?php
		echo $this->element("timeline/searchResult_timeline", $list);
		?>
			</div>
		</div>
	</div>
	<p class="pageTop">
		<a href="#top">上に戻る</a>
	</p>
</div>

<!-- #EndLibraryItem -->
<!-- InstanceEndEditable -->
<!-- contents_End -->