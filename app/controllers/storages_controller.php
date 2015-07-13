<?php

/**
 * Matcha-SNS
 *
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author
 *
 * @version $Id$
 */
/**
 * ファイル管理用のコントローラクラス
 *
 * @author 作成者
 */
class StoragesController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Storage";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Storage",
        "User",
        'Group'
    );

    /**
     * 自動レンダリングをするかどうか指定
     *
     * @var boolean
     * @access public
     */
    public $autoLayout = true;

    /**
     * コンポーネントを指定
     *
     * @var array
     * @access public
     */
    public $components = array(
        "Permission"
    );

    /**
     * ページネーションの初期設定
     *
     * @var array
     * @access public
     */
    public $paginate = array(
        'page' => 1,
        'conditions' => array(),
        'sort' => '',
        'limit' => 20,
        'order' => 'Storage.FLE_ID DESC',
        'recursive' => 0
    );

    /**
     * コントローラのアクション前に実行
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * ファイル一覧
     *
     * ファイル一覧を表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        // 初期化
        $this->set("main_title", "ファイル");
        $this->set("title_text", "ファイル一覧");
        
        $user = $this->Auth->user();
        
        $conditions = array();
        // トークンチェキ & 検索キーワードチェック
        if (isset($this->passedArgs['NAME'])) {
            
            /* 検索単語をGetデータより取得 */
            $this->data['Storage']['NAME'] = $this->passedArgs['NAME'];
        }
        
        if ($this->checkPost() && $this->data['Storage']['NAME'] != null) {
            
            // 条件が指定されている場合
            if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
                
                $conditions = $this->Storage->Search_File($user['User']['USR_ID'], $this->data['Storage']['NAME'], 'all');
            } else {
                
                $conditions = $this->Storage->Search_File($user['User']['USR_ID'], $this->data['Storage']['NAME']);
            }
            
            $this->set("keyword", $this->data['Storage']['NAME']);
        } else {
            
            if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
                
                // すべてのファイルを取得
                $conditions = $this->Storage->Get_File($user['User']['USR_ID'], true, $user);
            } else {
                
                // ログインしているユーザのファイルを取得
                $conditions = $this->Storage->Get_File($user['User']['USR_ID']);
            }
        }
        // ファイル情報を取得
        $list = $this->paginate($conditions);
        
        // 変数のセット
        $this->set("list", $list);
        $this->set("groupid", $user['User']['GRP_ID']);
        $this->set("userid", $user['User']['GRP_ID']);
        $this->set("m_class", 'Storage');
        $this->set("files_status", Configure::read('FILES_STATUS'));
    }

    /**
     * ファイルダウンロード
     *
     * ファイルをダウンロードする
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function download()
    {
        // 初期化
        $this->view = 'Media';
        $user = $this->Auth->user();
        
        // パスからIDの取得
        if (isset($this->params['pass'][0])) {
            
            $rand_name = $this->params['pass'][0];
        } else {
            
            $this->redirect('../homes');
        }
        // ファイル情報の取得
        $result = $this->Storage->find('first', array(
            'conditions' => array(
                'RAND_NAME' => $rand_name
            )
        ));
        if ($result == null) {
            $this->redirect('../homes');
        }
        
        // ファイルの権限チェック
        $this->Permission->allowAdmin();
        $this->Permission->allowOwner("Storage", $result["Storage"]["FLE_ID"]);
        
        // 管理者の場合は全てをダウンロード可能
        if ($this->Permission->isDenied($user['User']['USR_ID'])) {
            
            if ($result['Storage']['PUBLIC'] == Storage::STATUS_PRIVATE) {
                
                $this->Session->setFlash('そのファイルはダウンロード出来ません。');
                $this->redirect('index');
            }
        }
        
        // パスの取得
        $path = APP . "files/user/" . $result['User']['DIRECTORY1'] . "/" . $result['User']['DIRECTORY2'] . "/storage/";
        
        if ($result['Storage']['EXTENSION'] != NULL) {
            // データの整理
            $id = $result['Storage']['RAND_NAME']; // ファイル名
            $name = $result['Storage']['ORIGINAL_NAME'];
            
            if (ereg("MSIE", getenv("HTTP_USER_AGENT"))) {
                
                $name = mb_convert_encoding($name, "SJIS-win", "UTF-8");
            }
            
            $pos = strrpos($name, '.');
            $extension = substr($name, $pos + 1);
            $name = preg_replace("/.[^.]+$/", "", $name);
            
            $mimeType = array(
                $extension => $result['Storage']['F_TYPE']
            );
            // ダウンロードさせるためのヘッダの出力有無
            $download = true;
            
            // データのセット
            $this->set(compact('path', 'id', 'name', 'extension', 'download', 'mimeType'));
        } else {
            
            $name = $result['Storage']['ORIGINAL_NAME'];
            
            if (ereg("MSIE", getenv("HTTP_USER_AGENT"))) {
                
                $name = mb_convert_encoding($name, "SJIS-win", "UTF-8");
            }
            
            $path = $path . $result['Storage']['RAND_NAME'];
            header('Content-Disposition: attachment; filename="' . $name . '"');
            header('Content-Type: application/octet-stream');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($path));
            readfile($path);
        }
    }

    /**
     * 画像ファイルプレビュー
     *
     * 画像ファイルをプレビューする
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function preview()
    {
        // 初期化
        $this->view = 'Media';
        $user = $this->Auth->user();
        
        // パスからIDの取得
        if (isset($this->params['pass'][0])) {
            
            $id = $this->params['pass'][0];
        } else {
            $this->redirect('../homes');
        }
        
        // ファイル情報の取得
        $result = $this->Storage->find('first', array(
            'conditions' => array(
                'RAND_NAME' => $id
            )
        ));
        
        if ($result == null) {
            $this->redirect('../homes');
        }
        
        // ファイルの権限チェック
        $this->Permission->allowAdmin();
        $this->Permission->allowOwner("Storage", $result["Storage"]["FLE_ID"]);
        
        // 管理者の場合は閲覧可能
        if ($this->Permission->isDenied($user['User']['USR_ID'])) {
            
            if ($result['Storage']['PUBLIC'] == Storage::STATUS_PRIVATE) {
                $this->Session->setFlash('ファイルに権限がありません');
                $this->redirect('index');
            }
        }
        
        if (isset($this->params['pass'][1])) {
            
            // パスがある場合にはサムネイルの表示
            $id = $result['Storage']['RAND_NAME'] . '_thum'; // ファイル名
        } else {
            
            // ない場合にはプレビュー画像の表示
            $id = $result['Storage']['RAND_NAME'] . '_pre'; // ファイル名
        }
        
        // パスの取得
        $path = APP . "files/user/" . $result['User']['DIRECTORY1'] . "/" . $result['User']['DIRECTORY2'] . "/storage/";
        // 情報の整理
        $name = $result['Storage']['ORIGINAL_NAME'];
        
        if (ereg("MSIE", getenv("HTTP_USER_AGENT"))) {
            
            $name = mb_convert_encoding($name, "SJIS-win", "UTF-8");
        }
        
        // ユーザーに送信する拡張子名
        $extension = "jpg";
        
        // ダウンロードさせるためのヘッダの出力有無
        $download = false;
        
        $this->set(compact('path', 'id', 'name', 'extension', 'download'));
    }

    /**
     * ユーザのサムネイル画像
     *
     * ユーザのサムネイル画像を表示
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function thumbnail()
    {
        // 初期化
        $this->view = 'Media';
        $user = $this->Auth->user();
        
        // パスからユーザIDを取得
        if (isset($this->params['pass'][0])) {
            
            $userid = $this->params['pass'][0];
        } else {
            
            $this->redirect('../homes');
        }
        
        // ユーザ情報の取得
        $result = $this->User->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $userid
            )
        ));
        
        if ($result == null) {
            $this->redirect('../homes');
        }
        // ユーザが削除フラグがある場合は管理者ユーザのみアクセス可能
        $this->Permission->allowAdmin();
        if ($result['User']['STATUS'] == User::STATUS_WITHDRAWN) {
            
            if ($this->Permission->isDenied($user['User']['USR_ID'])) {
                
                $this->redirect('../homes');
            }
        }
        // ファイルの置いてあるディレクトリへのパス
        $path = APP . "files/user/" . $result['User']['DIRECTORY1'] . "/" . $result['User']['DIRECTORY2'] . "/thumbnail/";
        
        if (isset($this->params['pass'][1]) && $this->params['pass'][1] == 'pre') {
            
            // パスがあり、preである場合、プレビュー画像
            $id = 'preview'; // ファイル名
        } elseif (isset($this->params['pass'][1]) && $this->params['pass'][1] == 'ori') {
            
            // パスがあり、oriである場合、プレビュー画像
            $id = 'original'; // ファイル名
        } else {
            
            // サムネイルの小さい画像
            $id = 'thumbnail'; // ファイル名
        }
        
        // 情報の整理
        // ユーザーに送信するファイル名（拡張子を除く）
        $name = $result['User']['THUMBNAIL'];
        
        if (ereg("MSIE", getenv("HTTP_USER_AGENT"))) {
            
            $name = mb_convert_encoding($name, "SJIS-win", "UTF-8");
        }
        
        // ユーザーに送信する拡張子名
        $extension = "jpg";
        
        // ダウンロードさせるためのヘッダの出力有無
        $download = false;
        $this->set(compact('path', 'id', 'name', 'extension', 'download'));
    }

    /**
     * グループのサムネイル画像
     *
     * グループのサムネイル画像を表示
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function group_thumbnail()
    {
        // 初期化
        $this->view = 'Media';
        $user = $this->Auth->user();
        
        // パスからグループIDの取得
        if (isset($this->params['pass'][0])) {
            
            $grpid = $this->params['pass'][0];
        } else {
            
            $this->redirect('../homes');
        }
        
        // グループ情報の取得
        $result = $this->Group->find('first', array(
            'conditions' => array(
                'Group.GRP_ID' => $grpid,
                'NOT' => array(
                    'Group.TYPE' => Group::TYPE_PERSONAL
                )
            )
        ));
        
        if ($result == null) {
            
            $this->redirect('../homes');
        }

        // ファイルの置いてあるディレクトリへのパス
        $path = APP . "files/group/" . $result['Group']['DIRECTORY1'] . "/" . $result['Group']['DIRECTORY2'] . "/thumbnail/";
        
        if (isset($this->params['pass'][1]) && $this->params['pass'][1] == 'pre') {
            
            // プレビュー
            $id = 'preview'; // ファイル名
        } elseif (isset($this->params['pass'][1]) && $this->params['pass'][1] == 'ori') {
            
            // パスがあり、oriである場合、プレビュー画像
            $id = 'original'; // ファイル名
        } else {
            
            // サムネイル
            $id = 'thumbnail'; // ファイル名
        }
        // 情報の整理
        $name = $result['Group']['THUMBNAIL']; // ユーザーに送信するファイル名（拡張子を除く）
        if (ereg("MSIE", getenv("HTTP_USER_AGENT"))) {
            
            $name = mb_convert_encoding($name, "SJIS-win", "UTF-8");
        }
        
        // ユーザーに送信する拡張子名
        $extension = "jpg";
        
        // ダウンロードさせるためのヘッダの出力有無
        $download = false;
        
        $this->set(compact('path', 'id', 'name', 'extension', 'download'));
    }

    /**
     * ファイル削除
     *
     * ファイルを削除する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function delete()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        if ($this->checkAjaxPost()) {
            
            // POSTの処理
            $user = $this->Auth->user();
            
            // IDの取得
            if (isset($this->params['pass'][0]) && $this->params['pass'][0] != null) {
                
                $fleid = $this->params['pass'][0];
                
                // ファイルの所有者、管理者かどうかの確認
                $this->Permission->allowOwner("Storage", $fleid);
                $this->Permission->allowAdmin();
                
                // 所有権がない場合
                if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                    
                    $this->Session->setFlash('不正な操作が行われました。');
                    $this->redirect('index');
                }
            } else {
                $this->Session->setFlash('不正な操作が行われました。');
                $this->redirect('index');
            }
        }
        
        // ファイルの論理削除
        return $this->Storage->LogicalDelete($fleid, $user);
    }

    /**
     * ファイルアップロード
     *
     * ファイルをアップロード
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function fileup()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if ($this->checkPost()) {
            
            // 主キーがある場合はエラーにする
            $this->denyPrimaryKey("Storage");
            $group_id = $this->data['Storage']['GRP_ID'];
            
            // グループ用ファイルアップの場合
            if ($this->data['Storage']['M_CLASS'] == "Group") {
                
                // グループ権限チェキ
                if ($this->Group->existsID($group_id) == false) {
                    
                    $this->Session->setFlash('不正な操作が行われました。');
                    $this->redirect('/storages');
                }
                
                $this->Permission->allowGroupAdmin($group_id);
                $this->Permission->allowGroupParticipant($group_id);
                $this->Permission->allowAdmin();
                
                if ($this->Permission->isDenied($user['User']['USR_ID'])) {
                    
                    $this->Session->setFlash('不正な操作が行われました。');
                    $this->redirect('/storages');
                }
            }
            
            // ファイルの保存
            $error = "";
            if ($this->Storage->Save_File($this->data['Storage']['M_CLASS'], $this->data, $user, $group_id, 1, $error)) {
                
                // 成功
                echo "true";
                exit();
            } else {
                
                // 失敗
                if ($error == 1) {
                    
                    echo "１ファイルあたり100MBまでです。";
                } else {
                    
                    echo "ファイルのアップロードに失敗しました。";
                }
            }
        } else {
            
            // 初期表示
            $this->set("m_class", 'Storage');
            $this->set("grpid", $this->params['pass'][0]);
            
            if (isset($this->params['pass'][1])) {
                
                echo "PHPの設定以上のファイルです。";
            } else {
                
                $this->render('../elements/file', false);
            }
        }
    }

    /**
     * ステータス変更
     *
     * 公開・非公開のステータスを変更する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function c_public()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // トークンチェック
        if ($this->checkAjaxPost() == false) {
            
            $this->redirect("/administrators");
        }
        
        // URLから公開非公開を取得
        if (isset($this->params['pass'][0])) {
            
            $fleid = $this->params['pass'][0];
        } else {
            
            $this->Session->setFlash('不正な操作が行われました。');
            $this->redirect('/storages');
        }
        
        $this->Permission->allowOwner("Storage", $fleid);
        $this->Permission->allowAdmin();
        
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            
            $this->Session->setFlash('不正な操作が行われました。');
            $this->redirect('/storages');
        }
        
        // 公開の変更
        $result = $this->Storage->Change_Public($fleid);
        
        if ($result === false) {
            
            // 失敗
            $this->redirect("index");
        }
        
        // 変数のセット
        $this->set('public', $result['Storage']['PUBLIC']);
        $this->set('fleid', $fleid);
        $this->set("files_status", Configure::read('FILES_STATUS'));
        
        // 描画するviewの指定
        $this->render('public', false);
    }
}
