<?php
/**
 * Sample Controller
 *
 */
//何かコントローラーを継承する時
class SampleController extends SampleAppController {
/**
 * コントローラーの名前
 */
	var $name = 'Sample';
/**
 * 使用するモデル
 *
 */

/**
 * その他設定
 *
 */
	var $autoLayout = true;


/**
 * beforeFilter
 *
 * @return void
 */
	function beforeFilter() {
		parent::beforeFilter();
		$route= Router::getInstance();
		$route->__params[0]['plugin']=null;
	}
/**
 *
 */
	function index(){
		//初期化
		$this->set("main_title", "管理者メニュー");
		$this->set("title_text","ユーザ管理");
	}

}