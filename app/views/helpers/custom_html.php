<?php

/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
class CustomHtmlHelper extends HtmlHelper
{

	public $helpers = array(
		'Form'
	);

	/**
	 * htmlspecialchars
	 *
	 * @param unknown $_data        	
	 * @param string $_cont        	
	 * @param string $_attr        	
	 * @param string $_decimal        	
	 * @return unknown
	 */
	function ht2br($_data, $_cont = null, $_attr = null, $_decimal = null)
	{
		$data = h($_data);
		return $data;
	}

	/**
	 * URLを検出しリンクタグを付ける
	 *
	 * @param unknown $data        	
	 */
	private function __url_search(&$data)
	{
		$domain = Configure::read('DOMAIN_CODE');
		
		$pattern = '/[-_.!~*\'a-zA-Z0-9;\/?:\@&=+\$,%#]+/';
		preg_match_all($pattern, $data, $matches, PREG_SET_ORDER);
		$pattern2 = '/\s\n/';
		$i = 0;
		$array = array();
		
		foreach ($matches as $key => $val) {
			$array[$i] = preg_split($pattern2, $val[0]);
			$i ++;
		}
		
		$pos = 0;
		$i = 0;
		$body = array();
		$tes = 0;
		foreach ($array as $key => $val) {
			
			foreach ($val as $key3 => $val3) {
				if ($val3 != null) {
					$tes = strpos($data, $val3, $pos) + strlen($val3) - $pos;
					$body[$i] = substr($data, $pos, $tes);
					$pos = $pos + strlen($body[$i]);
					$test = preg_match('/(https?|ftp)(:\/\/[-_.!~*\'a-zA-Z0-9;\/?:\@&=+\$,%#]+)/', $body[$i], $str);
					if ($test > 0) {
						
						// 同じドメイン(サブドメイン)の場合以外の場合は、targetを_blankにする
						$dom = env('HTTP_HOST');
						
						$tar = "";
						$ma = preg_match("/" . $dom . "/", $str[0]);
						if ($ma == "1") {
							$tar = "_self";
						}
						if ($ma == "0") {
							$tar = "_blank";
						}
						
						$body[$i] = preg_replace('/(https?|ftp)(:\/\/[-_.!~*\'a-zA-Z0-9;\/?:\@&=+\$,%#]+)/', '<A href="\\1\\2" target=' . $tar . '>\\1\\2</A>', $body[$i]);
					} else {
						foreach ($domain as $key2 => $val2) {
							if (preg_match('/[a-zA-z0-9]+\.' . $val2 . '$/', $body[$i])) {
								if (strpos($val3, '@') !== false) {
									if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $val3)) {
										$str = '<A href = "mailto:' . $val3 . '">' . $val3 . '</A>';
									} else {
										$str = $val3;
									}
								} else {
									$str = '<A href = "http://' . $val3 . '">' . $val3 . '</A>';
								}
								$body[$i] = str_replace($val3, $str, $body[$i]);
							}
						}
					}
					$i ++;
				}
			}
		}
		
		$body[$i] = substr($data, $pos);
		
		$data = '';
		foreach ($body as $key => $val) {
			$data .= $val;
		}
	}

	/**
	 * 文字列カット
	 *
	 * 文字列を「表示するブロック」と「表示しないブロック」に区分けしjavascriptで表示の制御ができるHTMLを出力する
	 *
	 * @param string $data        	
	 * @param string $no        	
	 * @param string $parentId        	
	 * @param string $dnum        	
	 * @param string $url        	
	 * @param string $short        	
	 * @return string
	 */
	function text_cut($data, $no = null, $parentId = null, $dnum = null, $url = true, $short = true)
	{
		
		/*
		 * htmlspecialcharsを行う
		 */
		$data = h($data);
		
		/*
		 * URLの存在チェック
		 */
		if ($url) {
			$this->__url_search($data);
		}
		
		/*
		 * 改行箇所で改行タグをいれる
		 */
		$data = nl2br($data);
		
		$tmp_s = "";
		$result = "";
		$result = $data;
		
		$ar = array();
		
		if ($short) {
			
			/*
			 * 検証BL 400文字以上であれば
			 */
			if (mb_strlen($result) > 400) {
				
				// 改行タグの個数取得
				$match_number = preg_match_all("/<br \/>/", $result, $ar);
				
				/*
				 * 改行タグが5未満
				 */
				if ($match_number < 5) {
					
					// 400文字まで取り出す
					$result_tmp = mb_substr($result, 0, 400);
					
					// リンクタグの個数取得
					$a = preg_match('/<A href=/', $result_tmp);
					
					// 400文字移行を取り出す
					$hidden_string = mb_substr($result, 400);
					
					/*
					 * リンクタグがあれば
					 */
					if ($a) {
						
						// 表示しない文字列にリンクの閉じタグがある場合、場所を取得
						$pos = strpos($hidden_string, '/A>');
						
						// 表示しない文字列の「/A>」までを取得し、表示する文字列に結合
						$result_tmp = $result_tmp . substr($hidden_string, 0, $pos + 3);
						
						// 表示しない文字列の「/A>」以降を取得し表示しない文字列を再セット
						$hidden_string = substr($hidden_string, $pos + 3);
					}
					
					/*
					 * 表示しない文字列があれば
					 */
					if ($hidden_string != false) {
						
						// 表示文字列に「リンクしたさらに表示」を結合
						$result = $result_tmp . "<span class='open_" . $no . " open_parent_" . $parentId . "'>…" . $this->link("さらに表示", '#', array(
							'onclick' => 'open_text(' . $no . ');return false;'
						)) . "</span>";
						
						// 結合した表示文字列に「タグと表示しない文字列」を連結
						$result = $result . "<span style='display:none'class='hidden_text_" . $no . " hidden_text_parent_" . $parentId . "'>" . $hidden_string . "</span>";
					}
				} else {
					
					$first = 0;
					$tmp = "";
					$result_tmp = "";
					$dst = $result;
					
					for ($i = 0; $i < 4; $i ++) {
						
						// 1行分$tmpに格納
						$tmp = substr($dst, 0, strpos($dst, '<br />') + 6);
						
						// 1行分の文字列の長さを取得しその文字数以降の文字列を$dstに格納
						$dst = substr($dst, strlen($tmp));
						
						// 1行分を連結
						$result_tmp = $result_tmp . $tmp;
					}
					
					$a = preg_match('/<A href=/', $result_tmp);
					$a_end = preg_match('/\/A>/', $result_tmp);
					$hidden_string = substr($result, strlen($result_tmp));
					
					if ($a && $a != $a_end) {
						
						// 表示しない文字列にリンクの閉じタグがある場合、場所を取得
						$pos = strpos($hidden_string, '/A>');
						
						// 表示しない文字列の「/A>」までを取得し、表示する文字列に結合
						$result_tmp = $result_tmp . substr($hidden_string, 0, $pos + 3);
						
						// 表示しない文字列の「/A>」以降を取得し表示しない文字列を再セット
						$hidden_string = substr($hidden_string, $pos + 3);
					}
					
					if ($hidden_string != false) {
						
						// 表示文字列に「リンクしたさらに表示」を結合
						$result = $result_tmp . "<span class='open_" . $no . " open_parent_" . $parentId . "'>…" . $this->link("さらに表示", '#', array(
							'onclick' => 'open_text(' . $no . ');return false;'
						)) . "</span>";
						
						// 結合した表示文字列に「タグと表示しない文字列」を連結
						$result = $result . "<span style='display:none'class='hidden_text_" . $no . " hidden_text_parent_" . $parentId . "'>" . $hidden_string . "</span>";
					}
				}
			} else {
				
				// 400文字以下なら
				
				// 改行タグの個数取得
				$match_number = preg_match_all("/<br \/>/", $result, $ar);
				
				if ($match_number > 5) {
					
					$first = 0;
					$tmp = "";
					$result_tmp = "";
					$dst = $result;
					
					for ($i = 0; $i < 5; $i ++) {
						$tmp = substr($dst, 0, strpos($dst, '<br />') + 6);
						$dst = substr($dst, strlen($tmp));
						$result_tmp = $result_tmp . $tmp;
					}
					
					$a = preg_match('/<A href=/', $result_tmp);
					$a_end = preg_match('/\/A>/', $result_tmp);
					$hidden_string = substr($result, strlen($result_tmp));
					
					if ($a && $a != $a_end) {
						
						// 表示しない文字列にリンクの閉じタグがある場合、場所を取得
						$pos = strpos($hidden_string, '/A>');
						
						// 表示しない文字列の「/A>」までを取得し、表示する文字列に結合
						$result_tmp = $result_tmp . substr($hidden_string, 0, $pos + 3);
						
						// 表示しない文字列の「/A>」以降を取得し表示しない文字列を再セット
						$hidden_string = substr($hidden_string, $pos + 3);
					}
					
					if ($hidden_string != false) {
						
						// 表示文字列に「リンクしたさらに表示」を結合
						$result = $result_tmp . "<span class='open_" . $no . " open_parent_" . $parentId . "'>…" . $this->link("さらに表示", '#', array(
							'onclick' => 'open_text(' . $no . ');return false;'
						)) . "</span>";
						
						// 結合した表示文字列に「タグと表示しない文字列」を連結
						$result = $result . "<span style='display:none'class='hidden_text_" . $no . " hidden_text_parent_" . $parentId . "'>" . $hidden_string . "</span>";
					}
				}
			}
		}
		
		$data = $result;
		return $data;
	}

	/**
	 * バイト数を受け取り単位をつけて返す
	 *
	 * @param unknown $data        	
	 * @return string
	 * @access public
	 */
	function file_size($data)
	{
		if ($data > 1023) {
			
			$tmp = round(((float) $data / 1024.0), 1);
			if ($tmp > 1023) {
				$tmp2 = round(((float) $tmp / 1024.0), 1);
				$data = $tmp2 . "MB";
			} else {
				$data = $tmp . "KB";
			}
		} else {
			$data = $data . "B";
		}
		
		return $data;
	}

	/**
	 * トークン用の隠しフォームエレメントを返す
	 *
	 * @return object
	 * @access public
	 */
	public function hiddenToken()
	{
		return $this->Form->hidden('Security.token', array(
			'value' => session_id()
		));
	}

	/**
	 * 日時分割
	 *
	 * Y-m-d h:i:sの形式のデータを分割し改行タグを入れたものを返す
	 *
	 * @param string $_date        	
	 * @return string
	 * @access public
	 */
	public function date_split($_date)
	{
		$pieces = explode(" ", $_date);
		return $pieces[0] . "<br />" . $pieces[1];
	}
}
