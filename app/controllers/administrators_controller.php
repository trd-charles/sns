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
 * 管理者用のコントローラクラス
 *
 * @author 作成者
 */
class AdministratorsController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Administrator";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Administrator",
        "User",
        'Message',
        'Timeline',
        'Join',
        'Friend',
        'Group',
        'Notice',
        'Watch',
        'Read',
        'Storage'
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
        'Session',
        'Auth',
        'Cookie'
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
        'limit' => 10,
        'order' => 'Administrator.USR_ID',
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
        if (! $this->Authority_Check()) {
            $this->Session->setFlash('管理者以外アクセスできません');
            $this->redirect("/homes");
        }
    }

    /**
     * ユーザ一覧
     *
     * ユーザ一覧を表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ユーザ管理");
        
        $user = $this->Auth->user();
        
        // (名前 or メールアドレス) or ステータス検索
        if (! empty($this->data)) {
            
            $name = $this->data['Administrator']['NAME'];
            $status = $this->data['Administrator']['STATUS'];
            
            // 検索条件のパラメータセット
            $search = array();
            $search = array(
                'NAME' => urlencode($name),
                'STATUS' => urlencode($status)
            );
        } elseif (! empty($this->passedArgs['NAME']) || ! empty($this->passedArgs['STATUS'])) {
            
            // 検索条件パラメータを引き継ぐ
            $name = $this->passedArgs['NAME'];
            $status = $this->passedArgs['STATUS'];
            
            $this->data['Administrator']['NAME'] = $name;
            $this->data['Administrator']['STATUS'] = $status;
            
            // 検索条件のパラメータセット
            $search = array();
            $search = array(
                'NAME' => urlencode($name),
                'STATUS' => urlencode($status)
            );
        }
        
        if (! empty($this->data)) {
            // NAMEとSTATUS
            if ($status == User::STATUS_ALL) {
                
                // 全ユーザ
                if (empty($name)) {
                    
                    // 初期状態
                    $list = $this->paginate();
                } else {
                    
                    // 全ユーザ、NAME検索
                    $conditions['conditions']['or']['Administrator.NAME LIKE'] = "%{$name}%";
                    $conditions['conditions']['or']['Administrator.MAIL LIKE'] = "%{$name}%";
                    
                    $this->paginate['conditions'] = $conditions['conditions'];
                    $list = $this->paginate();
                }
            } else {
                
                // NAME, STATUS検索
                $conditions['conditions']['or']['Administrator.NAME LIKE'] = "%{$name}%";
                $conditions['conditions']['or']['Administrator.MAIL LIKE'] = "%{$name}%";
                $conditions['conditions']['Administrator.STATUS'] = $status;
                
                $this->paginate['conditions'] = $conditions['conditions'];
                $list = $this->paginate();
            }
            $this->set('status', $status);
        } else {
            
            // 全てのデータ表示
            $list = $this->paginate('Administrator');
        }
        $this->set('search', isset($search) ? $search : null);
        
        // 変数セット
        $this->set("user_status", Configure::read('USER_STATUS'));
        $this->set("index_list", $list);
        $this->set("user", $user);
    }

    /**
     * ユーザ追加
     *
     * ユーザを追加する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function add()
    {
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ユーザ追加");
        
        if (isset($this->params['form']['can_y'])) {
            $this->redirect("/administrators/");
        }
        
        if ($this->checkPost()) {
            
            // 主キーが存在している場合はエラーにする
            $this->denyPrimaryKey("Administrator");
            
            // サブミットが押された場合
            // バリデーション（パスワードのチェック）
            $this->Administrator->set($this->data['Administrator']);
            $error = $this->Administrator->invalidFields(array(
                'fieldList' => 'EDIT_PASSWORD'
            ));
            
            if (! isset($error['EDIT_PASSWORD'])) {
                $this->data['Administrator']['PASSWORD'] = $this->Auth->password($this->data['Administrator']['EDIT_PASSWORD']);
            }
            
            // データベース上に同じメールアドレスがあるかどうかのチェック
            $mail = $this->Administrator->Mail_Search($this->data['Administrator']['MAIL']);
            
            // 重複登録の場合
            // 保存は行わない
            // TODO Mail_Searchの返り値が不明。
            // 定数化が必要
            if ($mail === false || $mail === 2) {
                $mail = $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                return false;
            }
            
            // データのセーブ
            $id = $this->Administrator->User_Save($this->data, null, $mail);
            if ($id != false) {
                // 成功
                $this->Session->setFlash('ユーザを保存しました');
                $this->redirect("/administrators/");
            } else {
                // 失敗
                if (! $mail) {
                    // メールアドレスが被っていた場合のバリデーションエラー
                    $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                }
                $this->Session->setFlash('ユーザ保存に失敗しました。');
            }
        }
    }

    /**
     * ユーザ編集
     *
     * ユーザを編集する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function edit()
    {
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ユーザ編集");
        
        if (isset($this->params['form']['can_y'])) {
            $this->redirect("/administrators/");
        }
        
        if ($this->checkPost()) {
            // サブミットが押された場合の表示
            // パスワードのバリデーションチェック
            if ($this->data['Administrator']['PASS_C'] == 1) {
                $error = $this->Administrator->invalidFields(array(
                    'fieldList' => 'EDIT_PASSWORD'
                ));
                
                if (! isset($error['EDIT_PASSWORD'])) {
                    $this->data['Administrator']['PASSWORD'] = $this->Auth->password($this->data['Administrator']['EDIT_PASSWORD']);
                }
            } else {
                unset($this->data['Administrator']['PASSWORD']);
                unset($this->data['Administrator']['EDIT_PASSWORD']);
            }
            
            // 存在チェック
            if ($this->User->existsID($this->data["Administrator"]["USR_ID"]) == false) {
                $this->redirect("/administrators/");
            }
            
            // かぶっているメールアドレスがあるかのチェック（自分のIDと同じものでない場合エラー）
            $mail = $this->Administrator->Mail_Search($this->data['Administrator']['MAIL'], $this->data['Administrator']['USR_ID']);
            
            // 重複登録の場合 保存は行わない
            // TODO Mail_Searchの返り値が不明。
            // 定数化が必要
            if ($mail === false || $mail === 2) {
                $mail = $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                return false;
            }
            
            // ユーザの保存
            if ($this->Administrator->User_Edit($this->data) != false) {
                // 成功
                $this->Session->setFlash('ユーザを変更しました');
                $this->redirect("/administrators/");
            } else {
                
                // 失敗
                if (! $mail) {
                    // 同じメールアドレスがあった場合の処理
                    $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                }
                
                $this->set('pass_c', $this->data['Administrator']['PASS_C']);
                $this->Session->setFlash('ユーザに変更に失敗しました。');
            }
        } else {
            
            // 初期表示
            // パスからIDを取り出す
            $id = $this->params['pass'][0];
            
            // 存在チェック
            if ($this->User->existsID($id) == false) {
                $this->redirect("/administrators/");
            }
            
            // データベースからユーザ情報を取り出しセット
            $this->data = $this->Administrator->find('first', array(
                'conditions' => array(
                    'Administrator.USR_ID' => $id
                )
            ));
            
            $this->data['Administrator']['USR_ID'] = $id;
            $this->set('pass_c', 0);
        }
    }

    /**
     * ユーザ編集
     *
     * ユーザ使用不可能の処理
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function valid()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if ($this->checkAjaxPost() == false) {
            $this->redirect("/administrators");
        }
        
        // パスから現在のステータスを取り出す
        $id = $this->params['pass'][0];
        
        // 存在チェック
        if (! $this->User->existsID($id)) {
            $this->redirect("/administrators");
        }
        
        // 一つ目のパスにIDが入っていた場合
        // 変更処理
        // $this->Administrator->Change_STAT($id);
        $w = array(
            'Watch.USR_ID' => $id
        );
        $this->Watch->create();
        $this->Watch->deleteAll($w, false, false);
        $this->Read->Read_Delete($id);
        
        $r = array(
            'Read.USR_ID' => $id
        );
        $this->Read->create();
        $this->Read->deleteAll($r, false, false);
        $this->Request->Delete_Request($id, null);
        
        // メッセージ削除
        $this->Message->Delete_All($id);
        
        // グループ参加情報を全て削除
        $this->Join->Delete_All($id);
        
        // 友人関係を全て削除
        $this->Friend->Delete_All($id);
        
        // ユーザグループ削除
        $this->Group->delete_my_group($id);
        $this->Notice->Delete_Notice($id);
        $this->Timeline->Delete_All($id, false, false);
        $this->Administrator->Delete_User($id);
        
        $list = $this->Administrator->find('first', array(
            'conditions' => array(
                'Administrator.USR_ID' => $id
            )
        ));
        $this->set("list", $list);
    }

    /**
     * ユーザ削除
     *
     * ユーザを削除する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function delete()
    {
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ユーザ削除");
        $user = $this->Auth->user();
        $id = $this->params['pass'][0];
        
        // 存在チェック
        if ($this->Administrator->existsID($id) == false) {
            $this->Session->setFlash('不正なアクセスです。');
            $this->redirect("/administrators");
        }
        
        // 有効なユーザはデータ削除できない
        $usr_stat = $this->Administrator->field('STATUS', array(
            'USR_ID' => $id
        ));
        
        if ($usr_stat == User::STATUS_ENABLED) {
            $this->Session->setFlash('有効なユーザはデータ削除できません。');
            $this->redirect("/administrators");
        }
        
        if ($this->checkPost()) {
            
            // タイムライン削除
            if ($this->data['Administrator']['COMMENT'] == 1) {
                $this->Timeline->Delete_All($id);
            } else {
                $this->Timeline->Delete_All($id, false);
            }
            
            if ($this->data['Administrator']['FILE'] == 1) {
                // ファイル削除（空に）
                $this->Administrator->Truncate_Directory($id);
                // フォルダごと削除する。
                $this->Administrator->Delete_Directory($id);
            } else {
                
                $d = array();
                $d = $this->Storage->find('all', array(
                    'fields' => array(
                        'FLE_ID'
                    ),
                    'conditions' => array(
                        'Storage.USR_ID' => $id
                    )
                ));
                
                foreach ($d as $key => $val) {
                    $params = array();
                    $params['FLE_ID'] = $val['Storage']['FLE_ID'];
                    $params['DEL_FLG'] = 1;
                    $this->Storage->create();
                    $this->Storage->save($params, false);
                }
            }
            
            // メッセージ削除
            $this->Message->Delete_All($id);
            
            // グループ参加情報を全て削除
            $this->Join->Delete_All($id);
            
            // 友人関係を全て削除
            $this->Friend->Delete_All($id);
            
            // ユーザグループ削除
            $this->Group->delete_my_group($id);
            $this->Notice->Delete_Notice($id);
            
            $w = array(
                'Watch.USR_ID' => $id
            );
            $this->Watch->create();
            $this->Watch->deleteAll($w, false, false);
            $this->Read->Read_Delete($id);
            
            $r = array(
                'Read.USR_ID' => $id
            );
            $this->Read->create();
            $this->Read->deleteAll($r, false, false);
            $this->Request->Delete_Request($id, null);
            
            // ユーザごと削除
            if ($this->data['Administrator']['COMMENT'] == 1 && $this->data['Administrator']['FILE'] == 1 && $this->data['Administrator']['Users'] == 1) {
                $this->Administrator->delete($id);
            } else {
                $this->Administrator->Delete_User($id);
            }
            
            $this->redirect(array(
                'controller' => 'administrators',
                'action' => 'index'
            ));
        }
        
        $result = $this->Group->find('all', array(
            'conditions' => array(
                'Group.USR_ID' => $id,
                'NOT' => array(
                    'Group.TYPE' => '2'
                )
            )
        ));
        
        $this->set('delete_code', Configure::read('DELETE_CODE'));
        $this->set('index_list', $result);
        $this->set('id', $id);
    }

    /**
     * ログ削除
     *
     * ログを削除する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function delete_log()
    {
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ログ削除");
        
        if ($this->checkPost()) {
            $this->Administrator->set($this->data);
            
            if ($this->Administrator->validates()) {
                
                if (empty($this->data['Administrator']['DEL_POST']) && empty($this->data['Administrator']['DEL_FILE'])) {
                    $this->Administrator->invalidate('DEL_POST', '削除対象を選択してください。');
                } else {
                    $completeFlg = false;
                    
                    if (! empty($this->data['Administrator']['DEL_POST']) && ! empty($this->data['Administrator']['DEL_FILE'])) {
                        // 投稿もファイルも削除
                        $del_kind = array(
                            1,
                            4
                        );
                    } elseif (! empty($this->data['Administrator']['DEL_POST']) && empty($this->data['Administrator']['DEL_FILE'])) {
                        // 投稿だけ削除
                        $del_kind = 1;
                    } elseif (! empty($this->data['Administrator']['DEL_POST']) && empty($this->data['Administrator']['DEL_FILE'])) {
                        // ファイルだけ削除
                        $del_kind = 4;
                    }
                    
                    // okだったら
                    if (! empty($this->data['Span']['month']) && ! empty($this->data['Span']['day']) && ! empty($this->data['Span']['year'])) {
                        
                        $del_span = date("Y-m-d H:i:s", mktime(23, 59, 59, $this->data['Span']['month'], $this->data['Span']['day'], $this->data['Span']['year']));
                        $dbo = $this->Timeline->getDataSource();
                        $subQuery = $dbo->buildStatement(array(
                            'fields' => array(
                                'DelTimeline.TML_ID'
                            ),
                            'table' => $dbo->fullTableName($this->Timeline),
                            'alias' => 'DelTimeline', // DBに別名
                            'limit' => null,
                            'offset' => null,
                            'joins' => array(),
                            'order' => '',
                            'group' => null,
                            'conditions' => array(
                                'DelTimeline.ACT_ID' => $del_kind,
                                'DelTimeline.INSERT_DATE <= ' => $del_span
                            )
                        ), $this->Timeline);
                        
                        $conditions = array();
                        $conditions['or'][] = array(
                            'Timeline.VAL_ID IN (' . $subQuery . ')',
                            'Timeline.ACT_ID ' => 2
                        );
                        $conditions['or'][] = array(
                            'Timeline.TML_ID IN (' . $subQuery . ')'
                        );
                        $completeFlg = $completeFlg | $this->Timeline->deleteAll($conditions);
                    } else {
                        
                        $this->Administrator->invalidate('DEL_SPAN', '期間を設定してください。');
                        $this->set('postCount', $this->Timeline->find('count', array(
                            'conditions' => array(
                                'Timeline.ACT_ID' => 1
                            )
                        )));
                        
                        return false;
                    }
                    
                    if ($this->data['Administrator']['DEL_FILE']) {
                        
                        $dbo = $this->Timeline->getDataSource();
                        $subQuery = $dbo->buildStatement(array(
                            'fields' => array(
                                'DelTimeline.TML_ID'
                            ),
                            'table' => $dbo->fullTableName($this->Timeline),
                            'alias' => 'DelTimeline',
                            'limit' => null,
                            'offset' => null,
                            'joins' => array(),
                            'order' => '',
                            'group' => null,
                            'conditions' => array(
                                'DelTimeline.ACT_ID' => 4,
                                'DelTimeline.INSERT_DATE < ' => $del_span
                            )
                        ), $this->Timeline);
                        $conditions = array(
                            'Timeline.VAL_ID IN (' . $subQuery . ')',
                            'Timeline.ACT_ID ' => 2
                        );
                        $completeFlg = $completeFlg | $this->Timeline->deleteAll($conditions);
                        
                        $files_tl = $this->Timeline->find('all', array(
                            'fields' => array(
                                'Timeline.TML_ID',
                                'Timeline.VAL_ID',
                                'User.*'
                            ),
                            'conditions' => array(
                                'Timeline.ACT_ID' => 4,
                                'Timeline.INSERT_DATE < ' => $del_span
                            )
                        ));
                        
                        $files_st = $this->Storage->find('all', array(
                            'fields' => array(
                                'Storage.FLE_ID',
                                'User.*'
                            ),
                            'conditions' => array(
                                'Storage.LAST_UPDATE <=' => $del_span
                            )
                        ));
                        
                        $ids = array();
                        foreach ($files_tl as $file) {
                            $ids[] = $file['Timeline']['TML_ID'];
                            $this->Storage->Delete_File($file['Timeline']['VAL_ID'], $file);
                        }
                        $completeFlg = $completeFlg | $this->Timeline->deleteAll(array(
                            'Timeline.TML_ID' => $ids
                        ));
                    }
                    
                    if ($completeFlg) {
                        $this->Session->setFlash('削除しました。');
                    } else {
                        $this->Session->setFlash('削除に失敗しました。');
                    }
                }
            } else {
                
                if (empty($this->data['Administrator']['DEL_SPAN'])) {
                    $this->Administrator->invalidate('DEL_SPAN', '期間を設定してください。');
                }
                
                if (empty($this->data['Administrator']['DEL_POST']) && empty($this->data['Administrator']['DEL_FILE'])) {
                    $this->Administrator->invalidate('', '削除対象を選択してください。');
                }
            }
        }
        
        $this->set('postCount', $this->Timeline->find('count', array(
            'conditions' => array(
                'Timeline.ACT_ID' => array(
                    1,
                    4
                )
            )
        )));
    }

    /**
     * 権限変更
     *
     * ログを変更する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function role()
    {
        $this->autoRender = false;
        $this->uses = null;
        
        $type = '';
        $user = $this->Auth->user();
        
        // 一般ユーザーの場合，権限を変更させない
        if (! $this->Authority_Check()) {
            $this->Session->setFlash('権限を変更できません');
            return false;
        }
        
        // トークンチェック
        if ($this->checkAjaxPost() == false) {
            return false;
        }
        
        // ユーザーID，AUTHORITYがセットされていない場合
        if (! (isset($this->params['pass'][0]) && isset($this->params['pass'][1]))) {
            return false;
        }
        $userid = $this->params['pass'][0];
        
        // 存在チェック
        if ($this->User->existsID($userid) == false) {
            $this->Session->setFlash('存在しないユーザが指定されました。権限を変更できません。');
            return false;
        }
        
        if ($this->params['pass'][1]) {
            $type = $this->params['pass'][1];
        }
        
        $user_data = $this->User->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $userid
            )
        ));
        
        $administrators = count($this->User->find('all', array(
            'conditions' => array(
                'User.AUTHORITY' => User::AUTHORITY_TRUE
            )
        )));
        
        // 一般ユーザーの場合,管理者に変更
        // ステータスが有効以外の場合，権限を変更させない
        if ($user_data['User']['STATUS'] != User::STATUS_ENABLED) {
            $this->Session->setFlash('権限を変更できません');
            return false;
        }
        
        if ($type == 0) {
            
            // 管理者が1人の場合，一般ユーザーに変更させない
            if ($administrators < 2 || ! $this->Authority_Check()) {
                $this->Session->setFlash('権限を変更できません');
                return false;
            }
            
            $auth = User::AUTHORITY_TRUE;
        } elseif ($type == 1) {
            
            // 管理者の場合,一般ユーザーに変更
            $auth = User::AUTHORITY_FALSE;
        }
        
        $this->User->User_Regist($user_data, $auth, true, false, false);
    }
}
