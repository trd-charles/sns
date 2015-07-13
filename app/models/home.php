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
 * ホーム用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Home extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Home';

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
     * レコードのソート順の初期設定
     *
     * @var array
     * @access public
     */
    public $order = array(
        'Home.INSERT_DATE DESC'
    );

    /**
     * タイムラインの取得
     *
     * 条件１
     * Timeline.VAL_ID => 自分かフォローしている人のグループ
     * Timeline.ACT_ID => 1
     *
     * 条件２
     * Timeline.ACT_ID => 3
     * Timeline.VAL_ID => 自分の所属しているグループ
     *
     * 条件３
     * Timeline.ACT_ID => 4
     * Timeline.Storage.GRP_ID => 自分かフォローしている人のグループ
     * Timeline.Storage.PUBLIC => 1
     *
     * 条件４
     * Timeline.ACT_ID => 5
     * Timeline.Storage.GRP_ID => 自分かフォローしている人のグループ
     * Timeline.Storage.PUBLIC => 1
     *
     * @param unknown $_user            
     * @param string $_grpid            
     * @param string $while            
     * @param string $_tmlid            
     * @param string $_grpflag            
     * @return array ($conditions)
     * @access public
     * @author 作成者
     */
    public function Get_Timeline($_user, $_grpid = null, $while = null, $_tmlid = null, $_grpflag = null)
    {
        // フレンドモデルの読み込み
        App::import('Model', 'Friend');
        $friend = new Friend();
        
        // ユーザモデルの読み込み
        App::import('Model', 'User');
        $user_m = new User();
        
        // フォローしている人を全て取得し、リスト化
        $friend_list = $friend->Get_Friend($_user['User']['USR_ID']);
        $uid_or = array();
        
        foreach ($friend_list as $key => $val) {
            $tmp_ug = $user_m->find('first', array(
                'fields' => array(
                    'GRP_ID'
                ),
                'conditions' => array(
                    'USR_ID' => $val['Friend']['F_USR_ID']
                )
            ));
            $uid_or[$key] = $tmp_ug['User']['GRP_ID'];
        }
        
        // 自分のIDも付け加える
        $uid_or = array_merge($uid_or, (array) $_user['User']['GRP_ID']);
        
        // 参加情報モデルの読み込み
        App::import('Model', 'Join');
        $join = new Join();
        
        // 参加しているグループの取得とリスト化
        $jg = $join->Join_Group($_user['User']['USR_ID']);
        $gid_or = array();
        
        foreach ($jg as $key => $val) {
            $gid_or[$key] = $val['Join']['GRP_ID'];
        }
        
        $tm_cond = array();
        if ($_tmlid != null) {
            $tm_cond = array(
                'Timeline.TML_ID' => $_tmlid
            );
        }
        
        // 実際の構文作成
        if ($while == 1) {
            // 発言のみ取得
            // タイムライン(フォローのみ)の場合は、グループ以外を取得する
            if ($_grpflag == 1) {
                $result = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.VAL_ID' => $uid_or,
                            $tm_cond
                        )
                    )
                );
            } elseif ($_grpflag == 2) {
                // タイムライン(自分のみ)の場合は、自分の発言またはコメントした発言を取得する
                $result = array(
                    'or' => array(
                        '4' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.USR_ID' => $_user['User']['USR_ID']
                        ),
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.USR_ID' => $_user['User']['USR_ID']
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.TML_ID' => $_tmlid,
                            $tm_cond
                        ),
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.TML_ID' => $_tmlid,
                            $tm_cond
                        )
                    )
                );
            } elseif ($_grpflag == 3) {
                // タイムライン(グループのみ)の場合は、グループのみを取得する
                $result = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.VAL_ID' => $gid_or,
                            $tm_cond
                        )
                    )
                );
            } else {
                $result = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            $tm_cond
                        )
                    )
                );
            }
        } elseif ($while == 2) {
            // ファイルアップロードのみ取得
            // タイムライン(フォローのみ)の場合は、グループ以外を取得する
            if ($_grpflag == 1) {
                $result = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.GRP_ID' => $uid_or,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            } elseif ($_grpflag == 2) {
                // タイムライン(自分のみ)の場合は、自分の発言またはコメントした発言を取得する
                $result = array(
                    'or' => array(
                        '4' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Timeline.USR_ID' => $_user['User']['USR_ID'],
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        ),
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Timeline.USR_ID' => $_user['User']['USR_ID'],
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Timeline.TML_ID' => $_tmlid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        ),
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Timeline.TML_ID' => $_tmlid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            } elseif ($_grpflag == 3) {
                // タイムライン(グループのみ)の場合は、グループのみ取得する
                $result = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Storage.GRP_ID' => $gid_or,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            } else {
                $result = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            }
        } else {
            // すべてを取得
            // タイムライン(フォローのみ)の場合は、グループ以外を取得する
            if ($_grpflag == 1) {
                $result = array(
                    'or' => array(
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.VAL_ID' => $uid_or,
                            $tm_cond
                        ),
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.GRP_ID' => $uid_or,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            } elseif ($_grpflag == 2) {
                // タイムライン(自分のみ)の場合は、自分の発言またはコメントした発言を取得する
                $result = array(
                    'or' => array(
                        '8' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.USR_ID' => $_user['User']['USR_ID']
                        ),
                        '7' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.USR_ID' => $_user['User']['USR_ID']
                        ),
                        '6' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Timeline.USR_ID' => $_user['User']['USR_ID'],
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        ),
                        '5' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Timeline.USR_ID' => $_user['User']['USR_ID'],
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        ),
                        '4' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.TML_ID' => $_tmlid,
                            $tm_cond
                        ),
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.TML_ID' => $_tmlid,
                            $tm_cond
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Timeline.TML_ID' => $_tmlid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        ),
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Timeline.TML_ID' => $_tmlid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            } elseif ($_grpflag == 3) {
                // タイムライン(グループのみ)の場合は、グループのみ取得する
                $result = array(
                    'or' => array(
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.VAL_ID' => $gid_or,
                            $tm_cond
                        ),
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Storage.GRP_ID' => $gid_or,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            } elseif ($_grpflag == 4) {
                // タイムライン(１件のみ)の場合は、タイムラインIDに紐付くデータを取得する
                App::import('Model', 'Timeline');
                App::import('Model', 'Group');
                
                $timelineModel = new Timeline();
                $group_id = $timelineModel->getBelongGroup($_tmlid);
                
                $gid_or[] = $group_id;
                $result = array(
                    'or' => array(
                        '4' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            $tm_cond
                        ),
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.VAL_ID' => $gid_or,
                            $tm_cond
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.GRP_ID' => $uid_or,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        ),
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Storage.GRP_ID' => $gid_or,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            } else {
                $result = array(
                    'or' => array(
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            $tm_cond
                        ),
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                            $tm_cond
                        )
                    )
                );
            }
        }
        return $result;
    }

    /**
     * タイムラインを検索
     *
     * @param unknown $_keyword            
     * @param unknown $_user            
     * @param string $_grpid            
     * @param string $while            
     * @param string $_tmlid            
     * @param number $_page            
     * @param string $cond_com            
     * @param string $cond_pos            
     * @return array ($conditions)
     * @access public
     * @author 作成者
     */
    public function Search_Timeline($_keyword, $_user, $_grpid = null, $while = null, $_tmlid = null, $_page = 1, &$cond_com = null, &$cond_pos = null)
    {
        // prefixの取得
        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        $prefix = $db->config['prefix'];
        
        // 検索
        $str = $_keyword;
        
        // 参加情報モデルの読み込み
        App::import('Model', 'Join');
        $join = new Join();
        
        // 参加しているグループの取得とリスト化
        $jg = $join->Join_Group($_user['User']['USR_ID']);
        $gid_or = array();
        
        foreach ($jg as $key => $val) {
            $gid_or[$key] = $val['Join']['GRP_ID'];
        }
        
        // 検索
        $conditions = array();
        $joins = array(
            0 => array(
                'type' => 'LEFT',
                'table' => 'T_TIME_LINE',
                'alias' => 'Child',
                'conditions' => array(
                    'Timeline.TML_ID = Child.VAL_ID',
                    'Timeline.ACT_ID' => 2,
                    'Timeline.MESSAGE LIKE ?' => array(
                        "%$str%"
                    ),
                    'Timeline.DEL_FLG' => 0
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
        
        $cond_com = array(
            'joins' => $joins,
            'conditions' => array(
                'or' => array(
                    '1' => array(
                        'Child.ACT_ID' => 2,
                        'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                        ' Child.MESSAGE LIKE' => '%' . $str . '%'
                    ),
                    '2' => array(
                        'Child.ACT_ID' => 2,
                        'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                        ' Child.MESSAGE LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PUBLIC
                    ),
                    '3' => array(
                        'Child.ACT_ID' => 2,
                        'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                        ' Child.MESSAGE LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PRIVATE,
                        'Group.GRP_ID' => $gid_or
                    ),
                    '4' => array(
                        'Child.ACT_ID' => 2,
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                        ' Child.MESSAGE LIKE' => '%' . $str . '%',
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    ),
                    '5' => array(
                        'Child.ACT_ID' => 2,
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                        ' Child.MESSAGE LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PUBLIC,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    ),
                    '6' => array(
                        'Child.ACT_ID' => 2,
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                        ' Child.MESSAGE LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PRIVATE,
                        'Group.GRP_ID' => $gid_or,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    )
                )
            )
        );
        
        $cond_pos = array(
            'joins' => $joins,
            'conditions' => array(
                'or' => array(
                    '7' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                        'Timeline.Message LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PUBLIC
                    ),
                    '8' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                        'Timeline.Message LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PRIVATE,
                        'Group.GRP_ID' => $gid_or
                    ),
                    '9' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                        'Timeline.Message LIKE' => '%' . $str . '%',
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    ),
                    '10' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                        'Timeline.Message LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PUBLIC,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    ),
                    '11' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                        'Timeline.Message LIKE' => '%' . $str . '%',
                        'Group.TYPE' => Group::TYPE_PRIVATE,
                        'Group.GRP_ID' => $gid_or,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    ),
                    '12' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                        'Timeline.Message LIKE' => '%' . $str . '%'
                    )
                )
            )
        );
        
        $condition = array(
            'Timeline' => array(
                'fields' => array(
                    'DISTINCT Timeline.TML_ID',
                    'Timeline.*',
                    'User.USR_ID',
                    'User.GRP_ID',
                    'User.NAME',
                    'Storage.FLE_ID',
                    'Storage.RAND_NAME',
                    'Storage.GRP_ID',
                    'Storage.F_TYPE',
                    'Storage.ORIGINAL_NAME',
                    'Storage.EXTENSION',
                    'Group.GRP_ID',
                    'Group.NAME',
                    'Group.USR_ID',
                    'Storageg.GRP_ID',
                    'Storageg.FLE_ID',
                    'GroupType.TYPE',
                    'Child.ACT_ID'
                ),
                'conditions' => array(
                    'or' => array(
                        '1' => array(
                            'Child.ACT_ID' => 2,
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            ' Child.MESSAGE LIKE' => '%' . $str . '%'
                        ),
                        '2' => array(
                            'Child.ACT_ID' => 2,
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            ' Child.MESSAGE LIKE' => '%' . $str . '%',
                            'Group.TYPE' => Group::TYPE_PUBLIC
                        ),
                        '3' => array(
                            'Child.ACT_ID' => 2,
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            ' Child.MESSAGE LIKE' => '%' . $str . '%',
                            'Group.TYPE' => Group::TYPE_PRIVATE,
                            'Group.GRP_ID' => $gid_or
                        ),
                        '4' => array(
                            'Child.ACT_ID' => 2,
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            ' Child.MESSAGE LIKE' => '%' . $str . '%',
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        ),
                        '5' => array(
                            'Child.ACT_ID' => 2,
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            ' Child.MESSAGE LIKE' => '%' . $str . '%',
                            'GroupType.TYPE' => 0
                        ),
                        '6' => array(
                            'Child.ACT_ID' => 2,
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            ' Child.MESSAGE LIKE' => '%' . $str . '%',
                            'GroupType.TYPE' => 1,
                            'Storageg.GRP_ID' => $gid_or
                        ),
                        '7' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.Message LIKE' => '%' . $str . '%',
                            'Group.TYPE' => Group::TYPE_PUBLIC
                        ),
                        '8' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                            'Timeline.Message LIKE' => '%' . $str . '%',
                            'Group.TYPE' => Group::TYPE_PRIVATE,
                            'Group.GRP_ID' => $gid_or
                        ),
                        '9' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Timeline.Message LIKE' => '%' . $str . '%',
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        ),
                        '10' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Timeline.Message LIKE' => '%' . $str . '%',
                            'GroupType.TYPE' => 0
                        ),
                        '11' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                            'Timeline.Message LIKE' => '%' . $str . '%',
                            'GroupType.TYPE' => 1,
                            'Storageg.GRP_ID' => $gid_or
                        ),
                        '12' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.Message LIKE' => '%' . $str . '%'
                        )
                    )
                ),
                'limit' => 20,
                'joins' => $joins,
                'order' => 'Timeline.TML_ID DESC',
                'page' => $_page
            )
        );
        
        return $condition;
    }
}