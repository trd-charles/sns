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
 * タイムライン用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Timeline extends AppModel
{

    /**
     * タイムラインに関する定数
     *
     * @var number
     * @author 作成者
     */
    const ACT_ID_TIMELINE = 1; // タイムライン
    const ACT_ID_COMMENT = 2; // コメント
    const ACT_ID_GROUP = 3; // グループ投稿
    const ACT_ID_FILE_TIMELINE = 4; // ファイル投稿（タイムライン）
    const ACT_ID_FILE_GROUP = 5; // ファイル投稿（グループ）
    
    /**
     * タイムラインソートに関する定数
     *
     * @var number
     * @author 作成者
     */
    const ORDER_NEW_INCLUDING_COMMENTS = 0; // 新しい発言順
    const ORDER_NEW = 1; // 新しい発言順
    const ORDER_OLD = 2; // 古い発言順
    const ORDER_READ = 3; // 読んだ!の数順
    
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Timeline';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_TIME_LINE';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'TML_ID';

    /**
     * tmlid
     *
     * @var unknown
     */
    public $tmlid;

    /**
     * アソシエーションの定義
     *
     * @var array
     * @access public
     */
    public $belongsTo = array(
        'Storage' => array(
            'className' => 'Storage',
            'conditions' => array(
                'Timeline.ACT_ID' => array(
                    Timeline::ACT_ID_FILE_TIMELINE,
                    Timeline::ACT_ID_FILE_GROUP
                )
            ),
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'VAL_ID',
            'fields' => array(
                'FLE_ID',
                'RAND_NAME',
                'GRP_ID',
                'F_TYPE',
                'F_SIZE',
                'ORIGINAL_NAME',
                'EXTENSION'
            )
        ),
        'Group' => array(
            'className' => 'Group',
            'conditions' => array(
                'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP
            ),
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'VAL_ID',
            'fields' => array(
                'GRP_ID',
                'NAME',
                'USR_ID'
            )
        ),
        'User' => array(
            'className' => 'User',
            'conditions' => array(
                'STATUS' => User::STATUS_ENABLED
            ),
            'order' => '',
            'dependent' => true,
            'foreignKey' => 'USR_ID',
            'fields' => array(
                'USR_ID',
                'GRP_ID',
                'DIRECTORY1',
                'DIRECTORY2',
                'NAME'
            )
        )
    );

    /**
     * レコードのソート順の初期設定
     *
     * @var array
     * @access public
     */
    public $order = array(
        'Timeline.INSERT_DATE DESC'
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
                'message' => 'メッセージは1000文字以内で入力してください。'
            ),
            'rule1' => array(
                'rule' => array(
                    'minLengthJP',
                    0
                ),
                'message' => 'メッセージに何も入力されていません。'
            ),
            'rule2' => array(
                'rule' => array(
                    'ngWord'
                ),
                'message' => '禁止ワードが含まれているため、投稿できません。'
            )
        )
    );

    /**
     * NGWord
     *
     * NGWORDが一致したらエラー
     *
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function ngWord()
    {
        App::import('Model', 'Configuration');
        $conf = new Configuration();
        
        $ngword_v = $conf->find('first', array(
            'conditions' => array(
                'NAME' => 'NGWORD'
            )
        ));
        
        if (empty($ngword_v)) {
            return true;
        }
        
        $array = array_values($ngword_v["Configurations"]);
        $ng = split("\n", $array[2]);
        
        foreach ($ng as $val) {
            $str = str_replace(array(
                "\r\n",
                "\r",
                "\n"
            ), '', $val); // 改行削除
            if (empty($str))
                continue;
            if (preg_match("/$str/", $this->data['Timeline']['MESSAGE'])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * コメント取得
     *
     * @param unknown $_list
     *            (タイムラインのリスト)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Comment_Search(&$_list)
    {
        if (! $_list) {
            return false;
        }
        
        foreach ($_list as $key => $val) {
            if ($val['Timeline']['ACT_ID'] != Timeline::ACT_ID_TIMELINE && $val['Timeline']['ACT_ID'] != Timeline::ACT_ID_GROUP) {
                $group = $this->Group->find('first', array(
                    'fields' => array(
                        'NAME',
                        'USR_ID',
                        'GRP_ID'
                    ),
                    'conditions' => array(
                        'Group.GRP_ID' => $val['Storage']['GRP_ID']
                    )
                ));
                
                $_list[$key]['Group'] = $group['Group'];
            }
            
            $result = $this->find('all', array(
                'conditions' => array(
                    'Timeline.VAL_ID' => $val['Timeline']['TML_ID'],
                    'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT
                ),
                'order' => 'Timeline.INSERT_DATE'
            ));
            
            if ($result != null) {
                $_list[$key]['Timeline']['COMMENT'] = $result;
            }
        }
        return true;
    }

    /**
     * 保存用の関数
     *
     * @param unknown $_model
     *            (保存場所)
     * @param unknown $_data
     *            (保存したいデータ)
     * @param unknown $_user
     *            (保存したいユーザ)
     * @param string $_group
     *            (保存するグループのID)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Save_Message($_model, $_data, $_user, $_group = null)
    {
        $param = array();
        if ($_model == 'Comment') {
            // コメントの保存
            $param['ACT_ID'] = Timeline::ACT_ID_COMMENT;
            $param['USR_ID'] = $_user['User']['USR_ID'];
            $param['VAL_ID'] = $_data['Timeline']['TML_ID'];
            $param['MESSAGE'] = $_data['Timeline']['COMMENT'];
            $param['INSERT_DATE'] = date("Y-m-d H:i:s");
        } elseif ($_model == 'Group') {
            // グループへの投稿
            $param['ACT_ID'] = Timeline::ACT_ID_GROUP;
            $param['USR_ID'] = $_user['User']['USR_ID'];
            $param['VAL_ID'] = $_group;
            $param['MESSAGE'] = $_data['Timeline']['MESSAGE'];
            $param['INSERT_DATE'] = date("Y-m-d H:i:s");
            $param['LAST_DATE'] = date("Y-m-d H:i:s");
        } else {
            // その他
            $param['ACT_ID'] = Timeline::ACT_ID_TIMELINE;
            $param['USR_ID'] = $_user['User']['USR_ID'];
            
            if ($_group != null) {
                $param['VAL_ID'] = $_group;
            } else {
                $param['VAL_ID'] = $_user['User']['GRP_ID'];
            }
            
            $param['MESSAGE'] = $_data['Timeline']['MESSAGE'];
            $param['INSERT_DATE'] = date("Y-m-d H:i:s");
            $param['LAST_DATE'] = date("Y-m-d H:i:s");
        }
        
        // 保存
        if ($this->save($param)) {
            $id = $this->getInsertID();
            $param['TML_ID'] = $id;
            
            if ($_model == 'Comment') {
                
                // コメントに対する投稿データを読み込み
                $tmlid = $this->find('first', array(
                    'conditions' => array(
                        'Timeline.TML_ID' => $param['VAL_ID']
                    )
                ));
                
                // 投稿データ.変更日を設定
                $tmlid['Timeline']['LAST_DATE'] = $param['INSERT_DATE'];
                
                // 更新
                $this->save($tmlid);
                
                // 管理者モデルの読み込み
                App::import('Model', 'Notice');
                $notice = new Notice();
                $notice->Save_Notice($param, $this->find('first', array(
                    'conditions' => array(
                        'Timeline.TML_ID' => $param['VAL_ID']
                    )
                )));
            } elseif ($_model == 'Group') {
                $param['TML_ID'] = $id;
                
                // 管理者モデルの読み込み
                App::import('Model', 'Notice');
                $notice = new Notice();
            }
            return $id;
        } else {
            return false;
        }
    }

    /**
     * ファイル保存
     *
     * @param unknown $_data
     *            (保存したいデータ)
     * @param unknown $_user
     *            (保存したいユーザ)
     * @param unknown $_fleid
     *            (ファイルのID)
     * @param unknown $_mclass
     *            (保存場所)
     * @param string $comment
     *            (ファイルの投稿と一緒に保存したい投稿)
     * @return array
     * @access public
     * @author 作成者
     */
    public function Save_File($_data, $_user, $_fleid, $_mclass, $comment = null)
    {
        $param = array();
        
        if ($_mclass == 'Group') {
            // グループの場合
            $param['ACT_ID'] = Timeline::ACT_ID_FILE_GROUP;
            $param['USR_ID'] = $_user['User']['USR_ID'];
            $param['VAL_ID'] = $_fleid;
            
            if ($comment != null) {
                $param['MESSAGE'] = $comment;
            } else {
                $param['MESSAGE'] = "";
            }
            
            $param['INSERT_DATE'] = date("Y-m-d H:i:s");
            $param['LAST_DATE'] = date("Y-m-d H:i:s");
        } else {
            // その他の場合
            $param['ACT_ID'] = Timeline::ACT_ID_FILE_TIMELINE;
            $param['USR_ID'] = $_user['User']['USR_ID'];
            $param['VAL_ID'] = $_fleid;
            
            if ($comment != null) {
                $param['MESSAGE'] = $comment;
            } else {
                $param['MESSAGE'] = "";
            }
            
            $param['INSERT_DATE'] = date("Y-m-d H:i:s");
            $param['LAST_DATE'] = date("Y-m-d H:i:s");
        }
        
        // 保存
        if ($this->save($param)) {
            $param['TML_ID'] = $this->getInsertID();
            
            if ($_mclass == 'Group') {
                // 管理者モデルの読み込み
                App::import('Model', 'Notice');
                $notice = new Notice();
            }
            
            return $param['TML_ID'];
        }
    }

    /**
     * タイムライン削除
     *
     * @param unknown $_tmlid
     *            (タイムラインのID)
     * @param unknown $_user
     *            (削除したユーザ)
     * @return unknown string
     * @access public
     * @author 作成者
     */
    public function Delete_Timeline($_tmlid, $_user)
    {
        App::import('Model', 'Notice');
        $notice = new Notice();
        
        App::import('Model', 'Watch');
        $watch = new Watch();
        
        App::import('Model', 'Read');
        $read = new Read();
        
        App::import('Model', 'Group');
        $group = new Group();
        
        App::import('Model', 'Storage');
        $storage = new Storage();
        
        // タイムラインデータの取得
        $result = $this->find('first', array(
            'fields' => 'Timeline.USR_ID, Timeline.TML_ID, Timeline.ACT_ID, Timeline.VAL_ID, Group.USR_ID, Storage.GRP_ID',
            'conditions' => array(
                'Timeline.TML_ID' => $_tmlid
            )
        ));
        
        // 削除したいデータの整理
        $conditions = array(
            'or' => array(
                '1' => array(
                    'Timeline.TML_ID' => $_tmlid
                ),
                '2' => array(
                    'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT,
                    'Timeline.VAL_ID' => $_tmlid
                )
            )
        );
        $gp = $group->find('first', array(
            'fields' => 'Group.USR_ID',
            'conditions' => array(
                'Group.GRP_ID' => $result['Storage']['GRP_ID']
            )
        ));
        
        if ($result['Timeline']['ACT_ID'] != Timeline::ACT_ID_COMMENT) {
            // 削除できるユーザかどうかの確認
            if ($result['Timeline']['USR_ID'] == $_user['User']['USR_ID'] || $_user['User']['AUTHORITY'] == User::AUTHORITY_TRUE || $result['Group']['USR_ID'] == $_user['User']['USR_ID'] || $gp['Group']['USR_ID'] == $_user['User']['USR_ID']) {
                $res = $this->find('all', array(
                    'fields' => array(
                        'Timeline.TML_ID'
                    ),
                    'conditions' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT,
                        'Timeline.VAL_ID' => $_tmlid
                    )
                ));
                if ($result['Timeline']['ACT_ID'] == Timeline::ACT_ID_FILE_TIMELINE || $result['Timeline']['ACT_ID'] == Timeline::ACT_ID_FILE_GROUP) {
                    $this->Storage->Delete_File($result['Timeline']['VAL_ID'], $_user);
                }
                
                // 削除
                if ($this->deleteAll($conditions, false, false)) {
                    
                    $n = array(
                        'Notice.TML_ID' => $_tmlid
                    );
                    $notice->create();
                    $notice->deleteAll($n, false, false);
                    
                    $w = array(
                        'Watch.TML_ID' => $_tmlid
                    );
                    $watch->create();
                    $watch->deleteAll($w, false, false);
                    
                    $r = array(
                        'Read.TML_ID' => $_tmlid
                    );
                    $read->create();
                    $read->deleteAll($r, false, false);
                    
                    foreach ($res as $k => $v) {
                        $read->create();
                        $read->deleteAll(array(
                            'Read.TML_ID' => $v['Timeline']['TML_ID']
                        ));
                    }
                    
                    // 成功
                    return $_tmlid;
                } else {
                    // 失敗
                    return 'false';
                }
            } else {
                // 権限がない
                return 'not';
            }
        } else {
            $result2 = $this->find('first', array(
                'fields' => 'Timeline.USR_ID, Timeline.TML_ID, Group.USR_ID, Storage.GRP_ID',
                'conditions' => array(
                    'Timeline.TML_ID' => $result['Timeline']['VAL_ID']
                )
            ));
            
            $gp = $group->find('first', array(
                'fields' => 'Group.USR_ID',
                'conditions' => array(
                    'Group.GRP_ID' => $result2['Storage']['GRP_ID']
                )
            ));
            
            // 削除できるユーザかどうかの確認
            if ($result2['Timeline']['USR_ID'] == $_user['User']['USR_ID'] || $result['Timeline']['USR_ID'] == $_user['User']['USR_ID'] || $_user['User']['AUTHORITY'] == User::AUTHORITY_TRUE || $result2['Group']['USR_ID'] == $_user['User']['USR_ID'] || $gp['Group']['USR_ID'] == $_user['User']['USR_ID']) {
                // 削除
                $params = array();
                $params['TML_ID'] = $result['Timeline']['TML_ID'];
                $params['DEL_FLG'] = 1;
                
                if ($this->save($params)) {
                    // 成功
                    // タイムラインからコメントデータの取得
                    $cmt_cnt = $this->find('count', array(
                        "fields" => 'Timeline.USR_ID',
                        "conditions" => array(
                            'Timeline.DEL_FLG' => 0,
                            'Timeline.ACT_ID' => 2,
                            'Timeline.VAL_ID' => $result['Timeline']['VAL_ID'],
                            'Timeline.USR_ID' => $result['Timeline']['USR_ID']
                        ),
                        "group" => "Timeline.USR_ID"
                    ));
                    if ($cmt_cnt == 0) {
                        $n = array(
                            'Notice.P_USR_ID' => $result['Timeline']['USR_ID'],
                            'Notice.TML_ID' => $result['Timeline']['VAL_ID']
                        );
                        
                        $notice->create();
                        $notice->deleteAll($n, false, false);
                    }
                    return $_tmlid;
                } else {
                    // 失敗
                    return 'false';
                }
            } else {
                // 権限がない
                return 'not';
            }
        }
    }

    /**
     * 全て物理削除
     *
     * @param unknown $userid            
     * @param string $com            
     * @param string $com_del            
     * @return void
     * @access public
     * @author 作成者
     */
    public function Delete_All($userid, $com = true, $com_del = true)
    {
        if ($com_del == true) {
            $result = $this->find('all', array(
                'conditions' => array(
                    'Timeline.USR_ID' => $userid
                )
            ));
        } else {
            $result = $this->find('all', array(
                'conditions' => array(
                    'Timeline.USR_ID' => $userid,
                    'NOT' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT
                    )
                )
            ));
        }
        
        $params = array();
        
        App::import('Model', 'Notice');
        $notice = new Notice();
        
        App::import('Model', 'Watch');
        $watch = new Watch();
        
        App::import('Model', 'Read');
        $read = new Read();
        
        foreach ($result as $key => $val) {
            if ($val['Timeline']['ACT_ID'] != Timeline::ACT_ID_COMMENT) {
                $res = $this->find('all', array(
                    'conditions' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT,
                        'Timeline.VAL_ID' => $val['Timeline']['TML_ID']
                    )
                ));
                // 削除したいデータの整理
                $conditions = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.TML_ID' => $val['Timeline']['TML_ID']
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT,
                            'Timeline.VAL_ID' => $val['Timeline']['TML_ID']
                        )
                    )
                );
                
                $this->create();
                $this->deleteAll($conditions, false, false);
                
                $n = array(
                    'Notice.TML_ID' => $val['Timeline']['TML_ID']
                );
                
                $notice->create();
                $notice->deleteAll($n, false, false);
                
                $w = array(
                    'Watch.TML_ID' => $val['Timeline']['TML_ID']
                );
                $watch->create();
                $watch->deleteAll($w, false, false);
                
                $r = array(
                    'Read.TML_ID' => $val['Timeline']['TML_ID']
                );
                $read->create();
                $read->deleteAll($r, false, false);
                
                foreach ($res as $k => $v) {
                    $read->create();
                    $read->deleteAll(array(
                        'Read.TML_ID' => $v['Timeline']['TML_ID']
                    ));
                }
            } else {
                
                if ($com) {
                    $this->create();
                    $this->delete($val['Timeline']['TML_ID']);
                } else {
                    $this->create();
                    
                    $params['TML_ID'] = $val['Timeline']['TML_ID'];
                    $params['DEL_FLG'] = 2;
                    
                    $this->save($params);
                }
            }
        }
    }

    /**
     * 投稿のグループを取得する
     *
     * 返り値：投稿の属しているグループのID。グループ投稿で無い場合はnullが返される。
     *
     * @param unknown_type $tmlid            
     * @return 投稿の属しているグループのID
     * @access public
     * @author 作成者
     */
    public function getBelongGroup($tmlid)
    {
        $post = $this->read(array(
            'ACT_ID',
            'VAL_ID'
        ), $tmlid);
        
        if (empty($post)) {
            return null;
        }
        
        if ($post["Timeline"]["ACT_ID"] == Timeline::ACT_ID_FILE_GROUP || $post["Timeline"]["ACT_ID"] == Timeline::ACT_ID_GROUP) {
            return $post["Timeline"]["VAL_ID"];
        } else {
            return null;
        }
    }
}
