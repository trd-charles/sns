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
 * 参加用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Join extends AppModel
{

    /**
     * 参加ステータスに関する定数
     *
     * @var number
     * @author 作成者
     */
    const STATUS_ADMINISTRATOR = 0; // 管理者 - そのグループの管理者である状態
    const STATUS_WAITING = 1; // 申請中 - ユーザから管理者へグループ参加の申請をしている状態（現在未使用）
    const STATUS_JOINED = 2; // 参加中 - ユーザがグループへ参加している状態
    const STATUS_NOT_JOINED = 3; // 参加する - ユーザが退会した後に表示
    const STATUS_WAITING_JOIN = 8; // 参加待ち - 管理者がユーザを招待した状態
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Join';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_JOIN';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'JIN_ID';

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
     * ユーザ検索
     *
     * @param unknown $_grpid            
     * @param unknown $_userid            
     * @param string $_status            
     * @return array
     * @access public
     * @author 作成者
     */
    public function Search_User($_grpid, $_userid, $_status = null)
    {
        $conditions = array(
            'Join.GRP_ID' => $_grpid,
            'Join.USR_ID' => $_userid
        );
        
        if ($_status != null) {
            $conditions = array_merge($conditions, array(
                'or' => array(
                    'Join.STATUS' => $_status
                )
            ));
        }
        
        $result = $this->find('first', array(
            'fields' => array(
                'STATUS',
                'JIN_ID'
            ),
            'conditions' => $conditions
        ));
        
        return $result;
    }

    /**
     * 参加グループのグループIDを取得
     *
     * @param unknown $_userid            
     * @return array
     * @access public
     * @author 作成者
     */
    public function Join_Group($_userid)
    {
        $result = $this->find('all', array(
            'fields' => array(
                'GRP_ID'
            ),
            'conditions' => array(
                'Join.USR_ID' => $_userid,
                'Join.STATUS' => array(
                    Join::STATUS_ADMINISTRATOR,
                    Join::STATUS_JOINED
                )
            )
        ));
        
        return $result;
    }

    /**
     * グループにユーザが参加しているかを判断
     *
     * @param unknown $groupId            
     * @param unknown $userId            
     * @return boolean
     */
    public function isJoinFromGroupIdAndUserId($groupId, $userId)
    {
        $result = false;
        
        $count = $this->find('count', array(
            'conditions' => array(
                'Join.GRP_ID' => $groupId,
                'Join.USR_ID' => $userId,
                'Join.STATUS' => Join::STATUS_JOINED
            )
        ));
        
        if ($count > 0) {
            $result = true;
        }
        
        return $result;
    }

    /**
     * 参加グループのグループIDとグループ名を取得
     *
     * @param unknown $_userid            
     * @return unknown
     * @access publict
     * @author 作成者
     */
    public function getJoinGroupIdAndName($_userid, $authority)
    {
        $conditions = array(
            'Join.USR_ID' => $_userid,
            'Join.STATUS' => array(
                Join::STATUS_ADMINISTRATOR,
                Join::STATUS_JOINED
            )
        );
        
        // 管理者なら全部表示させる
        if ($authority == User::AUTHORITY_TRUE) {
            unset($conditions['Join.USR_ID']);
        }
        
        $result = $this->find('all', array(
            'fields' => array(
                'GRP_ID',
                'Group.NAME'
            ),
            'conditions' => $conditions
        ));
        
        return $result;
    }

    /**
     * グループユーザ取得
     *
     * @param unknown $_grpid            
     * @param number $num            
     * @return array
     * @access public
     * @author 作成者
     */
    public function Group_User($_grpid, $num = 10)
    {
        $result = $this->find('all', array(
            'conditions' => array(
                'User.STATUS' => User::STATUS_ENABLED,
                'Join.GRP_ID' => $_grpid,
                'Join.STATUS' => array(
                    Join::STATUS_ADMINISTRATOR,
                    Join::STATUS_JOINED
                )
            ),
            'limit' => $num
        ));
        
        return $result;
    }

    /**
     * グループへの参加
     *
     * @param unknown $_grpid            
     * @param unknown $_userid            
     * @param string $option            
     * @param string $auth            
     * @return Ambigous <unknown, string, NULL>|boolean|number
     * @access public
     * @author 作成者
     */
    public function Join_User($_grpid, $_userid, $option = null, $auth = false)
    {
        $_params = array();
        
        $status = $this->Search_User($_grpid, $_userid);
        
        App::import('Model', 'Request');
        $request = new Request();
        
        if ($status['Join']['STATUS'] == Join::STATUS_NOT_JOINED || $status == false) {
            App::import('Model', 'Group');
            $group = new Group();
            $type = $group->find('first', array(
                'fields' => array(
                    'TYPE',
                    'USR_ID'
                ),
                'conditions' => array(
                    'Group.GRP_ID' => $_grpid
                )
            ));
            
            $_params['GRP_ID'] = $_grpid;
            $_params['USR_ID'] = $_userid;
            
            if ($_userid == $type['Group']['USR_ID']) {
                $_params['STATUS'] = Join::STATUS_ADMINISTRATOR;
            } else {
                if ($type['Group']['TYPE'] == Group::TYPE_PRIVATE) {
                    if (! $auth) {
                        $_params['STATUS'] = Join::STATUS_WAITING;
                    } else {
                        $_params['STATUS'] = Join::STATUS_JOINED;
                    }
                } else {
                    $_params['STATUS'] = Join::STATUS_JOINED;
                }
            }
            
            $_params['INSERT_DATE'] = date("Y-m-d H:i:s");
            $_params['LAST_UPDATE'] = date("Y-m-d H:i:s");
            
            // トランザクションの開始
            $dataSource = $this->getDataSource();
            $dataSource->begin($this);
            
            if ($this->save($_params)) {
                if ($_params['STATUS'] == Join::STATUS_WAITING) {
                    if ($request->Group_Request($_userid, $_grpid)) {
                        $dataSource->commit($this);
                        return $_params['STATUS'];
                    } else {
                        return false;
                    }
                } else {
                    $dataSource->commit($this);
                    return $_params['STATUS'];
                }
            } else {
                $dataSource->rollback($this);
                return false;
            }
        } else {
            
            if ($status['Join']['STATUS'] == Join::STATUS_WAITING) {
                if (! $request->Delete_Request($_userid, $_grpid)) {
                    return false;
                } else {
                    $this->delete($status['Join']['JIN_ID']);
                    return 3;
                }
            } else {
                App::import('Model', 'Timeline');
                $timeline = new Timeline();
                
                App::import('Model', 'Watch');
                $watch = new Watch();
                
                $result = $timeline->find('all', array(
                    'fields' => array(
                        'Timeline.TML_ID'
                    ),
                    'conditions' => array(
                        'Timeline.ACT_ID' => array(
                            Timeline::ACT_ID_GROUP,
                            Timeline::ACT_ID_FILE_GROUP
                        ),
                        'Timeline.VAL_ID' => $_grpid
                    )
                ));
                foreach ($result as $key => $val) {
                    $res = $watch->find('first', array(
                        'fields' => array(
                            'Watch.WCH_ID'
                        ),
                        'conditions' => array(
                            'Watch.TML_ID' => $val['Timeline']['TML_ID'],
                            'Watch.USR_ID' => $_userid
                        )
                    ));
                    
                    if ($res != null) {
                        $watch->delete($res['Watch']['WCH_ID']);
                    }
                }
                
                $this->delete($status['Join']['JIN_ID']);
                return 3;
            }
        }
        return false;
    }

    /**
     * グループから物理削除
     *
     * @param unknown $userid            
     * @return void
     * @access public
     * @author 作成者
     */
    public function Delete_All($userid)
    {
        $conditions = array(
            'Join.USR_ID' => $userid
        );
        $this->deleteAll($conditions, false, false);
    }

    /**
     * グループの権限変更
     *
     * 副管理者 (STATUS_SEMI_ADMINISTRATOR) 4 ⇔ 一般ユーザ (STATUS_JOINED) 2
     *
     * @param unknown $_grpid
     *            (グループID)
     * @param unknown $_userid
     *            (ユーザID)
     * @return boolean (true:成功,false:失敗)
     * @access public
     * @author 作成者
     */
    public function Role_Change($_grpid, $_userid)
    {
        $_params = $this->find('first', array(
            'conditions' => array(
                'Join.GRP_ID' => $_grpid,
                'Join.USR_ID' => $_userid
            )
        ));
        
        if ($_params['Join']['STATUS'] == Join::STATUS_JOINED) {
            // 一般ユーザなら
            // 一般ユーザ → 副管理者
            $_params['Join']['STATUS'] = Join::STATUS_SEMI_ADMINISTRATOR;
        } elseif ($_params['Join']['STATUS'] == Join::STATUS_SEMI_ADMINISTRATOR) {
            // 副管理者なら
            // 副管理者 → 一般ユーザ
            $_params['Join']['STATUS'] = Join::STATUS_JOINED;
        }
        
        if ($this->save($_params)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ユーザ一覧を表示させる条件の設定
     *
     * @param unknown $_action
     *            (アクションなどの切り分け)
     * @param unknown $_grpid
     *            (グループID)
     * @param unknown $_user
     *            (ユーザ情報(Authでとってきた情報))
     * @param string $_keyword
     *            (検索のキーワード)
     * @return array (paginateの条件)
     * @access public
     * @author 作成者
     */
    public function Paginate_Conditions($_action, $_grpid, $_user, $_keyword = null)
    {
        // action role
        if ($_action == 'role_default') {
            $conditions = array(
                'Join.GRP_ID' => $_grpid,
                'Join.STATUS' => array(
                    Join::STATUS_SEMI_ADMINISTRATOR,
                    Join::STATUS_JOINED
                ),
                'NOT' => array(
                    'Administrator.USR_ID' => array(
                        $_user['User']['USR_ID']
                    ),
                    'Join.STATUS' => Join::STATUS_ADMINISTRATOR
                )
            );
        }         // action role search
        elseif ($_action == 'role_keyword') {
            $conditions = array(
                'Join.GRP_ID' => $_grpid,
                'Join.STATUS' => array(
                    Join::STATUS_SEMI_ADMINISTRATOR,
                    Join::STATUS_JOINED
                ),
                'Administrator.NAME LIKE' => "%{$_keyword}%",
                'NOT' => array(
                    'Administrator.USR_ID' => array(
                        $_user['User']['USR_ID']
                    ),
                    'Join.STATUS' => Join::STATUS_ADMINISTRATOR
                )
            );
        }         // action invite_user
        elseif ($_action == 'invite_user_default') {
            $_join = $this->find('all', array(
                'fields' => array(
                    'Join.USR_ID'
                ),
                'conditions' => array(
                    'Join.GRP_ID' => $_grpid
                )
            ));
            
            $join_or = array();
            foreach ($_join as $key => $val) {
                $join_or[$key] = $val['Join']['USR_ID'];
            }
            
            $conditions = array(
                'NOT' => array(
                    'User.USR_ID' => $join_or
                ),
                'AND' => array(
                    'User.STATUS' => User::STATUS_ENABLED
                )
            );
        }         // action invite_user search
        elseif ($_action == 'invite_user_keyword') {
            $_join = $this->find('all', array(
                'fields' => array(
                    'Join.USR_ID'
                ),
                'conditions' => array(
                    'Join.GRP_ID' => $_grpid
                )
            ));
            
            $join_or = array();
            foreach ($_join as $key => $val) {
                $join_or[$key] = $val['Join']['USR_ID'];
            }
            
            $conditions = array(
                'User.NAME LIKE' => "%{$_keyword}%",
                'NOT' => array(
                    'User.USR_ID' => $join_or
                ),
                'AND' => array(
                    'User.STATUS' => User::STATUS_ENABLED
                )
            );
        }
        return $conditions;
    }

    /**
     * グループの管理者のユーザーIDを返す
     *
     * @param unknown $_group_id
     *            (グループID)
     * @return array
     * @access public
     * @author 作成者
     */
    public function Get_Group_Admin($_group_id)
    {
        $user_id = $this->find('first', array(
            'fields' => array(
                'USR_ID'
            ),
            'conditions' => array(
                'Join.GRP_ID' => $_group_id,
                'Join.STATUS' => Join::STATUS_ADMINISTRATOR
            )
        ));
        
        return $user_id;
    }
}
