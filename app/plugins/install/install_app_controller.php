<?php
class InstallAppController extends AppController {

	function beforeFilter() {
		//説明文の文言
		if($this->action === 'add'){
			$db = ConnectionManager::getDataSource('default');
			if($this->Install->findUser() && file_exists(APP.'config'.DS.'finish')){
				$this->redirect('/');
			}
		}else{
			//インストールファイルがある場合
			if(file_exists(APP.'config'.DS.'finish')){
				$this->redirect('/');
			}
		}
	}
}