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
 * フレンド用のコントローラクラス
 *
 * @author 作成者
 */
class FriendsController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Friend";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Friend",
        "User",
        "Administrator",
        "Timeline"
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
     * 一覧画面
     *
     * 一覧画面を表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        // 初期化
        $this->set("main_title", "ユーザ");
        $this->set("title_text", "ユーザ");
        
        $user = $this->Auth->user();
        $conditions = array();
        
        if (isset($this->passedArgs['NAME'])) {
            
            // 検索単語をGetデータより取得
            $this->data['Friend']['NAME'] = $this->passedArgs['NAME'];
        }
        
        // 検索条件のチェック
        if ($this->checkPost() && ($this->data['Friend']['NAME'] != null)) {
            
            // 条件に何か指定されていた場合
            $conditions = $this->Friend->Get_Friend_Status($user['User']['USR_ID'], $this->data['Friend']['NAME'], $this->params['pass'][0]);
            $this->set("keyword", $this->data['Friend']['NAME']);
        } else {
            
            // 何も指定されていない時の処理
            // パスのチェック
            if (! isset($this->params['pass'][0])) {
                
                // パスに何も入っていなかった時用の処理
                $this->params['pass'][0] = 's';
            }
            
            // 条件の取得
            $conditions = $this->Friend->Get_Friend_Status($user['User']['USR_ID'], false, $this->params['pass'][0]);
        }
        
        // ページンに条件を書きこみ
        $this->paginate = $conditions;
        
        // 条件のチェック
        if ((! isset($this->params['pass'][0])) || ($this->params['pass'][0] != 'all')) {
            
            // 条件に何も指定されなかった場合の処理
            $list = $this->paginate('Friend');
        } else {
            
            // 指定された場合の処理
            $list = $this->paginate('Administrator');
        }
        
        // 取得した情報の件数を計算
        if (count($list) == 0 && isset($this->params['pass'][0]) && $this->params['pass'][0] == 's') {
            $this->Session->setFlash('まだフォローしているユーザがいません。「すべてのユーザを表示」タブからユーザをフォローしましょう。', 'default', array(
                'class' => 'help'
            ));
        }
        
        // 取得した情報からそのユーザをフォローしているかどうかをチェック
        foreach ($list as $key => $val) {
            
            if (isset($this->params['pass'][0]) && $this->params['pass'][0] == 'r') {
                
                // フォローされているユーザ、の場合
                // データの取得
                $result = $this->Friend->find('first', array(
                    'conditions' => array(
                        'F_USR_ID' => $val['Administrator']['USR_ID'],
                        'USR_ID' => $user['User']['USR_ID']
                    )
                ));
                
                if ($result != null) {
                    $list[$key]['Friend']['STATUS'] = Friend::STATUS_FOLLOWED;
                } else {
                    $list[$key]['Friend']['STATUS'] = Friend::STATUS_NOT_FOLLOWED;
                }
            } elseif (! isset($this->params['pass'][0]) || $this->params['pass'][0] == 'all') {
                
                // フォローしているユーザの場合
                if ($val['Friend']['F_USR_ID'] != null) {
                    $list[$key]['Friend']['STATUS'] = Friend::STATUS_FOLLOWED;
                } else {
                    $list[$key]['Friend']['STATUS'] = Friend::STATUS_NOT_FOLLOWED;
                }
            } else {
                
                // フォローしているユーザの場合
                if ($val['Friend']['F_USR_ID'] != null) {
                    $list[$key]['Friend']['STATUS'] = Friend::STATUS_FOLLOWED;
                } else {
                    $list[$key]['Friend']['STATUS'] = Friend::STATUS_NOT_FOLLOWED;
                }
            }
            
            $followed = $this->Friend->find('first', array(
                'conditions' => array(
                    'F_USR_ID' => $user['User']['USR_ID'],
                    'USR_ID' => $val['Administrator']['USR_ID']
                )
            ));
            
            // フォローされているかどうか
            if (! empty($followed)) {
                $list[$key]['Friend']['FOLLOWED'] = Friend::STATUS_FOLLOWED;
            } else {
                $list[$key]['Friend']['FOLLOWED'] = Friend::STATUS_NOT_FOLLOWED;
            }
            
            $race = $this->Timeline->find('first', array(
                'fields' => 'Timeline.MESSAGE',
                'order' => 'Timeline.INSERT_DATE DESC',
                'conditions' => array(
                    'Timeline.USR_ID' => $val['Administrator']['USR_ID'],
                    'Timeline.ACT_ID' => array(
                        Timeline::ACT_ID_TIMELINE,
                        Timeline::ACT_ID_FILE_TIMELINE
                    )
                )
            ));
            
            $list[$key]['Timeline']['RECENT'] = $race['Timeline']['MESSAGE'];
        }
        
        // 変数のセット
        $this->set("index_list", $list);
        $this->set("follow", Configure::read('FOLLOW_STATUS'));
    }

    /**
     * フォロー用
     *
     * フォローをする
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function follow()
    {
        $this->autoRender = false;
        $user = $this->Auth->user();
        
        // トークンチェック
        if ($this->checkAjaxPost() == false) {
            $this->redirect("/friends");
        }
        
        // IDの取得
        if (isset($this->params['pass'][0]) && $this->params['pass'][0]) {
            $C_ID = $this->params['pass'][0];
        }
        
        // データ存在チェック
        if ($this->User->existsID($C_ID) == false) {
            $this->redirect("/friends");
        }
        
        // フォローステータスの変更処理
        $result = $this->Friend->Change_Friend($user['User']['USR_ID'], $C_ID);
        if ($result == 0) {
            // フォローをはずした場合の処理
            $this->set("result", 0);
        } elseif ($result == 1) {
            // フォローした時の処理
            $this->set("result", 1);
        }
        
        $follow = Configure::read('FOLLOW_STATUS');
        $follow[0] = 'フォローする';
        
        // 変数をセット
        $this->set("userid", $C_ID);
        $this->set("follow", $follow);
        
        // 描画するviewを指定
        $this->render('friendship', false);
    }
}

