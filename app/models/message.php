<?php

/**
 * Matcha-SNS
 *
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author
 *
 *
 * @version $Id$
 */
/**
 * メッセージ用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Message extends AppModel
{

    /**
     * メッセージステータスに関する定数
     *
     * @var number
     * @author 作成者
     */
    const STATUS_SEND = 0;

    const STATUS_RECEIVE = 1;

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Message';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_MESSAGE';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'MSG_ID';

    /**
     * アソシエーションの定義
     *
     * @var array
     * @access public
     */
    public $belongsTo = array(
        'S_User' => array(
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'S_USR_ID'
        ),
        'R_User' => array(
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'R_USR_ID'
        )
    );

    /**
     * レコードのソート順の初期設定
     *
     * @var array
     * @access public
     */
    public $order = array(
        'Message.INSERT_DATE DESC'
    );

    /**
     * ビヘイビアを指定
     *
     * @var array
     * @access public
     */
    public $actsAs = array(
        'Cakeplus.AddValidationRule'
    );

    /**
     * バリデーションの設定
     *
     * @var array
     * @access public
     */
    public $validate = array(
        'MESSAGE' => array(
            'rule0' => array(
                'rule' => array(
                    'maxLengthJP',
                    1000
                ),
                'message' => 'メッセージは1000文字までです'
            )
        ),
        'S_USR_ID' => array(
            "rule1" => array(
                'rule' => 'notEmpty',
                'message' => '送信者は必須項目です'
            )
        ),
        'R_USR_ID' => array(
            "rule1" => array(
                'rule' => 'notEmpty',
                'message' => '宛先は必須項目です'
            )
        ),
        'SUBJECT' => array(
            'rule0' => array(
                'rule' => array(
                    'maxLengthJP',
                    40
                ),
                'message' => '件名は40文字までです'
            ),
            "rule1" => array(
                'rule' => 'notEmpty',
                'message' => '件名は必須項目です'
            )
        )
    );

    /**
     * メッセージの保存
     *
     * 0:送信,1:受信
     *
     * @param unknown $_user
     *            (ユーザの情報)
     * @param unknown $_data
     *            (送信内容)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Save_Msg($_user, $_data)
    {
        $params = array();
        $i = 1;
        
        // 管理者モデルの読み込み
        App::import('Model', 'Administrator');
        $administrator = new Administrator();
        
        $i = 1;
        $no = 1;
        
        // 情報の整理
        while ($no < 6) {
            if ((isset($_data['Message']['ID_' . $no]) && $_data['Message']['ID_' . $no] != null)) {
                $tmp = $administrator->find('first', array(
                    'fields' => array(
                        'Administrator.USR_ID',
                        'Administrator.NAME'
                    ),
                    'conditions' => array(
                        'Administrator.USR_ID' => $_data['Message']['ID_' . $no]
                    )
                ));
                $params[$i - 1]['S_USR_ID'] = $_user['User']['USR_ID'];
                $params[$i - 1]['S_NAME'] = $_user['User']['NAME'];
                $params[$i - 1]['R_USR_ID'] = $tmp['Administrator']['USR_ID'];
                $params[$i - 1]['R_NAME'] = $tmp['Administrator']['NAME'];
                $params[$i - 1]['MESSAGE'] = $_data['Message']['MESSAGE'];
                $params[$i - 1]['SUBJECT'] = $_data['Message']['SUBJECT'];
                $params[$i - 1]['INSERT_DATE'] = date("Y-m-d H:i:s");
                $params[$i - 1]['RED'] = 1;
                $params[$i - 1]['STATUS'] = Message::STATUS_SEND;
                $i ++;
            }
            $no ++;
        }
        
        // 全て保存
        if ($this->saveAll($params)) {
            
            foreach ($params as $key => $val) {
                $params[$key]['RED'] = 0;
                $params[$key]['STATUS'] = Message::STATUS_RECEIVE;
            }
            
            $this->create();
            $this->saveAll($params);
            
            return true;
        } else {
            return false;
        }
    }

    /**
     * メッセージ取得
     *
     * @param unknown $_user
     *            (ユーザの情報)
     * @param string $_status
     *            (送信、受信)
     * @param string $frag            
     * @return array
     * @access public
     * @author 作成者
     */
    public function Get_Message($_user, $_status = null, $frag = null)
    {
        if ($frag != null) {
            $result = $this->find('all', array(
                'conditions' => array(
                    'Message.RED' => 0,
                    'Message.R_USR_ID' => $_user['User']['USR_ID'],
                    'Message.STATUS' => Message::STATUS_RECEIVE
                )
            ));
            return $result;
        }
        
        if ($_status == 's') {
            $result = array(
                'Message.S_USR_ID' => $_user['User']['USR_ID'],
                'Message.STATUS' => Message::STATUS_SEND
            );
        } else {
            $result = array(
                'Message.R_USR_ID ' => $_user['User']['USR_ID'],
                'Message.STATUS' => Message::STATUS_RECEIVE
            );
        }
        
        return $result;
    }

    /**
     * メッセージ詳細画面で送信者を表示
     *
     * @param unknown $message            
     * @param unknown $user            
     * @return string
     * @access public
     * @author 作成者
     */
    public function getDestinationUsers($message, $user)
    {
        if ($message['S_User']['NAME'] != null) { // 送信者が現存する場合
            $tmp = array(
                'Message.INSERT_DATE' => $message['Message']['INSERT_DATE'],
                'Message.SUBJECT' => $message['Message']['SUBJECT'],
                'Message.STATUS' => Message::STATUS_SEND,
                'NOT' => array(
                    'Message.R_USR_ID' => $user["User"]["USR_ID"]
                )
            );
        } elseif ($message['S_User']['NAME'] == null) { // 送信者が削除されている場合
            $tmp = array(
                'Message.INSERT_DATE' => $message['Message']['INSERT_DATE'],
                'Message.SUBJECT' => $message['Message']['SUBJECT'],
                'Message.STATUS' => Message::STATUS_RECEIVE,
                'NOT' => array(
                    'Message.R_USR_ID' => $user["User"]["USR_ID"]
                )
            );
        }
        
        $users = $this->find('all', array(
            'conditions' => $tmp,
            'fields' => array(
                'R_User.NAME',
                'R_User.USR_ID',
                'Message.R_NAME'
            )
        ));
        
        $message['Users'] = $users;
        
        return $message;
    }

    /**
     * 全ての届け先ユーザ
     *
     * @param unknown $list            
     * @param unknown $user            
     * @return array
     * @access public
     * @author 作成者
     */
    public function getAllDestinationUsers($list, $user)
    {
        foreach ($list as $key => $message) {
            $message = $this->getDestinationUsers($message, $user);
            $list[$key] = $message;
        }
        
        return $list;
    }

    /**
     * メッセージの物理削除
     *
     * 返り値：成功した場合そのID 失敗した場合 'false'権限がない場合 'not'
     *
     * @param unknown $_msgid            
     * @param unknown $_user            
     * @return Ambigous <NULL>|string
     * @access public
     * @author 作成者
     */
    public function Delete_Message($_msgid, $_user)
    {
        $params = array();
        
        // メッセージ情報を取得
        $result = $this->find('first', array(
            'conditions' => array(
                'Message.MSG_ID' => $_msgid
            ),
            'fields' => 'Message.S_USR_ID,Message.R_USR_ID,Message.MSG_ID,Message.DEL_FLG'
        ));
        
        $params['MSG_ID'] = $result['Message']['MSG_ID'];
        
        // トランザクションの開始
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        if ($result['Message']['S_USR_ID'] == $_user['User']['USR_ID']) {
            
            // 受信者とユーザIDが同じ場合
            // 保存
            if ($this->delete($params['MSG_ID'])) {
                // 成功
                $dataSource->commit($this);
                return $params['MSG_ID'];
            } else {
                // 失敗
                $dataSource->rollback($this);
                return 'false';
            }
        } elseif ($result['Message']['R_USR_ID'] == $_user['User']['USR_ID']) {
            
            // 送信者とユーザが同じ場合
            // 保存
            if ($this->delete($params['MSG_ID'])) {
                // 成功
                $dataSource->commit($this);
                return $params['MSG_ID'];
            } else {
                // 失敗
                $dataSource->rollback($this);
                return 'false';
            }
        } else {
            // 権限がない場合
            $dataSource->rollback($this);
            return 'not';
        }
    }

    /**
     * メッセージの削除
     *
     * @param unknown $_msgid            
     * @param unknown $user            
     * @return string
     * @access public
     * @author 作成者
     */
    public function deleteOutgoingMessage($_msgid, $user)
    {
        $params = array();
        // メッセージIDから送信者、件名、送信日時を取り出す
        $result1 = $this->find('first', array(
            'conditions' => array(
                'Message.MSG_ID' => $_msgid
            ),
            'fields' => 'Message.STATUS,Message.S_USR_ID,Message.SUBJECT,Message.INSERT_DATE'
        ));
        
        $delData['S_USR_ID'] = $result1['Message']['S_USR_ID'];
        $delData['SUBJECT'] = $result1['Message']['SUBJECT'];
        $delData['INSERT_DATE'] = $result1['Message']['INSERT_DATE'];
        
        // 送信者、件名、送信日時が同一のメッセージを消去する
        $result = $this->find('all', array(
            'conditions' => array(
                'Message.SUBJECT' => $delData['SUBJECT'],
                'Message.INSERT_DATE' => $delData['INSERT_DATE'],
                'Message.S_USR_ID' => $delData['S_USR_ID'],
                'Message.STATUS' => Message::STATUS_SEND
            ),
            'fields' => 'Message.S_USR_ID,Message.R_USR_ID,Message.MSG_ID,Message.DEL_FLG'
        ));
        
        foreach ($result as $val) {
            $params['MSG_ID'] = $val['Message']['MSG_ID'];
            
            // トランザクションの開始
            $dataSource = $this->getDataSource();
            $dataSource->begin($this);
            if ($val['Message']['S_USR_ID'] == $user['User']['USR_ID']) {
                // 送信者とユーザIDが同じ場合
                // 保存
                if ($this->delete($params['MSG_ID'])) {
                    // 成功
                    $dataSource->commit($this);
                    // return $params['MSG_ID'];
                } else {
                    // 失敗
                    $dataSource->rollback($this);
                    return 'false';
                }
            } else {
                // 権限がない場合
                $dataSource->rollback($this);
                return 'not';
            }
        }
        
        return $result[0]['Message']["MSG_ID"];
    }

    /**
     * 指定メッセージの物理削除
     *
     * @param unknown $userid            
     * @return void
     * @access public
     * @author 作成者
     */
    public function Delete_All($userid)
    {
        $snd = $this->find('all', array(
            'conditions' => array(
                'Message.S_USR_ID' => $userid,
                'Message.STATUS' => Message::STATUS_SEND
            )
        ));
        $rcv = $this->find('all', array(
            'conditions' => array(
                'Message.R_USR_ID' => $userid,
                'Message.STATUS' => Message::STATUS_RECEIVE
            )
        ));
        
        foreach ($snd as $key => $val) {
            $params['MSG_ID'] = $val['Message']['MSG_ID'];
            $this->delete($params);
        }
        foreach ($rcv as $key => $val) {
            $params['MSG_ID'] = $val['Message']['MSG_ID'];
            $this->delete($params);
        }
    }

    /**
     * ページネーションのカウント数を返す
     *
     * @param string $conditions            
     * @param number $recursive            
     * @param array $extra            
     * @return number
     */
    public function paginateCount($conditions = null, $recursive = 0, $extra = array())
    {
        $parameters = compact('conditions');
        $this->recursive = $recursive;
        $count = $this->find('count', array_merge($parameters, $extra));
        
        if (isset($extra['group'])) {
            $count = $this->getAffectedRows();
        }
        
        return $count;
    }
}
