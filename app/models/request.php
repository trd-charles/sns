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
 * 申請情報用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Request extends AppModel
{

    /**
     * リクエストに関する定数
     *
     * @var number
     * @author 作成者
     */
    const TYPE_JOIN_SNS = 0; // 参加申請
    const TYPE_JOIN_SNS_DENY = 1; // 参加申請拒否
    const TYPE_JOIN_GROUP = 2; // グループ参加申請（ユーザ→グループ管理者）
    const TYPE_JOIN_GROUP_DENY = 3; // グループ参加申請拒否（グループ管理者→ユーザ）
    const TYPE_INVITE_GROUP = 4; // グループへ追加（グループ管理者→ユーザ ＊兼用）
    const TYPE_INVITE_SYSTEM_WITH_TOKEN = 5; // トークン付システム招待
    const TYPE_WAITING_ACTIVATION = 6; // 確認メールURLから有効化待ち状態
    const TYPE_INVITE_SELECT_GROUP = 7; // グループへの招待（グループ管理者→ユーザ）
    const TYPE_INVITE_SELECT_GROUP_DENY = 8; // グループ招待拒否（ユーザ→グループ管理者）
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Request';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_REQUEST';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'REQ_ID';

    /**
     * アソシエーションの定義
     *
     * @var array
     * @access public
     */
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'USR_ID'
        ),
        'Group' => array(
            'className' => 'Group',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'GRP_ID'
        )
    );

    /**
     * ユーザ参加申請
     *
     * @param unknown $_userid
     *            (ユーザのID)
     * @param unknown $usr_name
     *            (ユーザの名前)
     * @param string $_comfirm
     *            (確認待ち状態にするかどうか)
     * @param string $_approval            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function User_Request($_userid, $usr_name, $_comfirm = false, $_approval = false)
    {
        // 情報の整理
        $params = array();
        $params['USR_ID'] = $_userid;
        $params['TYPE'] = Request::TYPE_JOIN_SNS;
        
        if ($_comfirm && ! $_approval) {
            $params['TYPE'] = Request::TYPE_WAITING_ACTIVATION;
            $token = Security::hash(uniqid() . mt_rand());
            $params['TOKEN'] = $token;
            // $params['STATUS'] = 3;
        } else {
            $params['TYPE'] = Request::TYPE_JOIN_SNS;
            $params['MESSAGE'] = $usr_name . "さんが参加申請しました。";
            $token = Security::hash(uniqid() . mt_rand());
            $params['TOKEN'] = $token;
            // $params['STATUS'] = 3;
        }
        
        $params['INSERT_DATE'] = date("Y-m-d H:i:s");
        
        // 保存
        if ($this->save($params)) {
            // 成功
            return $token;
        }
        // 失敗
        return false;
    }

    /**
     * ユーザ参加申請
     *
     * @param unknown $_userid
     *            (ユーザのID)
     * @param unknown $_grpid
     *            (グループ参加申請)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Group_Request($_userid, $_grpid)
    {
        // ユーザモデルの読み込み
        App::import('Model', 'User');
        $user_m = new User();
        
        // グループモデルの読み込み
        App::import('Model', 'Group');
        $group_m = new Group();
        
        // ユーザの名前を取得
        $usr_name = $user_m->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $_userid
            )
        ));
        
        // グループの名前を取得
        $grp_name = $group_m->find('first', array(
            'conditions' => array(
                'Group.GRP_ID' => $_grpid
            )
        ));
        
        // 情報の整理
        $params = array();
        $params['USR_ID'] = $_userid;
        $params['TYPE'] = Request::TYPE_JOIN_GROUP;
        $params['GRP_ID'] = $_grpid;
        $params['MESSAGE'] = $usr_name['User']['NAME'] . "さんが" . $grp_name['Group']['NAME'] . "に参加申請しました。";
        $params['INSERT_DATE'] = date("Y-m-d H:i:s");
        
        // 保存
        if ($this->save($params)) {
            // 成功
            return true;
        }
        // 失敗
        return false;
    }

    /**
     * リクエスト削除
     *
     * @param unknown $_userid
     *            (ユーザのID)
     * @param unknown $_grpid
     *            (グループのId)
     * @return bool
     * @access public
     * @author 作成者
     */
    public function Delete_Request($_userid, $_grpid)
    {
        if ($_grpid != null) {
            return $this->deleteAll(array(
                'Request.USR_ID' => $_userid,
                'Request.GRP_ID' => $_grpid
            ));
        } else {
            return $this->deleteAll(array(
                'Request.USR_ID' => $_userid
            ));
        }
    }

    /**
     * 申請情報の取得
     *
     * @param unknown $_user
     *            (ユーザのID)
     * @return array
     * @access public
     * @author 作成者
     */
    public function Get_Request($_user)
    {
        $conditions = array();
        
        // 条件にグループを追加
        $conditions = array(
            '1' => array(
                'Request.USR_ID' => $_user['User']['USR_ID'],
                'OR' => array(
                    'Request.TYPE' => array(
                        Request::TYPE_INVITE_GROUP,
                        Request::TYPE_JOIN_GROUP_DENY,
                        Request::TYPE_INVITE_SELECT_GROUP
                    )
                )
            )
        );
        
        $id = array();
        $g_id = $this->Group->find('all', array(
            'fields' => array(
                'GRP_ID'
            ),
            'conditions' => array(
                'Group.USR_ID' => $_user['User']['USR_ID']
            )
        ));
        
        foreach ($g_id as $key => $val) {
            $flg = $this->find('all', array(
                'conditions' => array(
                    'Request.GRP_ID' => $val['Group']['GRP_ID']
                )
            ));
            if (! empty($flg)) {
                array_push($id, $val['Group']['GRP_ID']);
            }
        }
        
        $conditions = array_merge($conditions, array(
            '2' => array(
                'Request.GRP_ID' => $id,
                'OR' => array(
                    'Request.TYPE' => array(
                        Request::TYPE_JOIN_GROUP,
                        Request::TYPE_INVITE_SELECT_GROUP_DENY
                    )
                )
            )
        ));
        
        if ($_user['User']['AUTHORITY'] == User::AUTHORITY_TRUE) {
            // 管理者の場合、参加リクエストも追加
            $conditions = array_merge($conditions, array(
                '3' => array(
                    'Request.GRP_ID' => '0'
                )
            ));
        }
        
        // リクエスト情報をすべて取得
        $result = $this->find('all', array(
            'conditions' => array(
                'OR' => $conditions,
                'NOT' => array(
                    'Request.TYPE' => array(
                        Request::TYPE_INVITE_SYSTEM_WITH_TOKEN,
                        Request::TYPE_WAITING_ACTIVATION
                    )
                )
            ),
            'order' => 'REQ_ID DESC'
        ));
        
        return $result;
    }

    /**
     * リクエスト情報の変更
     *
     * @param unknown $_reqid
     *            (リクエストID)
     * @param unknown $_status
     *            (変える内容)
     * @param string $_user
     *            (ユーザID)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Change_Request($_reqid, $_status, $_user = null)
    {
        
        // 申請情報の取得
        $result = $this->find('first', array(
            'conditions' => array(
                'Request.REQ_ID' => $_reqid
            )
        ));
        
        $params = array();
        
        // トランザクションの開始
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        
        if ($_status == 'p') {
            // 許可
            // 申請情報の削除
            if ($result['Request']['TYPE'] == Request::TYPE_JOIN_SNS) {
                if ($this->save(array(
                    'REQ_ID' => $_reqid,
                    'TYPE' => Request::TYPE_WAITING_ACTIVATION
                ))) {
                    
                    // ユーザ参加申請の場合
                    // 管理者モデルの読み込み
                    App::import('Model', 'Administrator');
                    $admin = new Administrator();
                    
                    // ユーザを取得
                    $usr = $admin->find('first', array(
                        'conditions' => array(
                            'USR_ID' => $result['Request']['USR_ID']
                        )
                    ));
                    
                    // 情報の整理
                    $params = array();
                    $params = array(
                        'Administrator' => array(
                            'USR_ID' => $usr['Administrator']['USR_ID'],
                            'NAME' => $usr['Administrator']['NAME'],
                            'MAIL' => $usr['Administrator']['MAIL'],
                            'PASSWORD' => $usr['Administrator']['PASSWORD'],
                            'STATUS' => User::STATUS_WAITING_ACTIVATION
                        )
                    );
                    
                    // ユーザ保存
                    $admin->save($params);
                    
                    // コンポーネントの読み込み
                    App::import('Component', 'Common');
                    $common = new CommonComponent();
                    
                    // メールの用意
                    $url = Router::url(array(
                        'controller' => 'users',
                        'action' => 'active'
                    ), true) . '?token=' . $result['Request']['TOKEN'];
                    $to = $usr['Administrator']['MAIL'];
                    $subject = "【抹茶SNS】" . "参加承認のお知らせ";
                    $content = array(
                        'URL' => $url
                    );
                    
                    // メールの送信
                    if ($common->send_mail_beta($to, $subject, null, null, $content, 'join_approval', null, 0, 1)) {
                        // 成功
                        $dataSource->commit($this);
                        return true;
                    } else {
                        // 失敗
                        $dataSource->rollback($this);
                        return false;
                    }
                }
            } elseif ($result['Request']['TYPE'] == Request::TYPE_JOIN_GROUP) {
                if ($this->save(array(
                    'REQ_ID' => $_reqid,
                    'TYPE' => Request::TYPE_INVITE_GROUP
                ))) {
                    
                    // グループ参加申請
                    // 情報の整理
                    $params['USR_ID'] = $result['User']['USR_ID'];
                    $params['TYPE'] = Request::TYPE_INVITE_GROUP;
                    $params['GRP_ID'] = $result['Group']['GRP_ID'];
                    $params['MESSAGE'] = $result['Group']['NAME'] . "への参加申請が承認されました。";
                    $params['INSERT_DATE'] = date("Y-m-d H:i:s");
                    if ($this->save($params)) {
                        
                        // 参加情報の読み込み
                        App::import('Model', 'Join');
                        $join_m = new Join();
                        
                        // 情報の整理
                        $params = $join_m->find('first', array(
                            'conditions' => array(
                                'Join.GRP_ID' => $result['Request']['GRP_ID'],
                                'Join.USR_ID' => $result['Request']['USR_ID']
                            )
                        ));
                        $params['Join']['STATUS'] = Join::STATUS_JOINED;
                        $params['Join']['LAST_UPDATE'] = date("Y-m-d H:i:s");
                        
                        // 保存
                        if ($join_m->save($params)) {
                            // 成功
                            $dataSource->commit($this);
                            return true;
                        }
                    } else {
                        // 失敗
                        $dataSource->rollback($this);
                        return false;
                    }
                }
            } elseif ($result['Request']['TYPE'] == Request::TYPE_INVITE_SELECT_GROUP) {
                if ($this->save(array(
                    'REQ_ID' => $_reqid,
                    'TYPE' => Request::TYPE_INVITE_GROUP
                ))) {
                    
                    // グループ招待の場合
                    // 情報の整理
                    $params['USR_ID'] = $result['Group']['USR_ID'];
                    $params['TYPE'] = Request::TYPE_INVITE_GROUP;
                    $params['GRP_ID'] = $result['Group']['GRP_ID'];
                    $params['MESSAGE'] = $result['User']['NAME'] . "さんが" . $result['Group']['NAME'] . "への招待を承認しました。";
                    $params['INSERT_DATE'] = date("Y-m-d H:i:s");
                    
                    if ($this->save($params)) {
                        
                        // 参加情報の読み込み
                        App::import('Model', 'Join');
                        $join_m = new Join();
                        
                        // 情報の整理
                        $params = $join_m->find('first', array(
                            'conditions' => array(
                                'Join.GRP_ID' => $result['Request']['GRP_ID'],
                                'Join.USR_ID' => $result['Request']['USR_ID']
                            )
                        ));
                        $params['Join']['STATUS'] = Join::STATUS_JOINED;
                        $params['Join']['LAST_UPDATE'] = date("Y-m-d H:i:s");
                        
                        // 保存
                        if ($join_m->save($params)) {
                            // 成功
                            $dataSource->commit($this);
                            return true;
                        }
                    } else {
                        // 失敗
                        $dataSource->rollback($this);
                        return false;
                    }
                }
            }
        } elseif ($_status == 'r') {
            // 拒否の場合
            $params = array();
            
            if ($result['Request']['TYPE'] == Request::TYPE_JOIN_SNS) {
                // ユーザの参加申請の場合
                if ($this->delete($_reqid, false)) {
                    
                    // 管理者モデルの読み込み
                    App::import('Model', 'Administrator');
                    $admin = new Administrator();
                    
                    // リクエストユーザの情報を取得
                    $usr = $admin->find('first', array(
                        'conditions' => array(
                            'USR_ID' => $result['Request']['USR_ID']
                        )
                    ));
                    
                    // 仮登録を削除
                    if ($admin->delete($result['Request']['USR_ID'])) {
                        // 成功
                        // コンポーネントの読み込み
                        App::import('Component', 'Common');
                        $common = new CommonComponent();
                        // メール情報の整理
                        $to = $usr['Administrator']['MAIL'];
                        $subject = "【抹茶SNS】" . "参加拒否のお知らせ";
                        $content = array(
                            'USER_NAME' => $usr['Administrator']['NAME']
                        );
                        // メールの送信
                        if ($common->send_mail_beta($to, $subject, null, null, $content, 'join_deny', null, 0, 1)) {
                            // 成功
                            $dataSource->commit($this);
                            return true;
                        }
                    }
                } else {
                    // 失敗
                    $dataSource->rollback($this);
                    return false;
                }
            } elseif ($result['Request']['TYPE'] == Request::TYPE_JOIN_GROUP) {
                
                // グループ参加申請拒否の場合
                // 情報の整理
                $params['USR_ID'] = $result['User']['USR_ID'];
                $params['TYPE'] = Request::TYPE_JOIN_GROUP_DENY;
                $params['GRP_ID'] = $result['Group']['GRP_ID'];
                $params['MESSAGE'] = $result['Group']['NAME'] . "への参加申請が拒否されました。";
                $params['INSERT_DATE'] = date("Y-m-d H:i:s");
                
                // 申請の削除
                if ($this->delete($_reqid, false)) {
                    // 参加申請拒否を保存
                    if ($this->save($params)) {
                        
                        // 参加情報モデルの読み込み
                        App::import('Model', 'Join');
                        $join_m = new Join();
                        
                        // 参加情報の取得
                        $params = $join_m->find('first', array(
                            'conditions' => array(
                                'Join.GRP_ID' => $result['Request']['GRP_ID'],
                                'Join.USR_ID' => $result['Request']['USR_ID']
                            )
                        ));
                        
                        if ($join_m->delete($params['Join']['JIN_ID'])) {
                            // 参加情報の削除
                            $dataSource->commit($this);
                            return true;
                        }
                    } else {
                        // 失敗
                        $dataSource->rollback($this);
                        return false;
                    }
                }
            } elseif ($result['Request']['TYPE'] == Request::TYPE_INVITE_SELECT_GROUP) {
                // グループ招待拒否の場合
                // 情報の整理
                $params['USR_ID'] = $result['Group']['USR_ID'];
                $params['TYPE'] = Request::TYPE_INVITE_SELECT_GROUP_DENY;
                $params['GRP_ID'] = $result['Group']['GRP_ID'];
                $params['MESSAGE'] = $result['User']['NAME'] . "さんが" . $result['Group']['NAME'] . "への招待を拒否しました。";
                $params['INSERT_DATE'] = date("Y-m-d H:i:s");
                
                // 申請の削除
                if ($this->delete($_reqid, false)) {
                    // 参加申請拒否を保存
                    if ($this->save($params)) {
                        
                        // 参加情報モデルの読み込み
                        App::import('Model', 'Join');
                        $join_m = new Join();
                        
                        // 参加情報の取得
                        $params = $join_m->find('first', array(
                            'conditions' => array(
                                'Join.GRP_ID' => $result['Request']['GRP_ID'],
                                'Join.USR_ID' => $result['Request']['USR_ID']
                            )
                        ));
                        if ($join_m->delete($params['Join']['JIN_ID'])) {
                            
                            // 参加情報の削除
                            $dataSource->commit($this);
                            return true;
                        }
                    } else {
                        // 失敗
                        $dataSource->rollback($this);
                        return false;
                    }
                }
            }
        } elseif ($_status == 'c') {
            // 確認用
            if ($this->delete($_reqid, false)) {
                // 成功
                $dataSource->commit($this);
                return true;
            } else {
                // 失敗
                $dataSource->rollback($this);
                return false;
            }
        }
    }
}