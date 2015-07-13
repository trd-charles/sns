<?php
class Install extends InstallAppModel {
	var $name = 'Install';
	var $useTable = false;


	function regsit($params , $pass){
		App::import('Model','Administrator');
		$administrator = new Administrator();
		$params['Administrator']['PASSWORD']=$pass;
		if($administrator ->User_Save($params,true)){
			//DB登録
			return true;
		}else{
			return false;
		}
	}

	function mail_set($_params,&$_error){
		App::import('Model','Configuration');
		$config = new Configuration();

		$param["Configuration"] = $_params;
		return $config->index_set_data($param,$_error,'new');
	}
	function findUser(){
		App::import('Model','User');
		$user = new User();
		if(count($user->find()) > 0){
			return true;
		}
		return false;
	}
}