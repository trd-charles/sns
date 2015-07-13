<?php
/**
* @copyright ICZ Corporation (http://www.icz.co.jp/)
* @license See the LICENCE file
* @author <matcha@icz.co.jp>
* @version $Id$
*/

class NoteController extends NoteAppController {

	var $name = 'Note';
	var $uses = array('Note.Note', 'Join', 'Storage', 'Timeline');
	var $autoLayout = true;
	var $components = array('Session','Auth','Cookie','Permission');

	public $paginate = array(
		'page' => 1,
		'conditions' => array(),
		'sort' => '',
		'limit' => 20,
		'order' => 'Note.UPDATE_DATE DESC',
		'recursive' => 0
	);

	function beforeFilter(){
		parent::beforeFilter();
		//この2文を入れることにより、デフォルトのレイアウトのままでも動作する。
 		$route= Router::getInstance();
 		$route->__params[0]['plugin']=null;

		$user = $this->Auth->user();
		$this->Note->user = $user;
		$this->Note->userID = $user['User']['USR_ID'];
		$this->Note->setPublic();
		$this->set('status', $this->Note->status);
		$this->set('public', $this->Note->public);
		if(isset($this->params['named']['noteID'])) $this->Note->noteID = $this->params['named']['noteID'];
		if($this->action == 'index' && empty($this->params['named']['tab'])) $this->params['named']['tab'] = 'OnlyMe';
	}

	function index() {
		$this->set('main_title', 'ノート機能');
		$this->set('title_text', 'ノート一覧');

		if($this->checkPost() && isset($this->data['Note']['KEYWORD'])) {
			$keyword = $this->data['Note']['KEYWORD'];
			$this->set('keyword', $keyword);
			$this->data['Note']['KEYWORD'] = $keyword;
		}
		else if(isset($this->params['named']['keyword'])) {
			$keyword = urldecode($this->params['named']['keyword']);
			$this->set('keyword', urldecode($keyword));
			$this->data['Note']['KEYWORD'] = $keyword;
		}
		else {
			$keyword = null;
		}

		$isAdmin = false;
		$this->Permission->allowAdmin();
		$isAdmin = $this->Permission->isAllowed($this->Note->userID);
		$this->set('isAdmin', $isAdmin);
		$this->paginate['conditions'] = $this->Note->getPaginateCondition($this->params['named']['tab'], $keyword, $isAdmin);
		$list = $this->paginate();
		$this->set('list', $list);
		$this->set('page', isset($this->params['named']['page']) ? $this->params['named']['page'] : 1);
		$this->set('note_public', $this->Note->public);

	}

	function add() {
		$this->set('main_title', 'ノート機能');
		$this->set('title_text', 'ノート作成');

		if($this->checkPost()) {
            //主キーが設定された場合はエラーにする
            $this->denyPrimaryKey("Note");

			if($this->Note->saveNote($this->data)) {
				$this->Session->setFlash('ノートを作成しました。');
				$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'content', 'noteID' => $this->Note->id));
			}
		}
	}

	function edit() {

		$this->set('title_text', 'ノート編集');
		$this->autoRender = false;
		$noteID = isset($this->data['Note']['NOTE_ID']) ? $this->data['Note']['NOTE_ID'] : $this->Note->noteID;
		$this->Permission->allowOwner('Note',$noteID);
		$this->Permission->allowAdmin();
        //権限チェック
		if(!$this->Permission->isAllowed($this->Note->userID)) {
			$this->Session->setFlash('権限がありません。');
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
        }
		//存在チェック
        if(empty($noteID) || !$this->Note->existsID($noteID)) {
            $this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
        }
        //トークンチェック
		if($this->checkPost()) {
			$this->Note->saveNote($this->data, true);
			$this->Session->setFlash('ノートを保存しました。');
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'content', 'noteID' => $this->Note->id));
		} else {
			$this->data = $this->Note->getNoteContent();
			$this->set('note_public', $this->data['Note']['PUBLIC']);
			$this->set('note_status', $this->data['Note']['STATUS']);
			$this->set('main_title', h($this->data['Note']['TITLE']));
		}
		$this->render('add');
	}

	function delete() {
		
        
		$this->Permission->allowOwner('Note',$this->Note->noteID);
		$this->Permission->allowAdmin();
		
		$this->autoRender = false;
		if(!$this->Permission->isAllowed($this->Note->userID)) {
			$this->Session->setFlash('権限がありません。');
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
		}else if(!$this->Note->existsID($this->Note->noteID)){
			$this->Session->setFlash('ノートの削除に失敗しました。');
			$this->redirect('/note/index');
		}else if($this->checkAjaxPost()){
			$this->Session->setFlash('ノートを削除しました。');
			$this->Note->delete($this->Note->noteID);
		}

		
	}

	function content() {
        $this->set('title_text', 'ノート内容');


		if(empty($this->Note->noteID)) {
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
		}
        //存在チェック
        if($this->Note->existsID($this->Note->noteID) == false) {
			$this->Session->setFlash('ノートがありません');
            $this->redirect("/note/index");
        }

		$note = $this->Note->find('first', array('conditions' => array('Note.NOTE_ID' => $this->Note->noteID)));
        
        //権限チェック
        if($note["Note"]["PUBLIC"] == "All") {
            $this->Permission->allowAllUser();
        }
        else if($note["Note"]["PUBLIC"] == "Followed") {
            $this->Permission->allowOwner("Note", $this->Note->noteID);
            $this->Permission->allowAdmin();
            $this->Permission->allowFollower($note["Note"]["USR_ID"]);
        } 
        else if($note["Note"]["PUBLIC"] == "OnlyMe") {
            $this->Permission->allowOwner("Note", $this->Note->noteID);
            $this->Permission->allowAdmin();
        }
        else {
            //グループ公開の場合
            $group_id = $note["Note"]["PUBLIC"];
            $this->Permission->allowOwner("Note", $this->Note->noteID);
            $this->Permission->allowAdmin();
            $this->Permission->allowGroupAdmin($group_id);
            $this->Permission->allowGroupParticipant($group_id);
        }

        $user = $this->Auth->user();
        
		if($this->Permission->isDenied($user["User"]["USR_ID"])) {
			$this->Session->setFlash('ノートを閲覧する権限がありません。');
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
		}
		$note = $this->Note->getNoteContent($this->Note->noteID);
		$this->set('note', $note);
		//編集を許可するユーザーの設定
		$isEditAuth = false;
		//許可するユーザーの初期化
		$this->Permission->allowUsers = array();
		$this->Permission->allowOwner("Note", $this->Note->noteID);
        $this->Permission->allowAdmin();
        $isEditAuth = $this->Permission->isAllowed($this->Note->userID);
        $this->set('isMyNote', $isEditAuth);
		$this->set('main_title', h($note['Note']['TITLE']));
	}

	function renderNoteContent() {
        $this->autoRender = false;

		$note = $this->Note->find('first', array('conditions' => array('Note.NOTE_ID' => $this->Note->noteID)));
        //権限チェック
        if($note["Note"]["PUBLIC"] == "All") {
            $this->Permission->allowAllUser();
        }
        else if($note["Note"]["PUBLIC"] == "Followed") {
            $this->Permission->allowOwner("Note", $this->Note->noteID);
            $this->Permission->allowAdmin();
            $this->Permission->allowFollower($note["Note"]["USR_ID"]);
        } 
        else if($note["Note"]["PUBLIC"] == "OnlyMe") {
            $this->Permission->allowOwner("Note", $this->Note->noteID);
            $this->Permission->allowAdmin();
        }
        else {
            //グループ公開の場合
            $group_id = $note["Note"]["PUBLIC"];
            $this->Permission->allowOwner("Note", $this->Note->noteID);
            $this->Permission->allowAdmin();
            $this->Permission->allowGroupAdmin($group_id);
            $this->Permission->allowGroupParticipant($group_id);
        }

		$user = $this->Auth->user();
		if($this->Permission->isDenied($user["User"]["USR_ID"])) {
			$this->Session->setFlash('ノートを閲覧する権限がありません。');
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
		}

		if(empty($this->Note->noteID)) {
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
		}

		$note = $this->Note->getNoteContent();
		if(empty($note)) {
			$this->redirect(array('plugin' => 'note', 'controller' => 'note', 'action' => 'index'));
		}

		//TODO ヘッダー出力
		echo "<style>body {font-size:12px;} p{margin:3px;} ul{margin:10px; padding: 10px;}</style>";
		echo "<script type='text/javascript'>function iframeResize(){var PageHight = document.body.scrollHeight + 30;window.parent.document.getElementById('note_content').style.height = PageHight + 'px';}window.onload = iframeResize;</script>";
		echo $note['Note']['CONTENT'];
		$this->set('main_title', h($note['Note']['TITLE']));
	}


	function upload(){
		$this->autoRender = false;
		$this->autoLayout = false;
		$user = $this->Auth->user();
		//アップロードの場合


		if($this->checkPost()){
			//画像ファイル以外
			if ($this->data['Storage']['FILE']['type']==='image/jpeg' ||
			$this->data['Storage']['FILE']['type']==='image/gif'      ||
			$this->data['Storage']['FILE']['type']==='image/pjpeg'    ||
			$this->data['Storage']['FILE']['type']=== 'image/png'     ||
			$this->data['Storage']['FILE']['type']=== 'image/x-png') {
				$error='';
				if($id = $this->Storage->Save_File('Note', $this->data, $user, $this->data['Storage']['GRP_ID'], 1, $error)){
					echo $id;
				}else{
					//失敗
					if($error == 1){
						echo "画像ファイルのサイズが大きすぎます。";
					}else{
						echo "画像のアップロードに失敗しました。";
					}
				}
			}
			else {
				echo "画像ファイル以外はアップロードできません";
				return false;
			}

		//アップロードフォーム表示
		}else{
			$this->set("m_class",'Storage');
			$this->set("grpid",$this->params['pass'][0]);
			if(isset($this->params['pass'][1])){
				echo "PHPの設定以上のファイルです。";
			}else{
				$this->render('upload');
			}
		}
	}

	function insert_image() {
		$this->autoRender = false;
		$this->autoLayout = false;
		$this->render('insert_image');
	}
}
