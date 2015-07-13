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
 * プロフィール用のコントローラクラス
 *
 * @author 作成者
 */
class ProfilesController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Profile";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Profile",
        "User",
        "Timeline",
        "Group",
        'Administrator',
        'Friend',
        'Read',
        'Watch',
        'Join'
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
        'order' => 'Timeline.LAST_DATE DESC',
        'recursive' => 0
    );

    /**
     * プロフィール
     *
     * プロフィールを表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        // 初期化
        $list = array();
        $profile = array();
        
        $this->set("main_title", "プロフィール");
        $this->set("title_text", "プロフィール");
        
        $user2 = $this->Auth->User();
        $user = $this->Auth->User();
        
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
        
        // ID等を取得
        if (isset($this->params['pass'][0]) && $this->User->existsID($this->params['pass'][0])) {
            
            // パスにIDが入っている場合
            // IDからユーザ情報を取得
            $userid = $this->params['pass'][0];
            $user = $this->User->find('first', array(
                'conditions' => array(
                    'USR_ID' => $userid
                )
            ));
            
            if ($user['User']['STATUS'] != User::STATUS_ENABLED) {
                $this->Session->setFlash('ユーザが存在しないか、または退会されたユーザです。');
                $this->redirect(array(
                    'controller' => 'friends',
                    'action' => 'index'
                ));
            }
            
            // グループのIDを取得
            $groupid = $user['User']['GRP_ID'];
            $name = $user['User']['NAME'];
            
            // プロフィールにユーザ情報を入れる
            $profile = array_merge($profile, $user);
        } else {
            
            // パスがない場合
            // ログインしているユーザの情報を取得
            $user = $this->Auth->User();
            $userid = $user['User']['USR_ID'];
            $groupid = $user['User']['GRP_ID'];
            $name = $user['User']['NAME'];
            $profile = array_merge($profile, $user);
        }
        
        // タイムラインのページング条件を取得
        $conditions = $this->Profile->Get_Timeline($user, $user['User']['GRP_ID'], $select['while']);
        
        // タイムラインを取得
        $list = $this->paginate('Timeline', $conditions, array());
        
        // コメントがあるか、どうかのチェック
        if ($this->Timeline->Comment_Search($list)) {
            $this->set("first", $list[0]['Timeline']['TML_ID']);
            $this->set("lastid", $list[count($list) - 1]['Timeline']['TML_ID']);
            $this->set("lastdate", $list[count($list) - 1]['Timeline']['LAST_DATE']);
        } else {
            $this->set("first", 0);
        }
        
        // いいね、があるかのチェック
        $this->Read->Read_Search($list, $user2);
        
        // ウォッチリストに登録してあるかのチェック
        $this->Watch->Watch_Search($list, $user2);
        
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
        
        // プロフィールの読み込み
        $profile = $this->User->find('first', array(
            'conditions' => array(
                'USR_ID' => $userid
            )
        ));
        
        // グループ情報の取得
        $this->paginate = $this->Group->Search_Group($user['User']['USR_ID'], null, array(
            0,
            1,
            2
        ), 5);
        
        $group = $this->paginate('Group');
        $count = $this->Join->find('count', array(
            'conditions' => array(
                'Join.USR_ID' => $user['User']['USR_ID']
            )
        ));
        
        $group[0]['Count'] = $count;
        
        // 友達関係になっているかどうかの確認
        $friend = $this->Friend->find('first', array(
            'conditions' => array(
                'Friend.F_USR_ID' => $profile['User']['USR_ID'],
                'Friend.USR_ID' => $user2['User']['USR_ID']
            )
        ));
        
        $belong = array(
            'belongsTo' => array(
                'Administrator' => array(
                    'className' => 'Administrator',
                    'conditions' => '',
                    'order' => '',
                    'dependent' => true,
                    'foreignKey' => 'F_USR_ID'
                )
            )
        );
        
        // フォロー－しているユーザの取得
        $this->paginate = $this->Friend->Get_Friend_Status($user['User']['USR_ID'], false, 's', 6);
        $following_user = $this->paginate('Friend');
        $this->Friend->bindModel($belong);
        
        $count = $this->Friend->find('count', array(
            'conditions' => array(
                'Friend.USR_ID' => $user['User']['USR_ID'],
                'Administrator.STATUS' => 1
            )
        ));
        $following_user[0]['Count'] = $count;
        $belong['belongsTo']['Administrator']['foreignKey'] = 'USR_ID';
        
        // フォローされているユーザの取得
        $this->paginate = $this->Friend->Get_Friend_Status($user['User']['USR_ID'], false, 'r', 6);
        $follower_user = $this->paginate('Friend');
        $this->Friend->bindModel($belong);
        
        $count = $this->Friend->find('count', array(
            'conditions' => array(
                'Friend.F_USR_ID' => $user['User']['USR_ID'],
                'Administrator.STATUS' => 1
            )
        ));
        $follower_user[0]['Count'] = $count;
        
        // セット
        $this->set("following_user", $following_user);
        $this->set("follower_user", $follower_user);
        $this->set("watch", true);
        $this->set("follow", Configure::read('FOLLOW_STATUS'));
        $this->set("friend", $friend);
        $this->set("countys", Configure::read('PREFECTURE_CODE'));
        $this->set("date_frag", 1);
        $this->set("calender", $calender);
        $this->set("is_data", $is_data);
        $this->set("group", $group);
        $this->set("profile", $profile);
        $this->set("name", $name);
        $this->set("list", $list);
        $this->set("groupid", $groupid);
        $this->set("m_class", 'Profile');
        $this->set("select", array(
            'order' => - 1,
            'while' => - 1
        ));
    }

    /**
     * プロフィール編集
     *
     * プロフィールを編集する
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
        
        $error = array();
        
        $this->Permission->allowAdmin();
        $this->Permission->allowOwner('User', $user["User"]["USR_ID"]);
        
        // サブミットが押された場合
        if ($this->checkPost() && $this->Permission->isAllowed($user['User']['USR_ID']) && $this->User->existsID($user["User"]["USR_ID"])) {
            
            // セッション情報からユーザIDを取得
            $this->data["Profile"]["USR_ID"] = $user["User"]["USR_ID"];
            
            // プロフィールの保存
            if ($this->Profile->Save_Profile($this->data['Profile'], $user, $error)) {
                
                // 保存したユーザの情報を取得
                $result = $this->User->find('first', array(
                    'conditions' => array(
                        'User.USR_ID' => $user['User']['USR_ID']
                    )
                ));
                
                // ログインしているユーザの情報を上書き
                $this->Session->write('Auth', $result);
                return true;
            } else {
                
                // エラーがあった場合、それをセット
                $this->Profile->validationErrors = $error;
            }
        } else {
            
            // 初期表示
            $result = array();
            $profile = $this->User->find('first', array(
                'conditions' => array(
                    'USR_ID' => $user['User']['USR_ID']
                )
            ));
            
            $this->data['Profile'] = $profile['User'];
        }
        
        // 変数のセット
        $this->set("countys", Configure::read('PREFECTURE_CODE'));
        
        // 描画するviewの指定
        $this->render('profile_edit', false);
    }

    /**
     * サムネイル編集
     *
     * サムネイルを編集する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function image()
    {
        // ユーザIDを取得
        $user = $this->Auth->user();
        
        // トークンチェック
        if ($this->checkPost()) {
            
            // 存在チェック
            if ($this->User->existsID($user["User"]["USR_ID"]) == false) {
                $this->redirect("/profiles/");
            }
            
            // 権限チェック
            $this->Permission->allowOwner("User", $user["User"]["USR_ID"]);
            if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                $this->redirect("/profiles/");
            }
            
            // 画像を保存
            if ($this->Administrator->Save_Image($this->data, $user, 'edit')) {
                // 成功
                echo 'true';
                exit();
            } else {
                // 失敗
                echo "画像を変更に失敗しました";
                exit();
            }
        } else {
            
            // 初期表示
            // 描画するviewの指定
            $this->render('../elements/image', false);
        }
    }

    /**
     * タイムライン表示：全て
     *
     * 全てのタイムラインを表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function all()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $list = array();
        $profile = array();
        
        $user = $this->Auth->User();
        $userid = $user['User']['USR_ID'];
        $groupid = $user['User']['GRP_ID'];
        $name = $user['User']['NAME'];
        $profile = array_merge($profile, $user);
        
        // タイムラインの条件を取得
        $conditions = $this->Profile->Get_Timeline($user, $groupid, 'first');
        
        // タイムラインの取得
        $list = $this->paginate('Timeline', $conditions, array());
        
        // コメントの取得
        if ($this->Timeline->Comment_Search($list)) {
            $this->set("first", $list[0]['Timeline']['TML_ID']);
            $this->set("lastid", $list[count($list) - 1]['Timeline']['TML_ID']);
            $this->set("lastdate", $list[count($list) - 1]['Timeline']['LAST_DATE']);
        } else {
            $this->set("first", 0);
        }
        
        // 読んだの取得
        $this->Read->Read_Search($list, $user);
        
        // ウォッチリストの取得
        $this->Watch->Watch_Search($list, $user);
        
        // 変数のセット
        $this->set("watch", true);
        $this->set("list", $list);
        $this->set("date_frag", true);
        $this->set("m_class", 'Profile');
        $this->set("groupid", $groupid);
        
        // 描画するviewの指定
        $this->render('../elements/timeline/timeline', false);
    }
}
