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
 * 読んだ！のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Read extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Read';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_READ';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'RED_ID';

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
            'conditions' => array(
                'STATUS' => User::STATUS_ENABLED
            ),
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
     * 読んだ！
     *
     * @param unknown $_tmlid            
     * @param unknown $_userid            
     * @return unknown boolean
     * @access public
     * @author 作成者
     */
    public function Reads($_tmlid, $_userid)
    {
        $result = $this->find('first', array(
            'conditions' => array(
                'Read.TML_ID' => $_tmlid,
                'Read.USR_ID' => $_userid
            )
        ));
        
        if ($result != null) {
            if ($this->delete($result['Read']['RED_ID'])) {
                App::import('Model', 'Timeline');
                $timeline = new Timeline();
                $result2 = $timeline->find('first', array(
                    'fields' => array(
                        'RED_NUM'
                    ),
                    'conditions' => array(
                        'Timeline.TML_ID' => $_tmlid
                    )
                ));
                
                $num = $result2['Timeline']['RED_NUM'];
                if ($num > 0) {
                    $params2 = array();
                    $params2['TML_ID'] = $_tmlid;
                    $params2['RED_NUM'] = $num - 1;
                    $timeline->save($params2);
                }
                
                return $_tmlid;
            } else {
                return false;
            }
        } else {
            $params = array();
            $params['TML_ID'] = $_tmlid;
            $params['USR_ID'] = $_userid;
            
            if ($this->save($params)) {
                App::import('Model', 'Timeline');
                $timeline = new Timeline();
                $result = $timeline->find('first', array(
                    'fields' => array(
                        'RED_NUM'
                    ),
                    'conditions' => array(
                        'Timeline.TML_ID' => $_tmlid
                    )
                ));
                
                $num = $result['Timeline']['RED_NUM'];
                $params2 = array();
                $params2['TML_ID'] = $_tmlid;
                $params2['RED_NUM'] = $num + 1;
                
                $timeline->save($params2);
                return $_tmlid;
            } else {
                return false;
            }
        }
    }

    /**
     * 読んだ！検索
     *
     * @param unknown $_list            
     * @param unknown $_user            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Read_Search(&$_list, $_user)
    {
        if (! $_list) {
            return false;
        }
        
        foreach ($_list as $key => $val) {
            $result = $this->find('all', array(
                'conditions' => array(
                    'Read.TML_ID' => $val['Timeline']['TML_ID'],
                    'NOT' => array(
                        'Read.USR_ID' => $_user['User']['USR_ID']
                    )
                )
            ));
            
            $my_result = $this->find('all', array(
                'conditions' => array(
                    'Read.TML_ID' => $val['Timeline']['TML_ID'],
                    'Read.USR_ID' => $_user['User']['USR_ID']
                )
            ));
            
            if ($result != null) {
                $_list[$key]['READ']['Count'] = count($result);
                
                if (count($result) > 3) {
                    $_list[$key]['READ']['User1'] = $result[0]['User'];
                    $_list[$key]['READ']['User2'] = $result[1]['User'];
                    $_list[$key]['READ']['User3'] = $result[2]['User'];
                } elseif (count($result) == 2) {
                    $_list[$key]['READ']['User1'] = $result[0]['User'];
                    $_list[$key]['READ']['User2'] = $result[1]['User'];
                } else {
                    $_list[$key]['READ']['User1'] = $result[0]['User'];
                }
            } else {
                $_list[$key]['READ']['Count'] = 0;
            }
            
            if ($my_result != null) {
                $_list[$key]['READ']['MINE'] = true;
                $_list[$key]['READ']['User0'] = $my_result[0]['User'];
            } else {
                $_list[$key]['READ']['MINE'] = false;
            }
            
            if (isset($val['Timeline']['COMMENT']) && $val['Timeline']['COMMENT'] != null) {
                foreach ($val['Timeline']['COMMENT'] as $key2 => $val2) {
                    $result2 = $this->find('all', array(
                        'conditions' => array(
                            'Read.TML_ID' => $val2['Timeline']['TML_ID'],
                            'NOT' => array(
                                'Read.USR_ID' => $_user['User']['USR_ID']
                            )
                        )
                    ));
                    
                    $my_result2 = $this->find('all', array(
                        'conditions' => array(
                            'Read.TML_ID' => $val2['Timeline']['TML_ID'],
                            'Read.USR_ID' => $_user['User']['USR_ID']
                        )
                    ));
                    
                    if ($result2 != null) {
                        $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['Count'] = count($result2);
                        if (count($result2) > 3) {
                            $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['User1'] = $result2[0]['User'];
                            $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['User2'] = $result2[1]['User'];
                            $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['User3'] = $result2[2]['User'];
                        } elseif (count($result2) == 2) {
                            $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['User1'] = $result2[0]['User'];
                            $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['User2'] = $result2[1]['User'];
                        } else {
                            $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['User1'] = $result2[0]['User'];
                        }
                    } else {
                        $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['Count'] = 0;
                    }
                    
                    if ($my_result2 != null) {
                        $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['MINE'] = true;
                        $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['User0'] = $my_result2[0]['User'];
                    } else {
                        $_list[$key]['Timeline']['COMMENT'][$key2]['READ']['MINE'] = false;
                    }
                }
            }
        }
    }

    /**
     * 読んだ！削除
     *
     * @param unknown $_userid            
     * @return void
     * @access public
     * @author 作成者
     */
    public function Read_Delete($_userid)
    {
        $result = $this->find('all', array(
            'fields' => array(
                'Read.TML_ID'
            ),
            'conditions' => array(
                'Read.USR_ID' => $_userid
            )
        ));
        
        App::import('Model', 'Timeline');
        $timeline = new Timeline();
        
        foreach ($result as $key => $val) {
            $t = $timeline->find('first', array(
                'fields' => array(
                    'Timeline.RED_NUM',
                    'Timeline.TML_ID'
                ),
                'conditions' => array(
                    'Timeline.TML_ID' => $val['Read']['TML_ID']
                )
            ));
            
            $t['Timeline']['RED_NUM'] = $t['Timeline']['RED_NUM'] - 1;
            
            $timeline->create();
            $timeline->save($t);
        }
    }
}