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
 * グループ用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Group extends AppModel
{

    /**
     * グループタイプに関する定数
     *
     * @var number
     * @author 作成者
     */
    const TYPE_PUBLIC = 0; // 公開
    const TYPE_PRIVATE = 1; // 非公開
    const TYPE_PERSONAL = 2; // 個人用グループ
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Group';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'M_GROUP';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'GRP_ID';

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
     * バリデーションの設定
     *
     * @var array
     * @access public
     */
    public $validate = array(
        'NAME' => array(
            'rule0' => array(
                'rule' => array(
                    'maxLengthJP',
                    40
                ),
                'message' => 'グループ名は40文字までです'
            ),
            "rule1" => array(
                'rule' => 'notEmpty',
                'message' => 'グループ名は必須項目です'
            )
        ),
        'TYPE' => array(
            'rule0' => array(
                'rule' => array(
                    'radio_empty'
                ),
                'message' => '公開・非公開を設定してください'
            )
        ),
        'DESCRIPTION' => array(
            'rule0' => array(
                'rule' => array(
                    'maxLengthJP',
                    150
                ),
                'message' => 'グループ概要は150文字までです'
            )
        )
    );

    /**
     * 個人用のグループの作成
     *
     * @param unknown $_data
     *            (作成したいユーザの情報)
     * @param unknown $_userid
     *            (作成したいユーザのID
     * @return number (作成したグループのID)
     * @access public
     * @author 作成者
     */
    public function Create_My_Group($_data, $_userid)
    {
        
        // 保存する情報の整理
        $_params = array();
        $_params['NAME'] = $_data['Administrator']['NAME'];
        $_params['USR_ID'] = $_userid;
        $_params['TYPE'] = 2;
        $_params['INSERT_DATE'] = date("Y-m-d H:i:s");
        $_params['LAST_UPDATE'] = date("Y-m-d H:i:s");
        
        // 保存
        if ($this->save($_params)) {
            return $this->getInsertID();
        }
    }

    /**
     * グループ作成
     *
     * @param unknown $_data            
     * @param unknown $_userid            
     * @param string $error            
     * @return Ambigous <NULL, unknown>|boolean
     * @access public
     * @author 作成者
     */
    public function Create_Group($_data, $_userid, $error = null)
    {
        // 情報の整理
        $_params = array();
        $_params['NAME'] = $_data['Group']['NAME'];
        $_params['USR_ID'] = $_userid;
        $_params['TYPE'] = $_data['Group']['TYPE'];
        $_params['DESCRIPTION'] = $_data['Group']['DESCRIPTION'];
        
        if (isset($_data['Group']['GRP_ID'])) {
            // 編集の場合の処理
            $_params['GRP_ID'] = $_data['Group']['GRP_ID'];
            $_params['USR_ID'] = $_data['Group']['USR_ID'];
        } else {
            // 作成の場合の処理
            $_params['INSERT_DATE'] = date("Y-m-d H:i:s");
        }
        
        $_params['LAST_UPDATE'] = date("Y-m-d H:i:s");
        
        // 管理者モデルの読み込み
        App::import('Model', 'Administrator');
        $administrator = new Administrator();
        
        // 参加情報モデルの読み込み
        App::import('Model', 'Join');
        $join = new Join();
        
        $r_dir = 0;
        $c_dir = 0;
        
        // 保存
        if ($this->save($_params)) {
            // 保存したIDの取得
            $id = $this->getInsertID();
            
            if ($id == null) {
                // IDがなかった場合（編集の場合）保存したデータから取得
                $id = $_params['GRP_ID'];
            }
            // 保存したデータの取得
            $result = $this->find('first', array(
                'conditions' => array(
                    'Group.GRP_ID' => $id
                )
            ));
            
            if ($result["Group"]['DIRECTORY1'] == null && $result["Group"]['DIRECTORY2'] == null) {
                // 新規作成時、フォルダーを作成する処理
                $administrator->Create_Directory($id, $r_dir, $c_dir, 'group');
                
                $result['Group']['DIRECTORY1'] = $r_dir;
                $result['Group']['DIRECTORY2'] = $c_dir;
            }
            
            // サムネイルがなかった場合
            if ($result['Group']['THUMBNAIL'] == null) {
                // サムネイルを作成する
                $this->Save_Image(null, $result["Group"]);
            }
            
            // 新規の場合
            if (! isset($_params['GRP_ID'])) {
                // 管理者としてグループに参加
                $tmp_res = $join->Join_User($id, $_userid);
                if ($tmp_res != false || $tmp_res == '0') {
                    // 成功
                    return $id;
                }
            } else {
                // 編集の場合
                // 現在のグループ管理者IDが、変更後と異なる場合
                $group_admin_user_id = $join->field('Join.USR_ID', array(
                    'Join.GRP_ID' => $_params['GRP_ID'],
                    'Join.STATUS' => Join::STATUS_ADMINISTRATOR
                ));
                
                // 管理者変更を伴う場合
                if ($group_admin_user_id != $_params['USR_ID']) {
                    
                    // 現管理者のステータスを変更
                    $join->updateAll(array(
                        'Join.STATUS' => Join::STATUS_JOINED
                    ), array(
                        'Join.GRP_ID' => $_params['GRP_ID'],
                        'Join.USR_ID' => $group_admin_user_id
                    ));
                    
                    // 新管理者のステータスを変更
                    $join->updateAll(array(
                        'Join.STATUS' => Join::STATUS_ADMINISTRATOR
                    ), array(
                        'Join.GRP_ID' => $_params['GRP_ID'],
                        'Join.USR_ID' => $_params['USR_ID']
                    ));
                }
                // 成功
                return $_params['GRP_ID'];
            }
        } else {
            // 失敗
            $error = $this->invalidFields();
            return false;
        }
    }

    /**
     * サムネイルの保存をする関数
     *
     * @param unknown $_params
     *            (保存したい情報)
     * @param unknown $_group
     *            (保存したいグループの情報)
     * @param string $_status
     *            (新規か、編集かのステータス)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Save_Image($_params, $_group, $_status = null)
    {
        // ストレージモデルの読み込み
        App::import('Model', 'Storage');
        $storage = new Storage();
        
        if ($_status == 'edit') {
            // 編集の場合
            // パスの作成
            $root = $_group['Group']['DIRECTORY1'] . "/" . $_group['Group']['DIRECTORY2'] . "/thumbnail/";
            
            // ファイルがアップロードされたかどうかの確認
            if ($_params['Group']['FILE']['size'] > 0) {
                if (is_uploaded_file($_params['Group']['FILE']['tmp_name'])) {
                    
                    // IE用拡張子判断
                    $extension = strtolower(pathinfo($_params['Group']['FILE']['name'], PATHINFO_EXTENSION));
                    if ($extension === 'png' || $extension === 'jpeg' || $extension === 'jpg' || $extension === 'gif') {
                        
                        // 画像typeであるか確認
                        if ($_params['Group']['FILE']['type'] === 'image/gif' || $_params['Group']['FILE']['type'] === 'image/jpeg' || $_params['Group']['FILE']['type'] === 'image/pjpeg' || $_params['Group']['FILE']['type'] === 'image/png' || $_params['Group']['FILE']['type'] === 'image/x-png') {
                            if ($_params['Group']['FILE']['type'] === 'image/pjpeg') {
                                $_params['Group']['FILE']['type'] = 'image/jpeg';
                            }
                            if ($_params['Group']['FILE']['type'] === 'image/x-png') {
                                $_params['Group']['FILE']['type'] = 'image/png';
                            }
                            $info = getimagesize($_params['Group']['FILE']['tmp_name']);
                            
                            // 正しい画像ファイルであるかを確認
                            if ($info['mime'] == $_params['Group']['FILE']['type']) {
                                $storage->Image_Resize($_params['Group']['FILE'], APP . "/files/group/" . $root . "thumbnail", 'thumbnail');
                                $storage->Image_Resize($_params['Group']['FILE'], APP . "/files/group/" . $root . "preview", 'preview');
                                
                                if (move_uploaded_file($_params['Group']['FILE']['tmp_name'], APP . "/files/group/" . $root . "original")) {
                                    $result = array();
                                    $result['GRP_ID'] = $_group['Group']['GRP_ID'];
                                    $result['THUMBNAIL'] = $_params['Group']['FILE']['name'];
                                    
                                    if ($this->save($result)) {
                                        return true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            
            // 新規グループの場合
            $root = $_group['DIRECTORY1'] . "/" . $_group['DIRECTORY2'] . "/thumbnail/";
            
            // ファイルの移動
            if (copy("img/common/g_original", APP . "/files/group/" . $root . "original") && copy("img/common/g_preview", APP . "/files/group/" . $root . "preview") && copy("img/common/g_thumbnail", APP . "/files/group/" . $root . "thumbnail")) {
                
                // 情報の整理
                $result = array();
                $result['GRP_ID'] = $_group['GRP_ID'];
                $result['DIRECTORY1'] = $_group['DIRECTORY1'];
                $result['DIRECTORY2'] = $_group['DIRECTORY2'];
                $result['THUMBNAIL'] = "default.jpg";
                
                // 保存
                if ($this->save($result)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 参加しているグループの検索、条件を取得するための関数
     *
     * @param unknown $_userid
     *            (ユーザのID)
     * @param string $_name
     *            (グループの名前)
     * @param string $_status
     *            (参加条件)
     * @param number $limit
     *            (取得したい件数（デフォルトは10))
     * @return multitype:multitype:string multitype:string unknown number
     *         multitype:multitype:string multitype:string unknown
     * @access public
     * @author 作成者
     */
    public function Search_Group($_userid, $_name = null, $_status = null, $limit = 10)
    {
        
        // ログインユーザの取得
        App::import('Component', 'Session');
        $Session = new SessionComponent();
        $login_user = $Session->read('Auth.User');
        
        // prefixの取得
        $db = & ConnectionManager::getDataSource($this->useDbConfig);
        $prefix = $db->config['prefix'];
        
        // ストレージモデルの読み込み
        App::import('Model', 'User');
        $user = new User();
        $_user = $user->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $_userid
            )
        ));
        
        $group = array();
        
        // 個人用グループを抜かす
        $conditions = array(
            'not' => array(
                'TYPE' => Group::TYPE_PERSONAL
            )
        );
        
        if ($_name != null) {
            
            // 名前条件があった場合その条件を指定
            $conditions = array_merge($conditions, array(
                'Group.NAME LIKE' => "%$_name%"
            ));
        }
        
        $conds = array();
        
        if (isset($_status[0])) {
            
            // 参加条件があった場合、その条件の構文を作成
            foreach ($_status as $key => $val) {
                if ($val == 3) {
                    $conds = array(
                        '3' => array(
                            'Join.STATUS' => NULL
                        )
                    );
                }
            }
        }
        
        App::import('Model', 'Join');
        $join = new Join();
        $join_group_id = $join->find('all', array(
            'fields' => 'Join.GRP_ID',
            'conditions' => array(
                'Join.USR_ID' => $login_user['USR_ID'],
                'Group.TYPE' => Group::TYPE_PRIVATE
            )
        ));
        
        for ($i = 0; $i < count($join_group_id); $i ++) {
            $join_group_id[$i] = $join_group_id[$i]['Join']['GRP_ID'];
        }
        
        if ($login_user['AUTHORITY'] == User::AUTHORITY_TRUE) {
            $cond_or = array(
                'OR' => array(
                    '1' => array(
                        'Join.STATUS' => $_status
                    ),
                    $conds
                )
            );
        } else {
            $cond_or = array(
                'OR' => array(
                    '1' => array(
                        'Join.STATUS' => $_status,
                        'Group.TYPE' => Group::TYPE_PUBLIC
                    ),
                    '2' => array(
                        'Join.STATUS' => $_status,
                        'Group.TYPE' => Group::TYPE_PRIVATE,
                        'Join.GRP_ID' => $join_group_id
                    ),
                    $conds
                )
            );
        }
        
        // 条件の作成
        $conditions = array_merge($conditions, $cond_or);
        $joins = array(
            array(
                'type' => 'LEFT',
                'table' => 'T_JOIN',
                'alias' => 'Join',
                'conditions' => array(
                    'Group.GRP_ID = Join.GRP_ID',
                    'Join.USR_ID' => $_userid
                ),
                'fields' => ''
            )
        );
        
        $conditions = array(
            'Group' => array(
                "fields" => array(
                    'Group.*',
                    'Join.STATUS',
                    'User.*'
                ),
                'conditions' => $conditions,
                'limit' => $limit,
                'joins' => $joins,
                'order' => 'Join.JIN_ID Desc'
            )
        );
        return $conditions;
    }

    /**
     * グループへの招待
     *
     * @param unknown $_grpid
     *            (誘いたいグループ)
     * @param unknown $_data
     *            (誘いたい人のデータ)
     * @return number (1:招待成功 メール成功,2:招待成功 メール失敗,0:招待失敗 メール送信なし)
     * @access public
     * @author 作成者
     */
    public function invite($_grpid, $_data)
    {
        $params = array();
        $g_params = array();
        
        $i = 1;
        
        // 管理者モデルの読み込み
        App::import('Model', 'Administrator');
        $administrator = new Administrator();
        
        // 申請情報モデルの読み込み
        App::import('Model', 'Request');
        $request = new Request();
        
        // 参加情報モデルの読み込み
        App::import('Model', 'Join');
        $join = new Join();
        
        $grpname = $this->find('first', array(
            'fields' => array(
                'Group.NAME',
                'Group.USR_ID'
            ),
            'conditions' => array(
                'Group.GRP_ID' => $_grpid
            )
        ));
        
        $grpadmin = $administrator->find('first', array(
            'fields' => array(
                'Administrator.NAME'
            ),
            'conditions' => array(
                'Administrator.USR_ID' => $grpname['Group']['USR_ID']
            )
        ));
        
        $tes = 1;
        $mail = array();
        
        // 申請情報の整理
        $overlap = false;
        
        // グループ参加者を取得
        $g_member = $join->find("list", array(
            "conditions" => array(
                "GRP_ID" => $_grpid,
                "STATUS" => array(
                    Join::STATUS_JOINED,
                    Join::STATUS_ADMINISTRATOR
                )
            ),
            "fields" => array(
                "USR_ID"
            )
        ));
        while ($tes < 6) {
            $overlap = false;
            
            // 既に参加しているユーザかチェック
            if (! in_array($_data['ID_' . $tes], $g_member)) {
                $tmp = $administrator->find('first', array(
                    'fields' => array(
                        'Administrator.USR_ID',
                        'Administrator.GRP_ID',
                        'Administrator.NAME',
                        'Administrator.MAIL'
                    ),
                    'conditions' => array(
                        'Administrator.USR_ID' => $_data['ID_' . $tes]
                    )
                ));
                
                if ($tmp['Administrator']['USR_ID'] != null) {
                    // 重複と、既にグループ未参加のユーザかをチェック
                    for ($j = 0; $j < $i - 1; $j ++) {
                        if ($tmp['Administrator']['USR_ID'] == $params[$j]['USR_ID']) {
                            $overlap = true;
                        }
                    }
                    
                    if (! $overlap) {
                        // IDが重複しなかった場合
                        $params[$i - 1]['USR_ID'] = $tmp['Administrator']['USR_ID'];
                        $params[$i - 1]['TYPE'] = Request::TYPE_INVITE_SELECT_GROUP;
                        $params[$i - 1]['GRP_ID'] = $_grpid;
                        $params[$i - 1]['MESSAGE'] = $grpadmin['Administrator']['NAME'] . "があなたを" . $grpname['Group']['NAME'] . "に招待しました。";
                        $params[$i - 1]['INSERT_DATE'] = date("Y-m-d H:i:s");
                        $g_params[$i - 1]['GRP_ID'] = $_grpid;
                        $g_params[$i - 1]['USR_ID'] = $tmp['Administrator']['USR_ID'];
                        $g_params[$i - 1]['STATUS'] = Join::STATUS_WAITING_JOIN;
                        $g_params[$i - 1]['INSERT_DATE'] = date("Y-m-d H:i:s");
                        $g_params[$i - 1]['LAST_UPDATE'] = date("Y-m-d H:i:s");
                        $mail[$i - 1]['MAIL'] = $tmp['Administrator']['MAIL'];
                        $mail[$i - 1]['NAME'] = $tmp['Administrator']['NAME'];
                        $i ++;
                    }
                }
            }
            
            $tes ++;
        }
        if ($request->saveAll($params)) {
            if ($join->saveAll($g_params)) {
                // コンポーネントの読み込み
                App::import('Component', 'Common');
                $common = new CommonComponent();
                // メールの用意
                $url = Router::url(array(
                    'controller' => 'users',
                    'action' => 'login'
                ), true);
                
                foreach ($mail as $key => $val) {
                    $to = $val['MAIL'];
                    $subject = "【抹茶SNS】" . $grpname['Group']['NAME'] . "グループ追加のお知らせ";
                    $content = array(
                        'GROUP_NAME' => $grpname['Group']['NAME'],
                        'GROUP_ADMIN_NAME' => $grpadmin['Administrator']['NAME']
                    );
                    // メールの送信
                    if (! $common->send_mail_beta($to, $subject, null, null, $content, 'group_add', null, 0, 1)) {
                        // メール送信失敗
                        return 2;
                    }
                    // メール送信成功
                    return 1;
                }
            }
        }
        // データベースへの保存失敗（招待失敗）
        return 0;
    }

    /**
     * グループの物理削除
     *
     * @param unknown $_id            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function delete_group($_id)
    {
        // 参加情報モデルの読み込み
        App::import('Model', 'Timeline');
        $timeline = new Timeline();
        $conditions = array(
            'Timeline.VAL_ID' => $_id,
            'Timeline.ACT_ID' => array(
                Timeline::ACT_ID_GROUP,
                Timeline::ACT_ID_FILE_GROUP
            )
        );
        
        if (! $timeline->deleteAll($conditions)) {
            return false;
        }
        
        // リクエストモデルの読み込み
        App::import('Model', 'Request');
        $request = new Request();
        $request->deleteAll(array(
            'Request.GRP_ID' => $_id
        ));
        
        // 参加情報モデルの読み込み
        App::import('Model', 'Join');
        $join = new Join();
        unset($conditions);
        $conditions = array(
            'Join.GRP_ID' => $_id
        );
        
        if (! $join->deleteAll($conditions)) {
            return false;
        }
        
        App::import('Model', 'Administrator');
        $admin = new Administrator();
        $admin->Delete_Directory($_id, "group");
        return $this->delete($_id);
    }

    /**
     * マイグループの物理削除
     *
     * @param unknown $_id            
     * @access public
     * @author 作成者
     */
    public function delete_my_group($_id)
    {
        App::import('Model', 'User');
        $user = new User();
        $_user = $user->find('first', array(
            'conditions' => array(
                'User.USR_ID' => $_id
            )
        ));
        
        $this->delete($_user['User']['GRP_ID']);
    }

    /**
     * タイムライン情報の取得
     *
     * グループへのコメント ACT_ID =>3, VAL_ID => groupid
     * グループの画像 ACT_ID =>5, Storage.GRP_ID => groupid,'Storage.PUBLIC' => 1
     *
     * @param unknown $_user
     *            (取得したいユーザの情報)
     * @param unknown $_groupid
     *            (取得したいグループのID)
     * @param string $while            
     * @return array ($conditions ページング条件)
     * @access public
     * @author 作成者
     */
    public function Get_Timeline($_user, $_groupid, $while = null)
    {
        // 実際の構文作成
        if ($while == 1) {
            // 発言のみ取得
            $conditions = array(
                'or' => array(
                    '1' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                        'Timeline.VAL_ID' => array(
                            $_groupid
                        )
                    )
                )
            );
        } elseif ($while == 2) {
            // ファイルアップロードのみ取得
            $conditions = array(
                'or' => array(
                    '1' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                        'Storage.GRP_ID' => $_groupid,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    )
                )
            );
        } else {
            $conditions = array(
                'or' => array(
                    '1' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_GROUP,
                        'Timeline.VAL_ID' => array(
                            $_groupid
                        )
                    ),
                    '2' => array(
                        'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_GROUP,
                        'Storage.GRP_ID' => $_groupid,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                    )
                )
            );
        }
        
        return $conditions;
    }

    /**
     * 公開グループかどうか
     *
     * @param $group_id グループID            
     * @return boolean (公開グループならtrue, それ以外ならfalse)
     * @access public
     * @author 作成者
     */
    public function isPublicGroup($group_id)
    {
        $group = $this->read('TYPE', $group_id);
        
        if ($group['Group']['TYPE'] == Group::TYPE_PUBLIC) {
            return true;
        } else {
            return false;
        }
    }
}