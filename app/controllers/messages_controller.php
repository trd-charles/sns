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
 * メッセージ用のコントローラクラス
 *
 * @author 作成者
 */
class MessagesController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Message";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Message",
        'Friend',
        'User'
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
        'Permission' => array(
            "Message"
        )
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
        'fields' => array(
            ' Message.INSERT_DATE, Message.SUBJECT',
            'MIN(MSG_ID) AS Message__MSG_G_ID',
            'S_User.USR_ID',
            'S_User.NAME',
            'Message.RED',
            'Message.S_NAME',
            'Message.R_NAME'
        ),
        'group' => array(
            ' Message.INSERT_DATE, Message.SUBJECT',
            'S_User.USR_ID',
            'S_User.NAME'
        ),
        'sort' => '',
        'limit' => 10,
        'order' => 'Message.INSERT_DATE DESC',
        'recursive' => 0
    );

    /**
     * メッセージ一覧
     *
     * メッセージ一覧を表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        
        // 初期化
        $this->set("main_title", "メッセージ");
        $this->set("title_text", "メッセージ");
        
        $user = $this->Auth->user();
        $list = array();
        
        // メッセージの条件を取得
        $conditions = $this->Message->Get_Message($user, isset($this->params['pass']['0']) ? $this->params['pass']['0'] : 'r');
        
        // メッセージの取得
        $this->Message->virtualFields['MSG_G_ID'] = 0;
        $list = $this->paginate('Message', $conditions, array());
        $list = $this->Message->getAllDestinationUsers($list, $user);
        
        // 変数をセット
        $this->set("list", $list);
        $this->set("status", isset($this->params['pass']['0']) ? $this->params['pass']['0'] : 'r');
    }

    /**
     * メッセージ削除：送信箱
     *
     * 送信箱から削除する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function delete_snd()
    {
        
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        $user = $this->Auth->user();
        
        // トークンチェック
        if ($this->checkAjaxPost() == false) {
            return 'false';
        }
        
        // パスからメッセージIDを取得
        $msgid = $this->params['pass'][0];
        
        // データの有無のチェック
        if (! $this->Message->existsID($msgid)) {
            return 'false';
        }
        
        // 権限のあるユーザを登録
        $this->Permission->allowAdmin();
        
        $msg_stat = $this->Message->field('STATUS', array(
            'MSG_ID' => $msgid
        ));
        if ($msg_stat == Message::STATUS_SEND) {
            $this->Permission->allowSendUser($msgid);
        } else 
            if ($msg_stat == Message::STATUS_RECEIVE) {
                $this->Permission->allowRecieveUser($msgid);
            }
        
        // 権限チェック
        if ($this->Permission->isDenied($user['User']['USR_ID'])) {
            return 'not';
        }
        
        // メッセージの削除
        return $this->Message->deleteOutgoingMessage($msgid, $user);
    }

    /**
     * メッセージ削除：受信箱
     *
     * 受信箱から削除する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function delete_rcv()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // トークンチェック
        if ($this->checkAjaxPost() == false) {
            return 'false';
        }
        
        // パスからメッセージIDを取得
        $msgid = $this->params['pass'][0];
        
        // データの有無のチェック
        if (! $this->Message->existsID($msgid)) {
            return 'false';
        }
        
        // 権限のあるユーザを登録
        $this->Permission->allowAdmin();
        
        $msg_stat = $this->Message->field('STATUS', array(
            'MSG_ID' => $msgid
        ));
        if ($msg_stat == Message::STATUS_SEND) {
            $this->Permission->allowSendUser($msgid);
        } else 
            if ($msg_stat == Message::STATUS_RECEIVE) {
                $this->Permission->allowRecieveUser($msgid);
            }
        
        // 権限チェック
        if ($this->Permission->isDenied($user['User']['USR_ID'])) {
            return 'not';
        }
        
        // メッセージの削除
        return $this->Message->Delete_Message($msgid, $user);
    }

    /**
     * メッセージ表示
     *
     * メッセージを表示し既読にする
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function check()
    {
        // 初期化
        $this->set("main_title", "メッセージ");
        $this->set("title_text", "メッセージ");
        
        $user = $this->Auth->user();
        
        $detail = array();
        
        // 権限のあるユーザを登録
        $msgid = $this->params['pass'][0];
        if ($this->params['pass'][1] == 's') {
            $this->Permission->allowSendUser($msgid);
        } else 
            if ($this->params['pass'][1] == 'r') {
                $this->Permission->allowRecieveUser($msgid);
            }
        // 権限チェック
        $user_id = $user['User']['USR_ID'];
        
        if ($this->Permission->isDenied($user_id)) {
            $this->Session->setFlash('権限がありません');
            $this->redirect(array(
                'plugin' => false,
                'controller' => 'messages',
                'action' => 'index'
            ));
        }
        
        // データ有無のチェック
        if ($this->Message->existsID($this->params['pass'][0])) {
            
            // メッセージ内容を取得
            $detail = $this->Message->find('first', array(
                'conditions' => array(
                    'Message.MSG_ID' => $this->params['pass'][0]
                )
            ));
            
            // メッセージの取得
            $this->Message->virtualFields['MSG_G_ID'] = 0;
            $detail = $this->Message->getDestinationUsers($detail, $user);
            
            // メッセージを既読にする
            $params = array(
                'MSG_ID' => $detail['Message']['MSG_ID'],
                'RED' => 1
            );
            $this->Message->save($params);
        } else {
            
            // 無い場合の処理
            $this->redirect(array(
                'plugin' => false,
                'controller' => 'messages',
                'action' => 'index'
            ));
        }
        
        // 変数のセット
        $this->set("status", $this->params['pass'][1]);
        $this->set("message", $this->Message->Get_Message($this->Auth->user(), null, true));
        $this->set("detail", $detail);
    }

    /**
     * メッセージ作成
     *
     * メッセージを作成し保存する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function create()
    {
        
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        $this->set("main_title", "メッセージ");
        $this->set("title_text", "メッセージ作成");
        
        if (isset($this->params['pass'][0])) {
            
            if ($this->params['pass'][0] === 'new') {
                $this->Session->delete('Person');
            } elseif ($this->params['pass'][0] === 'del') {
                $d = $this->params['pass'][1];
                
                $this->Session->delete('Person.name_' . $d);
                $this->Session->delete('Person.id_' . $d);
                
                for ($a = 1; $a < 6; $a ++) {
                    $this->data['Message']['NAME' . $a] = $this->Session->read('Person.name_' . $a);
                    $this->data['Message']['ID_' . $a] = $this->Session->read('Person.id_' . $a);
                }
                
                $this->data['Message']['SUBJECT'] = $this->Session->read('Person.SUBJECT');
                $this->data['Message']['Message'] = $this->Session->read('Person.SUBJECT.Message');
            }
        }
        
        if (isset($this->params["named"]["no"])) {
            
            $k = $this->params["named"]["no"];
            
            $this->Session->write('Person.id_' . $k, $this->params["named"]["user_id" . $k]);
            $this->Session->write('Person.name_' . $k, $this->params["named"]["user_name" . $k]);
            
            for ($a = 1; $a < 6; $a ++) {
                $this->data['Message']['NAME' . $a] = $this->Session->read('Person.name_' . $a);
                $this->data['Message']['ID_' . $a] = $this->Session->read('Person.id_' . $a);
            }
            
            $this->data['Message']['SUBJECT'] = $this->Session->read('Person.SUBJECT');
            $this->data['Message']['Message'] = $this->Session->read('Person.SUBJECT.Message');
        }
        // 返信の場合の処理
        if (isset($this->data['Message']['S_USR_NAME'])) {
            
            $this->data['Message']['NAME1'] = $this->data['Message']['S_USR_NAME'];
            $this->data['Message']['ID_1'] = $this->data['Message']['USR_ID'];
            $this->Session->write('Person.id_1', $this->data['Message']['USR_ID']);
            $this->Session->write('Person.name_1', $this->data['Message']['S_USR_NAME']);
            
            $i = 0;
            while ($i < 5) {
                $j = $i + 2;
                if (isset($this->data['Message']['Users_NAME' . $i])) {
                    $this->data['Message']['NAME' . $j] = $this->data['Message']['Users_NAME' . $i];
                    $this->data['Message']['ID_' . $j] = $this->data['Message']['Users_ID' . $i];
                    $this->Session->write('Person.id_' . $j, $this->data['Message']['Users_ID' . $i]);
                    $this->Session->write('Person.name_' . $j, $this->data['Message']['Users_NAME' . $i]);
                }
                $i ++;
            }
            
            if (isset($this->data['Message']['SUBJECT'])) {
                $this->data['Message']['SUBJECT'] = "Re: " . $this->data['Message']['SUBJECT'];
                $this->Session->write('Person.SUBJECT', $this->data['Message']['SUBJECT']);
            }
        } elseif (isset($this->params['data']) && $this->params['data'] && $this->isCorrectToken($this->data['Security']['token'])) {
            
            // データがある場合
            $this->denyPrimaryKey("Message");
            
            // 送信
            if ($this->Message->Save_Msg($this->Auth->user(), $this->params['data'])) {
                // 成功
                echo "success";
                exit();
            } else {
                $this->Message->set($this->params['data']);
                $this->Message->validates();
                
                $m = 0;
                for ($n = 1; $n < 6; $n ++) {
                    $us = $this->User->find('first', array(
                        'conditions' => array(
                            'USR_ID' => $this->params['data']['Message']['ID_' . $n]
                        )
                    ));
                    $this->data['Message']['NAME' . $n] = $us['User']['NAME'];
                    if ($this->data['Message']['NAME' . $n] != NULL) {
                        $m ++;
                    }
                }
                
                if ($m == 0) {
                    $this->Message->validationErrors["USER"] = 'ユーザが一人も選択されていません。';
                }
            }
        }
        
        // 描画するviewを指定
        $this->render('create', false);
    }

    /**
     * メッセージ送信ユーザ取得
     *
     * メッセージを送信するユーザを取得する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function user()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        $conditions = array();
        
        // 選択できないユーザ
        $unselectable_user[0] = $user['User']['USR_ID'];
        
        for ($i = 1; $i < 6; $i ++) {
            if (! empty($_SESSION['Person']['id_' . $i])) {
                $unselectable_user[$i] = $_SESSION['Person']['id_' . $i];
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
                    'User.DEL_FLG' => '0',
                    'User.STATUS' => 1,
                    'NOT' => array(
                        'User.USR_ID' => $unselectable_user
                    )
                )
            )
        );
        
        // ユーザの取得
        $list = $this->paginate('User');
        
        // ナンバーの取得
        if (isset($this->params['pass'][0]) && $this->params['pass'][0]) {
            $this->set("no", $this->params['pass'][0]);
        }
        
        // 変数のセット
        $this->set("list", $list);
        $this->set("params", $this->params);
        
        // 描画するviewの指定
        $this->render('user', false);
    }

    /**
     * メッセージ送信
     *
     * メッセージを送信する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function send()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        if ($this->chechPost()) {
            
            // データがある場合
            // 送信
            if ($this->Message->Save_Msg($this->Auth->user(), $this->data)) {
                // 成功
                echo "success";
            } else {
                // 失敗
                echo "false";
            }
        } else {
            // 失敗
            echo "false";
        }
    }
}
