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
 * ウォッチ用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Watch extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Watch';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_WATCH';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'WCH_ID';

    /**
     * コンポーネントを指定
     *
     * @var array
     * @access public
     */
    public $components = array(
        'Auth'
    );

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
        )
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
     * ウォッチ保存
     *
     * @param unknown $_tmlid            
     * @param unknown $_userid            
     * @return unknown boolean
     * @access public
     * @author 作成者
     */
    public function Watch_Save($_tmlid, $_userid)
    {
        $result = $this->find('first', array(
            'conditions' => array(
                'Watch.TML_ID' => $_tmlid,
                'Watch.USR_ID' => $_userid
            )
        ));
        
        if ($result != null) {
            if ($this->delete($result['Watch']['WCH_ID'])) {
                return $_tmlid;
            } else {
                return false;
            }
        } else {
            $params = array();
            $params['TML_ID'] = $_tmlid;
            $params['USR_ID'] = $_userid;
            
            if ($this->save($params)) {
                return $_tmlid;
            } else {
                return false;
            }
        }
    }

    /**
     * ウォッチ検索
     *
     * @param unknown $_list            
     * @param unknown $_user            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Watch_Search(&$_list, $_user)
    {
        if (! $_list) {
            return false;
        }
        
        foreach ($_list as $key => $val) {
            $result = $this->find('all', array(
                'conditions' => array(
                    'Watch.TML_ID' => $val['Timeline']['TML_ID'],
                    'Watch.USR_ID' => $_user['User']['USR_ID']
                )
            ));
            
            if ($result != null) {
                $_list[$key]['Watch']['MINE'] = true;
            } else {
                $_list[$key]['Watch']['MINE'] = false;
            }
        }
    }

    /**
     * タイムライン取得
     *
     * @param unknown $_user            
     * @param string $grpid            
     * @return array ($conditions)
     * @access public
     * @author 作成者
     */
    public function Get_Timeline($_user, $grpid = null)
    {
        $result = array();
        
        // 参加情報モデルの読み込み
        App::import('Model', 'Join');
        $join = new Join();
        
        // 参加しているグループの取得とリスト化
        $jg = $join->Join_Group($_user['User']['USR_ID']);
        $gid_or = array();
        foreach ($jg as $key => $val) {
            $gid_or[$key] = $val['Join']['GRP_ID'];
        }
        
        $joins = array(
            0 => array(
                'type' => 'INNER',
                'table' => 'T_WATCH',
                'alias' => 'Watch',
                'conditions' => array(
                    'Watch.TML_ID = Timeline.TML_ID',
                    'Watch.USR_ID' => $_user['User']['USR_ID']
                )
            ),
            1 => array(
                'type' => 'LEFT',
                'table' => 'T_FILE',
                'alias' => 'Storageg',
                'conditions' => array(
                    'Timeline.VAL_ID = Storageg.FLE_ID',
                    'Storageg.PUBLIC' => 1
                )
            ),
            2 => array(
                'type' => 'LEFT',
                'table' => 'M_GROUP',
                'alias' => 'GroupType',
                'conditions' => array(
                    'GroupType.GRP_ID = Storageg.GRP_ID'
                )
            )
        );
        
        $conditions = array(
            'Timeline' => array(
                "fields" => array(
                    'Timeline.*',
                    'User.*',
                    'Group.*',
                    'Storage.*',
                    'Watch.WCH_ID'
                ),
                'conditions' => array(
                    'OR' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE
                        ),
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.VAL_ID' => $gid_or,
                            'Group.TYPE' => array(
                                Group::TYPE_PUBLIC,
                                Group::TYPE_PRIVATE
                            )
                        ),
                        '4' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Group.TYPE' => Group::TYPE_PUBLIC
                        ),
                        '5' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Storageg.GRP_ID' => $gid_or,
                            'Group.TYPE' => array(
                                Group::TYPE_PUBLIC,
                                Group::TYPE_PRIVATE
                            )
                        ),
                        '6' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'GroupType.TYPE' => 0
                        )
                    )
                ),
                'limit' => 20,
                'joins' => $joins,
                'order' => 'Watch.WCH_ID DESC'
            )
        );
        
        return $conditions;
    }
}