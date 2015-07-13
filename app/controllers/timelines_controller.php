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
 * タイムライン用のコントローラクラス
 *
 * @author 作成者
 */
class TimelinesController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Timeline";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Home",
        "Timeline",
        'Storage',
        'Group',
        'Profile',
        'User',
        'Read',
        'Watch',
        'Friend',
        'Notice'
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
     * タイムライン削除
     *
     * タイムラインを削除する
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
        
        // トークンチェック
        if ($this->checkAjaxPost() == false) {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        
        // パスからIDを取得
        if (isset($this->params['pass'][0])) {
            $timeline_id = $this->params['pass'][0];
        } else {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        
        // データの有無
        if ($this->Timeline->existsID($timeline_id) == false) {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        
        // 権限のチェック
        $this->Permission->allowAdmin();
        $this->Permission->allowOwner("Timeline", $timeline_id);
        
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        
        return $this->Timeline->Delete_timeline($timeline_id, $user);
    }

    /**
     * タイムライン投稿
     *
     * タイムラインを投稿する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function message()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        $list = array();
        
        // どの場所から投稿されたかの判断
        $m_class = $this->data['Timeline']['M_CLASS'];
        
        // トークンチェック
        if ($this->checkPost() == false) {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        ;
        
        // 権限チェック(グループの場合のみ)
        $grpid = $this->data['Timeline']['GRP_ID'];
        
        if ($m_class === 'Group' && ! empty($grpid)) {
            // グループ存在チェック
            if (! $this->Group->existsID($grpid)) {
                $this->Session->setFlash('不正な操作が行われました。');
                $this->redirect('/homes/');
            }
            $this->Permission->allowGroupParticipant($grpid);
            $this->Permission->allowGroupAdmin($grpid);
            $this->Permission->allowAdmin();
            if ($this->Permission->isDenied($user['User']['USR_ID'])) {
                $this->Session->setFlash('不正な操作が行われました。');
                $this->redirect('/homes/');
            }
        }
        
        // 主キーがある場合はエラーにする
        $this->denyPrimaryKey("Timeline");
        
        // コメントやファイルに何も無い時の処理
        if ($this->data['FILE']['name'] == null && $this->data['Timeline']['MESSAGE'] == null) {
            $this->Timeline->invalidate('MESSAGE', '投稿内容がありません。');
            return false;
        } elseif (isset($this->data['FILE']) && $this->data['FILE']['name'] == null) {
            $this->Timeline->invalidate('MESSAGE', 'ファイルのアップロードに失敗しました。');
            unset($this->data['FILE']);
            return false;
        }
        
        if (isset($this->data['FILE']['size']) && $this->data['FILE']['size'] > Configure::read('FILE_ONE_MAX')) {
            $this->Timeline->invalidate('MESSAGE', 'ファイルサイズが大きすぎます');
            return false;
        }
        
        if (! isset($this->data['FILE']) && mb_ereg_match("^[\t\n\x0b\x0c\r\s　]+$", $this->data['Timeline']['MESSAGE'])) {
            $this->Timeline->invalidate('MESSAGE', 'なにか文字を入力してください');
            return false;
        }
        // ファイルがあるかどうかで処理を変える
        if (isset($this->data['FILE'])) {
            
            // ファイルがある場合
            // 保存
            if ($result = $this->Storage->Save_File($m_class, $this->data, $user, $this->data['Timeline']['GRP_ID'])) {
                // 成功
                return true;
            } else {
                // 失敗
                $this->Timeline->invalidate('MESSAGE', 'ファイルのアップロードに失敗しました。');
                return false;
            }
        } else {
            
            // ファイルがない場合
            // 保存
            if ($result = $this->Timeline->Save_Message($m_class, $this->data, $user, $this->data['Timeline']['GRP_ID'])) {
                // 成功
                return true;
            } else {
                // 失敗
                return false;
            }
        }
    }

    /**
     * 投稿読み込み
     *
     * 投稿の読み込みをする
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function getmessage()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        $list = array();
        
        // どの場所かを取得
        $m_class = $this->data['Timeline']['M_CLASS'];
        
        // 取得条件を読み込み
        $select = $this->Session->read('select');
        
        // どのタブを選択しているか取得
        $tab_name = $this->data['Timeline']['TAB_NAME2'];
        
        // もっと見る」を押下した際に表示させるページ番号を取得（使用するのは読んだが多いを選択した時のみ）
        if ($this->data['Timeline']['READ_PAGE'] == null) {
            $read_page = 1;
        } else {
            $read_page = $this->data['Timeline']['READ_PAGE'];
        }
        
        $read_page ++;
        $this->set("read_page", $read_page);
        
        // ページング条件を読み込み
        // $conditions =
        // $this->$m_class->Get_Timeline($user,$this->data['Timeline']['GRP_ID'],$select['while']);
        // 取得条件を更に加える
        if (($m_class == 'Home' && isset($this->data['Timeline']['P_KEYWORD']) && $this->data['Timeline']['P_KEYWORD']) || (isset($this->data['Timeline']['P_KEYWORD_FLG']) && $this->data['Timeline']['P_KEYWORD_FLG'] == '1')) {
            $select['order'] = 0;
            $select['while'] = 0;
            $calender = - 1;
        }
        
        if ($select['order'] == 0) {
            $and = array(
                'LAST_DATE <' => $this->data['Timeline']['LAST_DATE']
            );
        } elseif ($select['order'] == 1) {
            $and = array(
                'TML_ID <' => $this->data['Timeline']['LAST_ID']
            );
        } elseif ($select['order'] == 2) {
            $and = array(
                'TML_ID >' => $this->data['Timeline']['LAST_ID']
            );
        } else {
            $and = array();
        }
        
        if ($m_class == 'Home' || $m_class == 'Profile' || $m_class == 'Group') {
            
            // ホーム画面の時の処理
            if (($m_class == 'Home' && isset($this->data['Timeline']['P_KEYWORD']) && $this->data['Timeline']['P_KEYWORD']) || (isset($this->data['Timeline']['P_KEYWORD_FLG']) && $this->data['Timeline']['P_KEYWORD_FLG'] == '1')) {
                $conditions = $this->Home->Search_Timeline($this->data['Timeline']['P_KEYWORD'], $user, $this->data['Timeline']['GRP_ID'], $select['while'], null, $this->data['Timeline']['CURRENT_PAGE']);
            } else {
                
                // ログインユーザーのコメントに紐付くタイムラインIDを取得
                $result = $this->Timeline->find('all', array(
                    'conditions' => array(
                        'Timeline.USR_ID' => $user['User']['USR_ID'],
                        'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT,
                        'Timeline.DEL_FLG' => 0
                    ),
                    'fields' => array(
                        'Timeline.VAL_ID'
                    )
                ));
                
                $vid_or = array();
                $tmp = null;
                foreach ($result as $key => $val) {
                    
                    // 同一のタイムラインIDが配列に格納されていない場合のみ実行
                    if ($tmp != $val['Timeline']['VAL_ID']) {
                        $vid_or[$key] = $val['Timeline']['VAL_ID'];
                        // 配列に設定したIDを格納
                        $tmp = $vid_or[$key];
                    }
                }
                
                // ホーム画面かつ、検索指定でない時の処理
                if ($m_class == 'Home') {
                    
                    // タブの種類に応じて処理を分岐 フォローのみ:FOLLOW 自分のみ:ONLY グループのみ:GROUP
                    if ($tab_name == 'FOLLOW') {
                        $conditions = $this->$m_class->Get_Timeline($user, $this->data['Timeline']['GRP_ID'], $select['while'], null, 1);
                    } elseif ($tab_name == 'ONLY') {
                        $conditions = $this->$m_class->Get_Timeline($user, $this->data['Timeline']['GRP_ID'], $select['while'], $vid_or, 2);
                    } elseif ($tab_name == 'GROUP') {
                        $conditions = $this->$m_class->Get_Timeline($user, $this->data['Timeline']['GRP_ID'], $select['while'], null, 3);
                    } else {
                        $conditions = $this->$m_class->Get_Timeline($user, $this->data['Timeline']['GRP_ID'], $select['while']);
                    }
                } elseif ($m_class == 'Profile') {
                    // プロフィール画面の処理
                    $conditions = $this->$m_class->Get_Timeline($user, $this->data['Timeline']['GRP_ID'], $select['while'], null, $this->data['Timeline']['USR_ID']);
                } else {
                    $conditions = $this->$m_class->Get_Timeline($user, $this->data['Timeline']['GRP_ID'], $select['while']);
                }
                
                $conditions = array_merge($conditions, $and);
            }
        } else {
            
            $conditions = $this->$m_class->Get_Timeline($user, $this->data['Timeline']['GRP_ID']);
            
            // 条件を整える
            $conditions = array_merge($conditions, $and);
        }
        
        // カレンダーの取得
        $calender = $this->Session->read('calender');
        if ($calender != - 1) {
            $conditions = array_merge($conditions, array(
                'and' => array(
                    'Timeline.INSERT_DATE LIKE' => "%" . $calender['year'] . "-" . $calender['mon'] . "-" . $calender['day'] . "%"
                )
            ));
        } else {
            $mon = date("m");
            $year = date("Y");
            $day = date("d");
            $calender = array(
                "mon" => $mon,
                "year" => $year,
                "day" => $day
            );
        }
        
        if ($m_class == 'Watch') {
            
            // ウォッチリストの場合
            $this->paginate = $conditions;
            $list = $this->paginate('Timeline', array(
                'Watch.WCH_ID <' => $this->data['Timeline']['LAST_ID']
            ));
        } elseif ($select['order'] == 1) {
            
            // 条件が新しい発言の場合
            $this->paginate = array(
                'Timeline' => array(
                    'order' => 'Timeline.INSERT_DATE DESC'
                )
            );
            // データを取得
            $list = $this->paginate('Timeline', $conditions, array());
        } elseif ($select['order'] == 2) {
            
            // 条件が古い発言の場合
            $this->paginate = array(
                'Timeline' => array(
                    'order' => 'Timeline.INSERT_DATE'
                )
            );
            // データを取得
            $list = $this->paginate('Timeline', $conditions, array());
        } elseif ($select['order'] == 3) {
            
            // 条件が読んだが多い順の場合
            $this->paginate = array(
                'Timeline' => array(
                    'order' => 'Timeline.RED_NUM DESC,Timeline.TML_ID DESC',
                    'conditions' => array(
                        'Timeline.RED_NUM >=' => 0
                    ),
                    'limit' => 20,
                    'page' => $read_page
                )
            );
            
            // データを取得
            $list = $this->paginate('Timeline', $conditions, array());
        } elseif (($m_class == 'Home' && isset($this->data['Timeline']['P_KEYWORD']) && $this->data['Timeline']['P_KEYWORD']) || (isset($this->data['Timeline']['P_KEYWORD_FLG']) && $this->data['Timeline']['P_KEYWORD_FLG'] == '1')) {
            $this->paginate = $conditions;
            $list = $this->paginate('Timeline', array(), array());
        } else {
            // その他
            $list = $this->paginate('Timeline', $conditions, array());
        }
        
        // コメントの読み込み
        $this->Timeline->Comment_Search($list);
        
        // 読んだ!リストを取得
        $this->Read->Read_Search($list, $user);
        
        // 変数をセット
        $this->set("list", $list);
        $this->set("date_frag", isset($this->params['pass'][0]) && $this->params['pass'][0] == 'calender' ? 0 : 1);
        $this->set("calender", $calender);
        $this->set("select", $select);
        $this->set("m_class", $m_class);
        $this->set("tab_name", $tab_name);
        
        if ($m_class == 'Group') {
            $this->set('groupid', $this->data['Timeline']['GRP_ID']);
        } else {
            $this->set('groupid', $user['User']['GRP_ID']);
        }
        
        if ($m_class == 'Watch') {
            // ウォッチリストの場合
            $this->set("lastid", $list[count($list) - 1]['Watch']['WCH_ID']);
            $this->set("watch", true);
            $this->set("m_class", 'Watch');
        } else {
            // その他
            $this->set("lastid", isset($list[count($list) - 1]['Timeline']['TML_ID']) ? $list[count($list) - 1]['Timeline']['TML_ID'] : - 1);
            $this->set("lastdate", isset($list[count($list) - 1]['Timeline']['LAST_DATE']) ? $list[count($list) - 1]['Timeline']['LAST_DATE'] : - 1);
        }
        
        // 描画先のviewを指定
        if (isset($this->data['Timeline']['no_render']) && $this->data['Timeline']['no_render']) {
            // 描画しない場合
        } else 
            if (($m_class == 'Home' && isset($this->data['Timeline']['P_KEYWORD']) && $this->data['Timeline']['P_KEYWORD']) || (isset($this->data['Timeline']['P_KEYWORD_FLG']) && $this->data['Timeline']['P_KEYWORD_FLG'] == '1')) {
                $this->render('../elements/timeline/search_timeline', false);
            } else {
                $this->render('../elements/timeline/timeline', false);
            }
    }

    /**
     * コメント投稿
     *
     * コメントを投稿する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function comment()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->User();
        
        $list = array();
        
        // TODO バリデーションに書く
        // View側で「なにも返ってこなかったらエラー」という判定がされているよう
        // なので、下記のエラーとともにつくりを見直す
        
        if ($this->data['Timeline']['COMMENT'] == null) {
            // コメントに何も入力されていない場合
            exit();
        }
        
        // トークンチェック
        if ($this->checkPost() == false) {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        ;
        
        // 存在チェック
        if ($this->Timeline->existsID($this->data["Timeline"]["TML_ID"]) == false) {
            exit();
        }
        
        // 権限のチェック
        $this->Permission->allowReader($this->data["Timeline"]["TML_ID"]);
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            // TODO コメントの投稿はうまくリダイレクトできないので、
            // とりあえずエラーを出力しておく
            exit();
        }
        
        // コメントの保存
        if ($result = $this->Timeline->Save_Message('Comment', $this->data, $user)) {
            // 成功
            // 変数をセット
            $this->set("list", $this->Timeline->find('first', array(
                'conditions' => array(
                    'Timeline.TML_ID' => $result
                )
            )));
            
            // 描画先を指定
            $this->render('../elements/timeline/comment', false);
        } else {
            // 失敗
            $error0 = "メッセージは1000文字以内で入力してください。";
            $error1 = "禁止ワードが含まれているため、投稿できません。";
            
            // エラー取得
            $error = $this->validateErrors($this->Timeline);
            if ($error["MESSAGE"] == $error0) {
                echo "over";
            } elseif ($error["MESSAGE"] == $error1) {
                echo "word";
            }
        }
    }

    /**
     * ファイル欄開く処理
     *
     * ファイル欄を開く
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function file()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // 変数をセット
        $this->set("m_class", $this->params['pass'][0]);
        $this->set("grpid", $this->params['pass'][1]);
        
        // 描画先を指定
        $this->render('../elements/timeline/file', false);
    }

    /**
     * 読んだ！
     *
     * 読んだ！
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function read()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // トークンチェック
        // TODO Ajaxだとリダイレクトがうまく動かないので、実装方法を検討
        if ($this->checkAjaxPost() == false) {
            $this->redirect("/homes/");
        }
        
        // パスからIDを取得
        if (isset($this->params['pass'][0])) {
            $timeline_id = $this->params['pass'][0];
        } else {
            $this->redirect("/homes/");
        }
        
        // データ存在チェック
        if ($this->Timeline->existsID($timeline_id) == false) {
            $this->redirect("/home/");
        }
        
        // 権限チェック
        $this->Permission->allowReader($timeline_id);
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            $this->redirect("/home/");
        }
        
        // よんだ！を保存
        if ($id = $this->Read->Reads($timeline_id, $user['User']['USR_ID'])) {
            // 成功
            $result = $this->Timeline->find('all', array(
                'conditions' => array(
                    'Timeline.TML_ID' => $id
                )
            ));
            
            $this->Read->Read_Search($result, $user);
            
            // 変数をセット
            $this->set("list", $result);
        }
        
        // 描画先を指定
        $this->render('../elements/timeline/read', false);
    }

    /**
     * 読んだ！ユーザ取得
     *
     * 読んだ！をクリックしたユーザの取得をする
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function read_user()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if (isset($this->params['pass'][0])) {
            $timeline_id = $this->params['pass'][0];
        } else {
            $this->redirect("/home/");
        }
        
        // データ存在チェック
        if ($this->Timeline->existsID($timeline_id) == false) {
            $this->redirect("/home/");
        }
        
        // 権限チェック
        $this->Permission->allowReader($timeline_id);
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            $this->redirect("/home/");
        }
        
        // 取得条件の指定
        $this->paginate = array(
            'Read' => array(
                "fields" => '',
                'conditions' => array(
                    'Read.TML_ID' => $timeline_id
                ),
                'limit' => 10,
                'joins' => '',
                'order' => ''
            )
        );
        
        // リストを取得
        $list = $this->paginate('Read');
        
        // 変数をセット
        $this->set("list", $list);
        
        // 描画先を指定
        $this->render('../elements/timeline/readuser', false);
    }

    /**
     * 被フォローユーザ取得
     *
     * フォローされているユーザを取得する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function follower_user()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if (isset($this->params['pass'][0])) {
            $id = $this->params['pass'][0];
        } else {
            $this->redirect(array(
                'controller' => 'homes'
            ));
        }
        
        // フォロー－しているユーザの取得
        $this->paginate = $this->Friend->Get_Friend_Status($id, false, 'r');
        
        $list = $this->paginate('Friend');
        $count = $this->Friend->find('count', array(
            'conditions' => array(
                'Friend.F_USR_ID' => $id
            )
        ));
        
        $name = $this->User->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $id
            ),
            'fields' => array(
                'User.NAME'
            )
        ));
        
        // 変数をセット
        $this->set("name", $name['User']['NAME']);
        $this->set("count", $count);
        $this->set("list", $list);
        $this->set("r", 'r');
        
        // 描画先を指定
        $this->render('../elements/timeline/followuser', false);
    }

    /**
     * フォローユーザ取得
     *
     * フォローしているユーザを取得する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function following_user()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if (isset($this->params['pass'][0])) {
            $id = $this->params['pass'][0];
        } else {
            $this->redirect(array(
                'controller' => 'homes'
            ));
        }
        
        // フォロー－しているユーザの取得
        $this->paginate = $this->Friend->Get_Friend_Status($id, false, 's');
        $list = $this->paginate('Friend');
        
        $count = $this->Friend->find('count', array(
            'conditions' => array(
                'Friend.USR_ID' => $id
            )
        ));
        
        $name = $this->User->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $id
            ),
            'fields' => array(
                'User.NAME'
            )
        ));
        
        // 変数をセット
        $this->set("name", $name['User']['NAME']);
        $this->set("count", $count);
        $this->set("list", $list);
        
        // 描画先を指定
        $this->render('../elements/timeline/followuser', false);
    }

    /**
     * ウォッチリスト登録
     *
     * ウォッチリストに登録する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function watch()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // トークンチェック
        if ($this->checkAjaxPost() == false) {
            $this->redirect("/home/");
        }
        
        // パスからIDを取得
        if (isset($this->params['pass'][0])) {
            $timeline_id = $this->params['pass'][0];
        } else {
            $this->redirect("/home/");
        }
        
        // 存在チェック
        if ($this->Timeline->existsID($timeline_id) == false) {
            $this->redirect("/home/");
        }
        
        // 権限チェック
        $this->Permission->allowReader($timeline_id);
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            $this->redirect("/home/");
        }
        
        // 保存
        if ($id = $this->Watch->Watch_Save($timeline_id, $user['User']['USR_ID'])) {
            $result = $this->Timeline->find('all', array(
                'conditions' => array(
                    'Timeline.TML_ID' => $id
                )
            ));
            $this->Watch->Watch_Search($result, $user);
            $this->set("list", $result);
        }
        
        // 描画先を指定
        $this->render('../elements/timeline/watch', false);
    }

    /**
     * ウォッチリスト取得
     *
     * ウォッチリストを取得する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function watch_list()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // 条件の所得
        $conditions = $this->Watch->Get_Timeline($user, null);
        
        // ページング条件のセット
        $this->paginate = $conditions;
        $list = $this->paginate('Timeline');
        
        // コメントの読み込み
        $this->Timeline->Comment_Search($list);
        
        // いいねリストを取得
        $this->Read->Read_Search($list, $user);
        
        // ウォッチリストを取得
        $this->Watch->Watch_Search($list, $user);
        
        // 変数をセット
        if ($list != null) {
            $this->set("lastid", $list[count($list) - 1]['Watch']['WCH_ID']);
        } else {
            $this->set("lastid", 0);
        }
        $this->set("list", $list);
        $this->set("watch", true);
        $this->set("m_class", 'Watch');
        $this->set('groupid', $user['User']['GRP_ID']);
        $this->set("date_frag", true);
        
        // 描画先を指定
        $this->render('../elements/timeline/timeline', false);
    }

    /**
     * 条件をつけた場合の取得
     *
     * 条件をつけた場合の取得
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function select()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $this->params['pass'][0]
            )
        ));
        
        // ログインユーザーのコメントに紐付くタイムラインIDを取得
        $result = $this->Timeline->find('all', array(
            'conditions' => array(
                'Timeline.USR_ID' => $user['User']['USR_ID'],
                'Timeline.ACT_ID' => 2,
                'Timeline.DEL_FLG' => 0
            ),
            'fields' => array(
                'Timeline.VAL_ID'
            )
        ));
        
        $vid_or = array();
        foreach ($result as $key => $val) {
            // 同一のタイムラインIDが配列に格納されていない場合のみ実行
            if (isset($tmp) && $tmp != $val['Timeline']['VAL_ID']) {
                $vid_or[$key] = $val['Timeline']['VAL_ID'];
                // 配列に設定したIDを格納
                $tmp = $vid_or[$key];
            }
        }
        
        $list = array();
        
        // パスから条件の取得
        if (isset($this->params['pass'][1]) && $this->params['pass'][1] != null) {
            $year = $this->params['pass'][1];
            $mon = sprintf("%02d", $this->params['pass'][2]);
            $day = sprintf("%02d", $this->params['pass'][3]);
            $calender = array(
                'year' => $year,
                'mon' => $mon,
                'day' => $day
            );
            
            $this->Session->write('calender', $calender);
            $select = $this->Session->read('select');
        } else {
            $order = $this->params['form']['params']['order'];
            $while = $this->params['form']['params']['while'];
            $tab = isset($this->params['form']['params']['tab_name']) ? $this->params['form']['params']['tab_name'] : '';
            $select = array(
                'order' => $order,
                'while' => $while,
                'tab_name' => $tab
            );
            
            $this->Session->write('select', $select);
            $calender = $this->Session->read('calender');
        }
        
        // ページング条件の取得
        if ($this->params['form']['params']['model'] == 'home') {
            
            // homeを使用している場合、どのタイムラインのタブを使用しているかを取得する
            $tab_name = $this->params['form']['params']['tab_name'];
            
            // タブの種類に応じて処理を分岐 フォローのみ:FOLLOW 自分のみ:ONLY グループのみ:GROUP
            if ($tab_name == 'FOLLOW') {
                $conditions = $this->Home->Get_Timeline($user, null, $select['while'], null, 1);
            } elseif ($tab_name == 'ONLY') {
                $conditions = $this->Home->Get_Timeline($user, null, $select['while'], $vid_or, 2);
            } elseif ($tab_name == 'GROUP') {
                $conditions = $this->Home->Get_Timeline($user, null, $select['while'], null, 3);
            } else {
                $conditions = $this->Home->Get_Timeline($user, null, $select['while']);
            }
            
            $mclass = 'Home';
        } elseif ($this->params['form']['params']['model'] == 'profile') {
            $conditions = $this->Profile->Get_Timeline($user, $user['User']['GRP_ID'], $select['while']);
            $mclass = 'Profile';
        } elseif ($this->params['form']['params']['model'] == 'group') {
            $conditions = $this->Group->Get_Timeline($this->Auth->user(), $this->params['pass'][0], $select['while']);
            $mclass = 'Group';
            $user = $this->Auth->user();
        }
        
        if ($calender != - 1) {
            // 日付指定がある場合
            $conditions = array_merge($conditions, array(
                'and' => array(
                    'Timeline.INSERT_DATE LIKE' => "%" . $calender['year'] . "-" . $calender['mon'] . "-" . $calender['day'] . "%"
                )
            ));
        }
        
        if ($select['order'] == 1) {
            // 新しい投稿順から取得
            $this->paginate = array(
                'Timeline' => array(
                    'order' => 'Timeline.INSERT_DATE DESC',
                    'conditions' => array(
                        'Timeline.RED_NUM >=' => 0
                    ),
                    'limit' => 20,
                    'page' => 1
                )
            );
        } elseif ($select['order'] == 2) {
            // 古い投稿順から取得
            $this->paginate = array(
                'Timeline' => array(
                    'order' => 'Timeline.INSERT_DATE',
                    'conditions' => array(
                        'Timeline.RED_NUM >=' => 0
                    ),
                    'limit' => 20,
                    'page' => 1
                )
            );
        } elseif ($select['order'] == 3) {
            // 読んだ!が多い順の取得
            $this->paginate = array(
                'Timeline' => array(
                    'order' => 'Timeline.RED_NUM DESC,Timeline.TML_ID DESC',
                    'conditions' => array(
                        'Timeline.RED_NUM >=' => 0
                    ),
                    'limit' => 20,
                    'page' => 1
                )
            );
        }
        
        // リストを取得
        $list = $this->paginate('Timeline', $conditions);
        
        // コメントを取得
        $this->Timeline->Comment_Search($list);
        
        // 読んだ!リストを取得
        if ($mclass == 'Profile') {
            $this->Read->Read_Search($list, $this->Auth->user());
        } else {
            $this->Read->Read_Search($list, $user);
        }
        
        // 変数をセット
        if (count($list) > 0) {
            $this->set("lastid", $list[count($list) - 1]['Timeline']['TML_ID']);
            $this->set("lastdate", $list[count($list) - 1]['Timeline']['LAST_DATE']);
        }
        
        $this->set("select", $this->params['form']['params']);
        if ($this->params['form']['params']['model'] == 'group') {
            $this->set('groupid', $this->params['pass'][0]);
        } else {
            $this->set('groupid', $user['User']['GRP_ID']);
        }
        
        $this->set("date_frag", true);
        $this->set("m_class", $mclass);
        $this->set("list", $list);
        
        // タブ名称を再設定
        $this->set("tab_name", isset($tab_name) ? $tab_name : '');
        
        // 描画先を指定
        $this->render('../elements/timeline/timeline', false);
    }

    /**
     * カレンダー取得
     *
     * カレンダーを取得する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function calender()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if (! isset($this->params['form']['token'])) {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        
        // パスから日付の取得
        if (isset($this->params['pass'][0]) && $this->params['pass'][0]) {
            $mon = $this->params['pass'][1];
            $year = $this->params['pass'][0];
            
            $m_class = $this->params['pass'][2];
            $userid = $this->params['pass'][3];
            if ($m_class != 'Group') {
                $user = $this->User->find('first', array(
                    'conditions' => array(
                        'User.USR_ID' => $userid
                    )
                ));
            } else {
                $group = $this->Group->find('first', array(
                    'conditions' => array(
                        'Group.GRP_ID' => $this->params['pass'][3]
                    )
                ));
            }
        } else {
            $this->redirect(array(
                'controller' => 'homes',
                'action' => 'index'
            ));
        }
        
        $calender = array(
            "mon" => $mon,
            "year" => $year
        );
        
        $conditions = array();
        
        if ($m_class == 'Home') {
            $conditions = $this->Home->Get_Timeline($user, $user['User']['GRP_ID']);
            $this->set("user", $user);
        } elseif ($m_class == 'Profile') {
            $conditions = $this->Profile->Get_Timeline($user, $user['User']['GRP_ID']);
            $this->set("profile", $user);
        } elseif ($m_class == 'Group') {
            $conditions = $this->Group->Get_Timeline($user, $group['Group']['GRP_ID']);
            $this->set("group", $group);
        }
        
        // 日付にデータがあるか検査
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
        
        // 変数のセット
        $this->set("m_class", $m_class);
        $this->set("is_data", $is_data);
        $this->set("calender", $calender);
        
        // 描画するviewの指定
        $this->render('../elements/calender', false);
    }
}
