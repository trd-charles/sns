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
 * フレンドのモデルクラス
 *
 * @author 作成者名
 *        
 */
class Friend extends AppModel
{

    /**
     * フォローステータスに関する定数
     *
     * @var number
     * @author 作成者
     */
    const STATUS_NOT_FOLLOWED = 0; // フォローされていない
    const STATUS_FOLLOWED = 1; // フォローされている
    
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Friend';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_FRIEND';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'FRD_ID';

    /**
     * 条件指定の取得のための関数
     *
     * @param unknown $_userid
     *            (ユーザID)
     * @param string $_name
     *            (名前条件)
     * @param string $_status
     *            (ステータス)
     * @param number $num            
     * @return array ($conditions 条件指定)
     * @access public
     * @author 作成者
     */
    public function Get_Friend_Status($_userid, $_name = false, $_status = false, $num = 10)
    {
        // prefixの取得
        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        $prefix = $db->config['prefix'];
        
        $result = array();
        $name_con = array();
        
        if ($_name) {
            // 名前条件が指定された場合
            $name_con = array(
                'NOT' => array(
                    'Administrator.USR_ID' => $_userid
                ),
                'Administrator.NAME LIKE' => "%$_name%",
                'Administrator.DEL_FLG' => '0',
                'Administrator.STATUS' => User::STATUS_ENABLED
            );
        } else {
            $name_con = array(
                'NOT' => array(
                    'Administrator.USR_ID' => $_userid
                ),
                'Administrator.DEL_FLG' => '0',
                'Administrator.STATUS' => User::STATUS_ENABLED
            );
        }
        
        if ($_status == 'r') {
            // フォローされているユーザ条件の取得
            $joins = array(
                array(
                    'type' => 'LEFT',
                    'table' => 'T_USER',
                    'alias' => 'Administrator',
                    'conditions' => 'Administrator.USR_ID = Friend.USR_ID',
                    'fields' => ''
                )
            );
            $conditions = array(
                'Friend' => array(
                    "fields" => array(
                        'Administrator.*'
                    ),
                    'conditions' => array_merge(array(
                        'Friend.F_USR_ID' => $_userid
                    ), $name_con),
                    'limit' => $num,
                    'joins' => $joins,
                    'order' => 'Friend.FRD_ID DESC'
                )
            );
        } elseif ($_status == 'n') {
            // フォローしていないユーザー条件の取得
            $conditions = array(
                'conditions' => array_merge(array(
                    'USR_ID NOT IN (SELECT F_USR_ID FROM ' . $db->fullTableName('T_FRIEND') . ' WHERE USR_ID = ' . $_userid . ')'
                ), $name_con),
                'limit' => $num,
                'order' => ''
            );
        } elseif ($_status == 'all') {
            // フォローしているユーザ条件の取得
            $joins = array(
                array(
                    'type' => 'LEFT',
                    'table' => 'T_FRIEND',
                    'alias' => 'Friend',
                    'conditions' => 'Administrator.USR_ID = Friend.F_USR_ID AND Friend.USR_ID = ' . $_userid,
                    'fields' => ''
                )
            );
            $conditions = array(
                'Administrator' => array(
                    'fields' => array(
                        'Administrator.*',
                        'Friend.F_USR_ID'
                    ),
                    'conditions' => $name_con,
                    'limit' => $num,
                    'joins' => $joins,
                    'order' => 'Administrator.USR_ID DESC'
                )
            );
        } else {
            // フォローしているユーザ条件の取得
            $joins = array(
                array(
                    'type' => 'LEFT',
                    'table' => 'T_USER',
                    'alias' => 'Administrator',
                    'conditions' => 'Administrator.USR_ID = Friend.F_USR_ID',
                    'fields' => ''
                )
            );
            $conditions = array(
                'Friend' => array(
                    "fields" => array(
                        'Administrator.*',
                        'Friend.F_USR_ID'
                    ),
                    'conditions' => array_merge(array(
                        'Friend.USR_ID' => $_userid
                    ), $name_con),
                    'limit' => $num,
                    'joins' => $joins,
                    'order' => 'Friend.FRD_ID DESC'
                )
            );
        }
        return $conditions;
    }

    /**
     * ユーザをフォローするための関数
     *
     * @param unknown $_userid
     *            (フォローする人のユーザID)
     * @param unknown $_c_userid
     *            (フォローされる人のユーザID)
     * @return boolean
     * @access private
     * @author 作成者
     */
    private function Friend_Create($_userid, $_c_userid)
    {
        $_data = array();
        
        // 情報の整理
        $_data['INSERT_DATE'] = date("Y-m-d H:i:s");
        $_data['USR_ID'] = $_userid;
        $_data['F_USR_ID'] = $_c_userid;
        $_data['STATUS'] = Friend::STATUS_FOLLOWED;
        
        // 保存
        if ($this->save($_data)) {
            return true;
        }
    }

    /**
     * フォローステータス変更の関数
     *
     * @param unknown $_userid
     *            (フォローする(はずす）人のユーザID)
     * @param unknown $_c_userid
     *            (フォローされる（はずされる）人のユーザID)
     * @return number boolean
     * @access public
     * @author 作成者
     */
    public function Change_Friend($_userid, $_c_userid)
    {
        // 現在のステータスを取得
        $follow = $this->find('first', array(
            'fields' => array(
                'FRD_ID',
                'STATUS'
            ),
            'conditions' => array(
                'Friend.USR_ID' => $_userid,
                'Friend.F_USR_ID' => $_c_userid
            )
        ));
        
        // 管理者モデルの取得（ユーザモデルの代わり)
        App::import('Model', 'Administrator');
        $admin = new Administrator();
        
        if ($follow['Friend']['STATUS'] == Friend::STATUS_FOLLOWED) {
            // フォロー取り消しの場合
            $this->delete($follow['Friend']['FRD_ID']);
            
            // フォロしている人数を減らす
            $following = $admin->find('first', array(
                'fields' => array(
                    'Administrator.FOLLOWING',
                    'Administrator.USR_ID'
                ),
                'conditions' => array(
                    'Administrator.USR_ID' => $_userid
                )
            ));
            
            $following['Administrator']['FOLLOWING'] --;
            $admin->save($following);
            
            // フォローされている人数を減らす
            $follower = $admin->find('first', array(
                'fields' => array(
                    'Administrator.FOLLOWER',
                    'Administrator.USR_ID'
                ),
                'conditions' => array(
                    'Administrator.USR_ID' => $_c_userid
                )
            ));
            $follower['Administrator']['FOLLOWER'] --;
            $admin->save($follower);
            
            return 0;
        } else {
            // フォローする
            
            if ($this->Friend_Create($_userid, $_c_userid)) {
                // フォローしている人数を増やす
                $following = $admin->find('first', array(
                    'fields' => array(
                        'Administrator.FOLLOWING',
                        'Administrator.USR_ID'
                    ),
                    'conditions' => array(
                        'Administrator.USR_ID' => $_userid
                    )
                ));
                $following['Administrator']['FOLLOWING'] ++;
                $admin->save($following);
                
                // フォローされている人数を減らす
                $follower = $admin->find('first', array(
                    'fields' => array(
                        'Administrator.FOLLOWER',
                        'Administrator.USR_ID'
                    ),
                    'conditions' => array(
                        'Administrator.USR_ID' => $_c_userid
                    )
                ));
                $follower['Administrator']['FOLLOWER'] ++;
                $admin->save($follower);
                
                return 1;
            }
        }
        return false;
    }

    /**
     * 友達の取得をする関数
     *
     * @param unknown $_userid
     *            (自分のユーザID)
     * @return string (フォローしているユーザのID)
     * @access public
     * @author 作成者
     */
    public function Get_Friend($_userid)
    {
        $result = $this->find('all', array(
            'fields' => array(
                'F_USR_ID'
            ),
            'conditions' => array(
                'Friend.USR_ID' => $_userid,
                'Friend.STATUS' => Friend::STATUS_FOLLOWED
            )
        ));
        
        return $result;
    }

    /**
     * 物理削除する
     *
     * @param unknown $userid            
     * @access public
     * @author 作成者
     */
    public function Delete_All($userid)
    {
        $conditions = array(
            'or' => array(
                '0' => array(
                    'Friend.F_USR_ID' => $userid
                ),
                '1' => array(
                    'Friend.USR_ID' => $userid
                )
            )
        );
        
        $this->deleteAll($conditions, false, false);
    }
}
