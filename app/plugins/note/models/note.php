<?php
/**
* @copyright ICZ Corporation (http://www.icz.co.jp/)
* @license See the LICENCE file
* @author <matcha@icz.co.jp>
* @version $Id$
*/

class Note extends NoteAppModel{

	var $name = 'Note';
	var $useTable = 'T_NOTE';
	var $primaryKey = 'NOTE_ID';

	var $user;
	var $userID;
	var $noteID;

	//公開ステータス
	var $STATUS_DRAFT = 0;
	var $STATUS_PUBLIC = 1;

	//プラグイン読み込み
	var $actsAs = array('Cakeplus.AddValidationRule');

	var $belongsTo = array('User' =>
		array(
			'className'  => 'User',
			'fields'     => array('NAME'),
			'conditions' => '',
			'order'      => '',
			'dependent'  => true,
			'foreignKey' => 'USR_ID',
		),
	);

	//バリデーション
	var $validate = array(
		'TITLE' => array(
			'rule0' => array('rule' => array('maxLengthJP', 40),'message' => 'タイトルは40文字以内で入力してください。'),
			'rule1' => array('rule' => 'notEmpty','message' => 'タイトルは必須項目です'),
		),
	);

	var $status = array('0' => '下書き', '1' => '公開');
	var $default_public = array('All' => '全体公開', 'OnlyMe' => '自分のみ', 'Followed' => 'フォローされているユーザ');
	var $public;

	function saveNote($_data, $isEdit = false) {

		if(empty($this->userID)) {
			return false;
		}


		if($isEdit) {
			$_data['Note']['UPDATE_DATE'] = date("Y-m-d H:i:s");
		}
		else {
			if($_data['Note']['STATUS']) {
				//$this->postMessage();
			}

			$_data['Note']['USR_ID'] = $this->userID;
			$_data['Note']['INSERT_DATE'] = date("Y-m-d H:i:s");
			$_data['Note']['UPDATE_DATE'] = date("Y-m-d H:i:s");
		}

		if(isset($_data['Note']['STATUS']) && $_data['Note']['STATUS'] == $this->STATUS_DRAFT) {
			$_data['Note']['PUBLIC'] = 'OnlyMe';
		}

		if($this->save($_data)){
			if($_data['Note']['PUBLIC'] == 'All') {
				$_data['Note']['NOTE_ID'] = $this->id;
				$this->postMessage($_data);
			}
			else if($_data['Note']['PUBLIC'] == 'OnlyMe') {
			}
			else if($_data['Note']['PUBLIC'] == 'Followed'){
				$_data['Note']['NOTE_ID'] = $this->id;
				$this->postMessage($_data);
			}
			else {
				$_data['Note']['NOTE_ID'] = $this->id;
				$this->postMessage($_data, false, 'Group');
			}
			return true;
		}else {
			return false;
		}

	}

	function getNoteContent() {
		if(empty($this->noteID)) {
			return false;
		}else {
			return $this->find('first', array('conditions' => array('Note.NOTE_ID' => $this->noteID)));
		}

	}

	function setPublic() {
		$this->public = $this->default_public;

		App::import('Model','Group');
		App::import('Model','Join');
		$group = new Group();
		$join  = new Join();

		if(empty($this->userID)) {
			return false;
		}

		$result = $join->Join_Group($this->userID);
		$joinGroup = array();

		foreach($result as $key => $val) {
			array_push($joinGroup, $val['Join']['GRP_ID']);
		}

		$public = $group->find('all', array('fields' => array('Group.GRP_ID', 'Group.NAME'), 'conditions' => array('Group.GRP_ID' => $joinGroup)));

		foreach($public as $key => $val) {
			$this->public[$val['Group']['GRP_ID']] = $val['Group']['NAME'];
		}
	}

	function getPaginateCondition($action, $keyword = null, $isAdmin = false) {
		$conditions = array();
		if($action == 'All') {
			//管理者の場合すべてのノートを表示
			if (!$isAdmin) {
				App::import('Model','Friend');
				$friend = new Friend();

				//フォローしているユーザの取得
				$aryFriendId = array();
				$result = $friend->Get_Friend($this->userID);
				foreach ($result as $key => $val) {
					array_push($aryFriendId, $val['Friend']['F_USR_ID']);
				}
				array_push($aryFriendId, $this->userID);

				//所属グループ取得
				$aryGrpId = array();
				foreach ($this->public as $key => $val) {
					if(!in_array($val, $this->default_public)) array_push($aryGrpId, $key);
				}

				App::import('Model','Group');
				$group = new Group();
				$public_group = $group->find('all', array('fields' => 'Group.*', 'conditions' => array('Group.TYPE' => Group::TYPE_PUBLIC)));

				foreach ($public_group as $key => $val) {
					if(!in_array($val['Group']['GRP_ID'], $aryGrpId))  {
						array_push($aryGrpId, $val['Group']['GRP_ID']);
						$this->public[$val['Group']['GRP_ID']] = $val['Group']['NAME'];
					}
				}

				$conditions['or'][] = array('Note.PUBLIC' => 'All', 'Note.STATUS' => $this->STATUS_PUBLIC);
				$conditions['or'][] = array('Note.PUBLIC' => 'OnlyMe',   'Note.USR_ID' => $this->userID, 'Note.STATUS' => $this->STATUS_PUBLIC);
				$conditions['or'][] = array('Note.PUBLIC' => 'Followed', 'Note.USR_ID' => $aryFriendId, 'Note.STATUS' => $this->STATUS_PUBLIC);
				$conditions['or'][] = array('Note.PUBLIC' => $aryGrpId, 'Note.STATUS' => $this->STATUS_PUBLIC);
			}
			if(!empty($keyword)) {
				$tmp = $conditions;
				$conditions = array();
				$conditions['and'][] = array('Note.TITLE LIKE' => "%".$keyword."%");
				$conditions['and'][] = $tmp;
			}

		}else {
			$conditions = array('Note.USR_ID' => $this->userID);

			if(!empty($keyword)) {
				$conditions['Note.TITLE LIKE'] = "%".$keyword."%";
			}
		}

		return $conditions;
	}

	function postMessage($_note, $_isEdit = false, $_model = 'Note') {
		//ステータスチェック
		if(
			(isset($_note['Note']['STATUS_OLD'])
			&& $_note['Note']['STATUS_OLD'] == $this->STATUS_DRAFT
			&& $_note['Note']['STATUS'] == $this->STATUS_PUBLIC)
			|| (!isset($_note['Note']['STATUS_OLD'])
			&& $_note['Note']['STATUS'] == $this->STATUS_PUBLIC)
		)
		{
			App::import('Model','Timeline');
			$timeline = new Timeline();
			if($_model == 'Note') {
				$data['Timeline']['MESSAGE'] = "ノート「".$_note['Note']['TITLE']."」を公開しました。\n".Router::url(array('plugin' => 'note', 'controller' => 'note', 'action' => 'content', 'noteID' => $_note['Note']['NOTE_ID']), true);;
				return $timeline->Save_Message($_model, $data, $this->user);
			}
			else {
				$data['Timeline']['MESSAGE'] = "ノート「".$_note['Note']['TITLE']."」を公開しました。\n".Router::url(array('plugin' => 'note', 'controller' => 'note', 'action' => 'content', 'noteID' => $_note['Note']['NOTE_ID']), true);;
				return $timeline->Save_Message($_model, $data, $this->user, $_note['Note']['PUBLIC']);
			}

		}
	}

	function checkInspectionAuth() {
		$this->userID;
		$this->noteID;

		$note = $this->find('first', array('conditions' => array('Note.NOTE_ID' => $this->noteID)));

		if(empty($note)) {
			return false;
		}
		//自分が作ったもの
		if($note['Note']['USR_ID'] == $this->userID) {
			return true;
		}

		//他人が作ったもの
		else {
			if($note['Note']['PUBLIC'] == 'All') {
				return true;
			}
			else if($note['Note']['PUBLIC'] == 'Followed') {

				App::import('Model','Friend');
				$friend = new Friend();

				//フォローしているユーザの取得
				$aryFriendId = array();
				$result = $friend->Get_Friend($this->userID);

				foreach ($result as $key => $val) {
					array_push($aryFriendId, $val['Friend']['F_USR_ID']);
				}
				return in_array($note['Note']['USR_ID'], $aryFriendId);

			}
			else if($note['Note']['PUBLIC'] == 'OnlyMe') {
				return false;
			}
			else {
				//所属グループ取得
				$aryGrpId = array();
				foreach ($this->public as $key => $val) {
					if(!in_array($val, $this->default_public)) array_push($aryGrpId, $key);
				}

				App::import('Model','Group');
				$group = new Group();
				$public_group = $group->find('all', array('fields' => 'Group.*', 'conditions' => array('Group.TYPE' => Group::TYPE_PUBLIC)));

				foreach ($public_group as $key => $val) {
					if(!in_array($val['Group']['GRP_ID'], $aryGrpId))  {
						array_push($aryGrpId, $val['Group']['GRP_ID']);
					}
				}

				return in_array($note['Note']['PUBLIC'], $aryGrpId);
			}
		}

	}

	//権限チェック
	function checkEditAuth($id) {
		$note = $this->find('first', array('conditions' => array('Note.NOTE_ID' => $id, 'Note.USR_ID' => $this->userID)));
		return !empty($note);
	}

}