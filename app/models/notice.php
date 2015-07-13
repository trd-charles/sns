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
class Notice extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Notice';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_NOTICE';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'NTC_ID';

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
     * ビヘイビアを指定
     *
     * @var array
     * @access public
     */
    public $actsAs = array(
        'Cakeplus.AddValidationRule'
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
        ),
        'P_User' => array(
            'className' => 'User',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'P_USR_ID'
        ),
        'Group' => array(
            'className' => 'Group',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'GRP_ID'
        ),
        'Timeline' => array(
            'className' => 'Timeline',
            'conditions' => '',
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'TML_ID'
        )
    );

    /**
     * 通知数を取得する
     *
     * @param unknown $_userid            
     * @return number
     * @access public
     * @author 作成者
     */
    public function Get_Notice($_userid)
    {
        $count = 0;
        
        $result = array();
        $result = $this->find('all', array(
            'conditions' => array(
                'Notice.USR_ID' => $_userid['User']['USR_ID'],
                'Notice.STATUS' => 0,
                'NOT' => array(
                    'Notice.P_USR_ID' => $_userid['User']['USR_ID']
                )
            ),
            'order' => 'Notice.NTC_ID DESC'
        ));
        
        if ($result != null) {
            $count = count($result);
        }
        
        App::import('Model', 'User');
        $user = new User();
        
        foreach ($result as $key => $val) {
            if ($val['Notice']['ACT_ID'] == 2) {
                $tmp = $user->find('first', array(
                    'conditions' => array(
                        'User.USR_ID' => $val['Timeline']['USR_ID']
                    ),
                    'fields' => array(
                        'User.NAME'
                    )
                ));
                if ($tmp['User']['NAME'] == $val['P_User']['NAME']) {
                    $tmp['User']['NAME'] = "自分";
                } elseif ($tmp['User']['NAME'] == $val['User']['NAME']) {
                    $tmp['User']['NAME'] = "あなた";
                }
                $result[$key]['Timeline']['NAME'] = $tmp['User']['NAME'];
            }
        }
        
        $result['Count'] = $count;
        return $result;
    }

    /**
     * 通知を保存
     *
     * @param unknown $_data
     *            (投稿したデータ)
     * @param string $_userid
     *            (親スレのデータ)
     * @return void
     * @access public
     * @author 作成者
     */
    public function Save_Notice($_data, $_userid = null)
    {
        $params = array();
        
        if ($_userid != null) {
            if ($_userid['Timeline']['ACT_ID'] == 3) {
                App::import('Model', 'Join');
                $join = new Join();
                $joins = $join->find('all', array(
                    'conditions' => array(
                        'Join.GRP_ID' => $_userid['Timeline']['VAL_ID']
                    )
                ));
                
                foreach ($joins as $key => $val) {
                    if ($val['Join']['USR_ID'] == $_userid['Timeline']['USR_ID']) {
                        $params[$key]['USR_ID'] = $val['Join']['USR_ID'];
                        $params[$key]['P_USR_ID'] = $_data['USR_ID'];
                        $params[$key]['TML_ID'] = $_userid['Timeline']['TML_ID'];
                        $params[$key]['GRP_ID'] = $_userid['Timeline']['VAL_ID'];
                        $params[$key]['ACT_ID'] = 2;
                        
                        $n = $this->find('first', array(
                            'fields' => array(
                                'Notice.NTC_ID'
                            ),
                            'conditions' => array(
                                'Notice.USR_ID' => $params[$key]['USR_ID'],
                                'Notice.P_USR_ID' => $params[$key]['P_USR_ID'],
                                'Notice.TML_ID' => $params[$key]['TML_ID'],
                                'Notice.ACT_ID' => $params[$key]['ACT_ID']
                            )
                        ));
                        
                        if ($n) {
                            $params[$key]['NTC_ID'] = $n;
                        }
                        
                        $params[$key]['STATUS'] = 0;
                        $params[$key]['INSERT_DATE'] = date("Y-m-d H:i:s");
                    }
                }
                $this->saveAll($params);
            } elseif ($_userid['Timeline']['ACT_ID'] == 5) {
                
                App::import('Model', 'Storage');
                $storage = new Storage();
                $storages = $storage->find('first', array(
                    'fields' => array(
                        'Group.GRP_ID'
                    ),
                    'conditions' => array(
                        'Storage.FLE_ID' => $_userid['Timeline']['VAL_ID']
                    )
                ));
                
                App::import('Model', 'Join');
                $join = new Join();
                $joins = $join->find('all', array(
                    'conditions' => array(
                        'Join.GRP_ID' => $storages['Group']['GRP_ID'],
                        'Join.USR_ID' => $_userid['Timeline']['USR_ID']
                    )
                ));
                
                foreach ($joins as $key => $val) {
                    if ($val['Join']['USR_ID'] == $_userid['Timeline']['USR_ID']) {
                        $params[$key]['USR_ID'] = $val['Join']['USR_ID'];
                        $params[$key]['P_USR_ID'] = $_data['USR_ID'];
                        $params[$key]['TML_ID'] = $_userid['Timeline']['TML_ID'];
                        $params[$key]['GRP_ID'] = $storages['Group']['GRP_ID'];
                        $params[$key]['ACT_ID'] = 2;
                        $n = $this->find('first', array(
                            'fields' => array(
                                'Notice.NTC_ID'
                            ),
                            'conditions' => array(
                                'Notice.USR_ID' => $params[$key]['USR_ID'],
                                'Notice.P_USR_ID' => $params[$key]['P_USR_ID'],
                                'Notice.TML_ID' => $params[$key]['TML_ID'],
                                'Notice.ACT_ID' => $params[$key]['ACT_ID']
                            )
                        ));
                        
                        if ($n) {
                            $params[$key]['NTC_ID'] = $n['Notice']['NTC_ID'];
                        }
                        $params[$key]['STATUS'] = 0;
                        $params[$key]['INSERT_DATE'] = date("Y-m-d H:i:s");
                    }
                    $this->saveAll($params);
                }
            } else {
                $params['USR_ID'] = $_userid['Timeline']['USR_ID'];
                $params['P_USR_ID'] = $_data['USR_ID'];
                $params['TML_ID'] = $_data['VAL_ID'];
                $params['ACT_ID'] = 0;
                
                $n = $this->find('first', array(
                    'fields' => array(
                        'Notice.NTC_ID'
                    ),
                    'conditions' => array(
                        'Notice.USR_ID' => $params['USR_ID'],
                        'Notice.P_USR_ID' => $params['P_USR_ID'],
                        'Notice.TML_ID' => $params['TML_ID'],
                        'Notice.ACT_ID' => $params['ACT_ID']
                    )
                ));
                
                if ($n) {
                    $params['NTC_ID'] = $n['Notice']['NTC_ID'];
                }
                
                $params['STATUS'] = 0;
                $params['INSERT_DATE'] = date("Y-m-d H:i:s");
                
                $this->save($params);
            }
        } else {
            if ($_data['ACT_ID'] == 5) {
                App::import('Model', 'Storage');
                $storage = new Storage();
                $storages = $storage->find('first', array(
                    'fields' => array(
                        'Group.GRP_ID'
                    ),
                    'conditions' => array(
                        'Storage.FLE_ID' => $_data['VAL_ID']
                    )
                ));
                
                App::import('Model', 'Join');
                $join = new Join();
                $joins = $join->find('all', array(
                    'conditions' => array(
                        'Join.GRP_ID' => $storages['Group']['GRP_ID']
                    )
                ));
                
                foreach ($joins as $key => $val) {
                    $params[$key]['USR_ID'] = $val['Join']['USR_ID'];
                    $params[$key]['P_USR_ID'] = $_data['USR_ID'];
                    $params[$key]['TML_ID'] = $_data['TML_ID'];
                    $params[$key]['GRP_ID'] = $storages['Group']['GRP_ID'];
                    $params[$key]['ACT_ID'] = 1;
                    
                    $n = $this->find('first', array(
                        'fields' => array(
                            'Notice.NTC_ID'
                        ),
                        'conditions' => array(
                            'Notice.USR_ID' => $params[$key]['USR_ID'],
                            'Notice.P_USR_ID' => $params[$key]['P_USR_ID'],
                            'Notice.TML_ID' => $params[$key]['TML_ID'],
                            'Notice.ACT_ID' => $params[$key]['ACT_ID']
                        )
                    ));
                    
                    if ($n) {
                        $params[$key]['NTC_ID'] = $n['Notice']['NTC_ID'];
                    }
                    
                    $params[$key]['STATUS'] = 0;
                    $params[$key]['INSERT_DATE'] = date("Y-m-d H:i:s");
                }
                
                $this->saveAll($params);
            } else {
                App::import('Model', 'Join');
                $join = new Join();
                $joins = $join->find('all', array(
                    'conditions' => array(
                        'Join.GRP_ID' => $_data['VAL_ID']
                    )
                ));
                
                foreach ($joins as $key => $val) {
                    $params[$key]['USR_ID'] = $val['Join']['USR_ID'];
                    $params[$key]['P_USR_ID'] = $_data['USR_ID'];
                    $params[$key]['TML_ID'] = $_data['TML_ID'];
                    $params[$key]['GRP_ID'] = $_data['VAL_ID'];
                    $params[$key]['ACT_ID'] = 1;
                    
                    $n = $this->find('first', array(
                        'fields' => array(
                            'Notice.NTC_ID'
                        ),
                        'conditions' => array(
                            'Notice.USR_ID' => $params[$key]['USR_ID'],
                            'Notice.P_USR_ID' => $params[$key]['P_USR_ID'],
                            'Notice.TML_ID' => $params[$key]['TML_ID'],
                            'Notice.ACT_ID' => $params[$key]['ACT_ID']
                        )
                    ));
                    
                    if ($n) {
                        $params[$key]['NTC_ID'] = $n['Notice']['NTC_ID'];
                    }
                    
                    $params[$key]['STATUS'] = 0;
                    $params[$key]['INSERT_DATE'] = date("Y-m-d H:i:s");
                }
                $this->saveAll($params);
            }
        }
    }

    /**
     * 通知を更新
     *
     * @param unknown $_userid            
     * @return number
     * @access public
     * @author 作成者
     */
    public function Change($_userid)
    {
        $result = array();
        $result = $this->find('all', array(
            'conditions' => array(
                'Notice.USR_ID' => $_userid,
                'Notice.STATUS' => 0
            )
        ));
        
        $params = array();
        foreach ($result as $key => $val) {
            $params[$key]['NTC_ID'] = $val['Notice']['NTC_ID'];
            $params[$key]['STATUS'] = 1;
        }
        
        return $this->saveAll($params);
    }

    /**
     * 通知を物理削除
     *
     * @param unknown $_userid            
     * @return void
     * @access public
     * @author 作成者
     */
    public function Delete_Notice($_userid)
    {
        $params = array();
        $params = array(
            'Notice.USR_ID' => $_userid
        );
        
        $this->deleteAll($params);
        
        $params2 = array();
        $params2 = array(
            'Notice.P_USR_ID' => $_userid
        );
        
        $this->deleteAll($params2);
    }
}