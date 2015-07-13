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
 * グループ用のコントローラクラス
 *
 * @author 作成者
 */
class GroupsController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Group";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Group",
        "Timeline",
        "User",
        "Join",
        'Read',
        'Watch',
        'Administrator'
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
        'Permission'
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
        'order' => 'Timeline.LAST_DATE DESC',
        'recursive' => 0
    );

    /**
     * グループ一覧
     *
     * グループ一覧を表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        // 初期化
        $list = array();
        
        $this->set("main_title", "グループ");
        $this->set("title_text", "グループ一覧");
        
        $user = $this->Auth->User();
        $msg_flag = 0;
        
        if (isset($this->passedArgs['NAME'])) {
            
            /* 検索単語をGetデータより取得 */
            $this->data['Group']['NAME'] = $this->passedArgs['NAME'];
        }
        
        // 条件処理
        if ($this->checkPost() && ($this->data['Group']['NAME'] != null)) {
            
            // 条件が指定されている場合
            if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
                $conditions = $this->Group->Search_Group($user['User']['USR_ID'], $this->data['Group']['NAME'], array(
                    0,
                    1,
                    2,
                    3,
                    4,
                    8
                ));
            } else {
                $conditions = $this->Group->Search_Group($user['User']['USR_ID'], $this->data['Group']['NAME'], array(
                    0,
                    1,
                    2
                ));
            }
            $this->set("keyword", $this->data['Group']['NAME']);
        } else {
            
            // 何も指定されていない場合
            if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'all') {
                $conditions = $this->Group->Search_Group($user['User']['USR_ID'], null, array(
                    0,
                    1,
                    2,
                    3,
                    4,
                    8
                ));
            } else {
                $conditions = $this->Group->Search_Group($user['User']['USR_ID'], null, array(
                    0,
                    1,
                    2
                ));
                $msg_flag = 1;
            }
        }
        
        // ページンに条件を指定
        $this->paginate = $conditions;
        
        // グループ情報を取得
        $list = $this->paginate('Group');
        
        // まだグループがない場合
        if (count($list) == 0 && empty($this->params['pass'][0]) && $msg_flag == 1) {
            $this->Session->setFlash('まだグループに参加していません。「すべてのグループ」タブからグループに参加するか、「グループ作成」からグループを作成してみましょう。', 'default', array(
                'class' => 'help'
            ));
        }
        
        // グループに参加していない場合の処理
        foreach ($list as $key => $val) {
            if ($val['Join']['STATUS'] == NULL) {
                $list[$key]['Join']['STATUS'] = Join::STATUS_NOT_JOINED;
            }
        }
        
        // 変数をセット
        $this->set("index_list", $list);
        $this->set("group_status", Configure::read('GROUP_JOIN_STATUS'));
        $this->set("group_type", Configure::read('GROUP_STATUS'));
    }

    /**
     * グループ作成
     *
     * グループを作成する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function create()
    {
        // 初期化
        $this->set("main_title", "グループ");
        $this->set("title_text", "グループ");
        
        $user = $this->Auth->User();
        
        // POSTの場合
        if ($this->checkPost()) {
            
            // 主キーがある場合はエラーにする
            $this->denyPrimaryKey("Group");
            
            // グループの作成
            if ($id = $this->Group->Create_Group($this->data, $user['User']['USR_ID'])) {
                // 成功
                $this->Session->setFlash('グループを作成しました。');
                $this->redirect("main/" . $id);
            } else {
                $this->set('type', $this->data['Group']['TYPE']);
            }
        } else {
            $this->set('type', 0);
        }
        
        // 変数のセット
        $this->set("group_type", Configure::read('GROUP_STATUS'));
    }

    /**
     * グループのタイムライン
     *
     * グループのタイムラインを表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function main()
    {
        // 初期化
        $list = array();
        
        $this->set("main_title", "グループ");
        $this->set("title_text", "グループタイムライン");
        
        $user = $this->Auth->user();
        
        $mon = date("m");
        $year = date("Y");
        $day = date("d");
        $calender = array(
            "mon" => $mon,
            "year" => $year,
            "day" => $day
        );
        
        $this->Session->write('calender', - 1);
        
        $select = array(
            'order' => 1,
            'while' => 1
        );
        $select = $this->Session->write('select');
        
        $group_auth = false; /* コメントをする権限 */
        
        // パスからグループIDを取得
        if (isset($this->params['pass'][0])) {
            $group_id = $this->params['pass'][0];
        } else {
            $this->redirect("/groups");
        }
        
        if (! $this->Group->existsID($group_id)) {
            $this->redirect('/groups');
        }
        
        // 投稿(POST)の場合
        if ($this->checkPost()) {
            
            // 存在チェック
            $this->Timeline->validates();
            
            if ($this->requestAction('/timelines/message')) {
                
                // 成功した場合、ページを再表示させる
                $this->redirect(Router::url(null, true));
            }
        }
        
        // グループの情報を取得
        $group = $this->Group->find('first', array(
            'conditions' => array(
                'Group.GRP_ID' => $group_id
            )
        ));
        
        // グループ編集表示権限
        $this->Permission->allowGroupAdmin($group_id);
        $this->Permission->allowAdmin();
        
        $edit_auth = $this->Permission->isAllowed($user['User']['USR_ID']);
        
        // 閲覧権限のチェック
        $this->Permission->allowGroupParticipant($group_id);
        
        if ($group['Group']['TYPE'] == Group::TYPE_PRIVATE) {
            if ($this->Permission->isDenied($user['User']['USR_ID'])) {
                $this->Session->setFlash('グループを閲覧する権限がありません。');
                $this->redirect("/groups");
            }
        } elseif ($group['Group']['TYPE'] == Group::TYPE_PERSONAL) {
            $this->Session->setFlash('グループを閲覧する権限がありません。');
            $this->redirect("/groups");
        }
        
        // 投稿権限
        $group_auth = $this->Permission->isAllowed($user['User']['USR_ID']);
        
        // タイムライン条件の取得
        $conditions = $this->Group->Get_Timeline($user, $group_id);
        
        // タイムラインの取得
        $list = $this->paginate('Timeline', $conditions, array());
        
        // コメントの検索
        if ($this->Timeline->Comment_Search($list)) {
            $this->set("first", $list[0]['Timeline']['TML_ID']);
            $this->set("lastid", $list[count($list) - 1]['Timeline']['TML_ID']);
            $this->set("lastdate", $list[count($list) - 1]['Timeline']['LAST_DATE']);
        } else {
            $this->set("first", 0);
        }
        
        // グループに参加しているユーザの取得
        $join_user = $this->Join->Group_User($group_id, 6);
        $join_user_num = count($this->Join->Group_User($group_id, null));
        
        // 読んだが押してあるかどうかの確認
        $this->Read->Read_Search($list, $user);
        
        // ウォッチリストに登録してあるかの確認
        $this->Watch->Watch_Search($list, $user);
        
        // カレンダー日付にデータがあるか検証
        for ($i = 1; $i < $this->month_days($calender['mon'], $calender['year']) + 1; $i ++) {
            $conditions_tmp = array_merge($conditions, array(
                'and' => array(
                    'Timeline.INSERT_DATE LIKE' => "%" . $calender['year'] . "-" . $calender['mon'] . "-" . sprintf('%02d', $i) . "%"
                )
            ));
            $is_data[$i] = $this->Timeline->find('first', array(
                'conditions' => $conditions_tmp
            ));
        }
        
        $join = $this->Join->Search_User($group_id, $user['User']['USR_ID']);
        
        // グループの管理者ユーザーをJoinから取得
        $group_admin_id = $this->Join->Get_Group_Admin($group_id);
        $group_admin_name = $this->User->find('first', array(
            'fields' => array(
                'NAME'
            ),
            'conditions' => array(
                'User.USR_ID' => $group_admin_id['Join']['USR_ID']
            )
        ));
        
        // 変数のセット
        $this->set("group_status", Configure::read('GROUP_JOIN_STATUS'));
        $this->set("join", $join);
        $this->set("select", array(
            'order' => - 1,
            'while' => - 1
        ));
        $this->set("date_frag", 1);
        $this->set("join_user", $join_user);
        $this->set("join_user_num", $join_user_num);
        $this->set("edit_auth", $edit_auth);
        $this->set("g_auth", $group_auth);
        $this->set("group", $group);
        $this->set("list", $list);
        $this->set("groupid", $group_id);
        $this->set("m_class", 'Group');
        $this->set("tab_name", null);
        $this->set("calender", $calender);
        $this->set("is_data", $is_data);
        $this->set("grpid", $group['Group']['GRP_ID']);
        $this->set("group_admin_name", $group_admin_name);
    }

    /**
     * グループへの参加
     *
     * グループへ参加する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function join()
    {
        // 初期化
        $this->autoRender = false;
        
        $user = $this->Auth->user();
        
        // トークンチェック
        $this->checkAjaxPost();
        
        // パスからIDの取得
        if (isset($this->params['pass'][0]) && $this->params['pass'][0]) {
            $group_id = $this->params['pass'][0];
        }
        
        // グループへ参加等の処理
        if ($user['User']['AUTHORITY'] != User::AUTHORITY_TRUE) {
            $result = $this->Join->Join_User($group_id, $user['User']['USR_ID']);
        } else {
            $result = $this->Join->Join_User($group_id, $user['User']['USR_ID'], null, true);
        }
        
        if ($result == Join::STATUS_WAITING) {
            $this->set("result", 1);
        } elseif ($result == Join::STATUS_JOINED) {
            $this->set("result", 2);
        } else {
            // グループから抜けた場合
            $this->set("result", 3);
        }
        
        // 変数のセット
        $this->set("groupid", $group_id);
        $this->set("group_status", Configure::read('GROUP_JOIN_STATUS'));
        
        // 描画するviewの指定
        $this->render('join', false);
    }

    /**
     * グループの編集
     *
     * グループを編集する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function edit()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->User();
        
        $this->set("main_title", "グループ");
        $this->set("title_text", "グループ編集");
        
        $error = array();
        
        // POSTの場合
        if ($this->checkPost()) {
            
            // データの存在チェック
            $group_id = $this->data["Group"]["GRP_ID"];
            $grp_admin_id = $this->data["Group"]["USR_ID"];
            
            if (! $this->Group->existsID($group_id) || ! $this->User->existsID($grp_admin_id)) {
                $this->redirect('/groups');
            }
            
            // 権限のあるユーザを登録
            $this->Permission->allowGroupAdmin($group_id);
            $this->Permission->allowAdmin();
            
            // 権限チェック
            if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                $this->Session->setFlash('権限がありません');
                $this->redirect("/groups");
            }
            
            // グループの保存
            if ($this->Group->Create_Group($this->data, $user['User']['USR_ID'], $error)) {
                // 成功
                return true;
            } else {
                // 失敗
                // バリデーションエラーを書き込み
                $this->Profile->validationErrors = $error;
                $us = $this->User->find('first', array(
                    'conditions' => array(
                        'User.USR_ID' => $this->data['Group']['USR_ID']
                    )
                ));
                $this->set('names', $us['User']['NAME']);
                $this->set('ids', $us['User']['USR_ID']);
            }
        } else {
            if (isset($this->params['pass'][0]) && $this->Group->existsID($this->params['pass'][0])) {
                $group_id = $this->params['pass'][0];
            } else {
                $this->redirect("/groups");
            }
            
            // 権限のあるユーザを登録
            $this->Permission->allowGroupAdmin($group_id);
            $this->Permission->allowAdmin();
            
            // 権限チェック
            if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                $this->Session->setFlash('権限がありません');
                $this->redirect('/groups');
            }
            
            // 初期表示
            // 現在のデータを取得
            $this->data = $this->Group->find('first', array(
                'conditions' => array(
                    'Group.GRP_ID' => $group_id
                )
            ));
            $this->set('names', $this->data['User']['NAME']);
            $this->set('ids', $this->data['User']['USR_ID']);
        }
        
        // 変数のセット
        $this->set("group_type", Configure::read('GROUP_STATUS'));
        $this->set("edit", "edit");
        
        // 描画するviewの指定
        $this->render('edit', false);
    }

    /**
     * サムネイル画像変更
     *
     * サムネイル画像を変更する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function image()
    {
        $user = $this->Auth->user();
        
        // サブミットが押された場合
        if ($this->checkPost()) {
            $group_id = $this->data['Group']['GRP_ID'];
            if (! $this->Group->existsID($group_id)) {
                $this->redirect('/groups');
            }
            
            // 権限チェック
            $this->Permission->allowGroupAdmin($group_id);
            $this->Permission->allowAdmin();
            
            if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                $this->Session->setFlash('権限がありません');
                $this->redirect('/groups');
            }
            
            // 現在のグループ情報を取得
            $group = $this->Group->find('first', array(
                'conditions' => array(
                    'Group.GRP_ID' => $group_id
                )
            ));
            
            // 画像の保存
            if ($this->Group->Save_Image($this->data, $group, 'edit')) {
                // 成功
                echo 'true';
                exit();
            } else {
                // 失敗
                echo "画像を変更に失敗しました";
                exit();
            }
        } else {
            
            // パスからグループIDを取得
            if (isset($this->params['pass'][0])) {
                $group_id = $this->params['pass'][0];
            } else {
                $this->redirect('/groups');
            }
            
            // 権限チェック
            $this->Permission->allowGroupAdmin($group_id);
            $this->Permission->allowAdmin();
            if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                $this->Session->setFlash('権限がありません');
                $this->redirect('/groups');
            }
            
            // 初期表示
            // 変数のセット
            $this->set("grpid", $group_id);
            
            // 描画するviewの指定
            $this->render('../elements/image', false);
        }
    }

    /**
     * 参加グループ一覧
     *
     * 参加しているグループ一覧を取得する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function join_user()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        // パスからユーザIDの取得
        if (isset($this->params['pass'][0]) && $this->params['pass'][0]) {
            $group_id = $this->params['pass'][0];
        }
        if (isset($this->params['pass'][1]) && $this->params['pass'][1]) {
            $this->set("own", $this->params['pass'][1]);
        }
        
        $joins = array(
            0 => array(
                'type' => 'LEFT',
                'table' => 'T_USER',
                'alias' => 'Administrator',
                'conditions' => 'Administrator.USR_ID = Join.USR_ID',
                'fields' => ''
            )
        );
        
        $this->paginate = array(
            'Join' => array(
                "fields" => array(
                    'Join.*',
                    'Administrator.*',
                    'Group.USR_ID'
                ),
                'joins' => $joins,
                'limit' => 10,
                'order' => 'User.USR_ID',
                'conditions' => array(
                    'Join.GRP_ID' => $group_id,
                    'Join.STATUS' => array(
                        Join::STATUS_ADMINISTRATOR,
                        Join::STATUS_JOINED
                    )
                )
            )
        );
        
        $list = $this->paginate('Join');
        
        $this->set("list", $list);
        $this->set("grpid", $group_id);
        $this->render('../group/join_users', false);
    }

    /**
     * 参加ユーザグループ取得
     *
     * 参加しているユーザグループを取得する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function join_group()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        // パスからユーザIDの取得
        if (isset($this->params['pass'][0]) && $this->params['pass'][0]) {
            $userid = $this->params['pass'][0];
        }
        
        // ページングに条件を指定
        $this->paginate = $this->Group->Search_Group($userid, null, array(
            0,
            1,
            2
        ));
        
        // 参加しているグループの取得
        $group = $this->paginate('Group');
        $name = $this->User->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $userid
            ),
            'fields' => array(
                'User.NAME'
            )
        ));
        
        // 変数をセット
        $this->set("name", $name['User']['NAME']);
        $this->set("list", $group);
        
        // 描画するviewの指定
        $this->render('../elements/group_pop', false);
    }

    /**
     * グループへ招待
     *
     * グループへ招待する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function invite()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // パスの取得
        if (isset($this->params['pass'][0])) {
            $group_id = $this->params['pass'][0];
        } else {
            $this->redirect('/groups');
        }
        
        if (! $this->Group->existsID($group_id)) {
            $this->redirect('/groups');
        }
        
        // サブミットが押された場合
        if ($this->checkPost()) {
            $ret = 0;
            for ($i = 1; $i < 6; $i ++) {
                if ($this->data['Group']['ID_' . $i] != null) {
                    $ret = 1;
                }
            }
            
            if ($ret != 0) {
                echo $this->Group->invite($group_id, $this->data['Group']);
            } else {
                // 入力がなかった場合
                echo 3;
            }
            
            return;
        } else {
            
            // 権限チェック
            $this->Permission->allowGroupAdmin($group_id);
            $this->Permission->allowAdmin();
            if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                $this->Session->setFlash('権限がありません');
                $this->redirect('/groups');
            }
        }
        
        // 変数のセット
        $this->set("groupid", $group_id);
        $this->render('invite', false);
    }

    /**
     * グループ招待中ユーザ一覧
     *
     * グループへ招待中のユーザー一覧を表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function invite_user()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        $conditions = array();
        
        if (isset($this->params['pass'][0])) {
            $id = $this->params['pass'][0];
        } else {
            $this->redirect(array(
                'controller' => 'homes'
            ));
        }
        
        if (isset($this->params['pass'][1])) {
            $this->set('no', $this->params['pass'][1]);
        } else {
            $this->redirect(array(
                'controller' => 'homes'
            ));
        }
        
        $join = $this->Join->find('all', array(
            'conditions' => array(
                'Join.GRP_ID' => $id
            ),
            'fields' => array(
                'Join.USR_ID'
            )
        ));
        
        $join_or = array();
        foreach ($join as $key => $val) {
            $join_or[$key] = $val['Join']['USR_ID'];
        }
        
        // すでに選択したユーザ
        for ($i = 1; $i <= 5; $i ++) {
            if (! empty($this->params['form']['id' . $i])) {
                array_push($join_or, $this->params['form']['id' . $i]);
            }
        }
        
        // ページング情報
        $this->paginate = array(
            'User' => array(
                "fields" => array(
                    'User.*'
                ),
                'limit' => 10,
                'order' => 'User.USR_ID',
                'conditions' => array(
                    'NOT' => array(
                        'User.USR_ID' => $join_or
                    ),
                    'AND' => array(
                        'User.STATUS' => User::STATUS_ENABLED
                    )
                )
            )
        );
        
        // ユーザの取得
        $list = $this->paginate('User');
        
        // 変数のセット
        $this->set('groupid', $id);
        $this->set("list", $list);
        $this->render('invite_user', false);
    }

    /**
     * グループ削除
     *
     * グループを削除する
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
        
        $user = $this->Auth->user();
        
        if (isset($this->params['pass'][0])) {
            $group_id = $this->params['pass'][0];
            if (! $this->Group->existsID($group_id)) {
                $this->redirect('/groups');
            }
        } else {
            $this->Session->setFlash('不正なアクセスがされました。');
            $this->redirect('/groups');
        }
        
        // トークンチェック
        $this->checkAjaxPost();
        
        // 権限のあるユーザを登録
        $this->Permission->allowGroupAdmin($group_id);
        $this->Permission->allowAdmin();
        
        // 権限チェック
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            $this->Session->setFlash('権限がありません');
            $this->redirect('/groups');
        }
        
        if ($this->Group->delete_group($group_id)) {
            
            // グループの削除成功の場合
            $this->Session->setFlash('グループを削除しました。');
            $this->redirect(array(
                'controller' => 'groups',
                'action' => 'index'
            ));
        } else {
            
            // グループ削除失敗の場合
            $this->Session->setFlash('グループを削除に失敗しました。');
            $this->redirect('/groups');
        }
    }

    /**
     * 強制退会
     *
     * ユーザを強制退会させる
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function forcedWithdrawal()
    {
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if ($this->params['named']['group']) {
            $group_id = $this->params['named']['group'];
        }
        
        // グループ権限チェック
        $this->Permission->allowGroupAdmin($group_id);
        $this->Permission->allowAdmin();
        
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            echo '権限がありません';
        } else {
            
            if (isset($this->params['named']['user'])) {
                // ユーザーが選択された場合
                $usrid = $this->params['named']['user'];
                
                // 存在チェック
                if (! $this->Group->existsID($group_id) || ! $this->User->existsID($usrid)) {
                    $this->redirect('/groups');
                }
                if ($this->Join->Join_User($group_id, $usrid)) {
                    echo 'ユーザを退会させました';
                } else {
                    echo 'ユーザの退会に失敗しました';
                }
            } else {
                // ユーザー選択処理
                
                $group = $this->Group->find('first', array(
                    'conditions' => array(
                        'Group.GRP_ID' => $group_id,
                        'Group.USR_ID' => $user['User']['USR_ID']
                    )
                ));
                
                if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                    $this->Session->setFlash('権限がありません');
                    $this->redirect('/groups');
                }
                
                $joins = array(
                    0 => array(
                        'type' => 'LEFT',
                        'table' => 'T_USER',
                        'alias' => 'Administrator',
                        'conditions' => 'Administrator.USR_ID = Join.USR_ID',
                        'fields' => ''
                    )
                );
                
                $this->paginate = array(
                    'Join' => array(
                        "fields" => array(
                            'Join.*',
                            'Administrator.*',
                            'Group.USR_ID'
                        ),
                        'joins' => $joins,
                        'limit' => 10,
                        'order' => 'User.USR_ID',
                        'conditions' => array(
                            'Join.GRP_ID' => $group_id,
                            'Join.STATUS' => array(
                                Join::STATUS_ADMINISTRATOR,
                                Join::STATUS_JOINED
                            ),
                            'NOT' => array(
                                'Administrator.USR_ID' => array(
                                    $user['User']['USR_ID']
                                )
                            )
                        )
                    )
                );
                
                $list = $this->paginate('Join');
                
                $this->set("list", $list);
                $this->set("grpid", $group_id);
                $this->render('withdrawal_users', false);
            }
        }
    }
}
