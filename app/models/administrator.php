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
 * 管理者用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Administrator extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Administrator';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_USER';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'USR_ID';

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
                    30
                ),
                'message' => '名前は30文字までです'
            ),
            "rule1" => array(
                'rule' => 'notEmpty',
                'message' => '名前は必須項目です'
            )
        ),
        'EDIT_PASSWORD' => array(
            'rule0' => array(
                'rule' => array(
                    'password_valid',
                    'EDIT_PASSWORD',
                    6,
                    20
                ),
                'message' => 'パスワードは6～20文字で入力してください。'
            ),
            "rule1" => array(
                'rule' => array(
                    'notEmpty'
                ),
                'message' => 'パスワードは必須項目です。'
            )
        ),
        'MAIL' => array(
            "rule0" => array(
                'rule' => array(
                    'maxLengthJP',
                    256
                ),
                'message' => 'メールアドレスは256文字以下です。'
            ),
            "rule1" => array(
                'rule' => array(
                    'email'
                ),
                'message' => '正しいメールアドレスを入力してください。'
            ),
            "rule2" => array(
                'rule' => array(
                    'notEmpty'
                ),
                'message' => 'メールアドレスは必須項目です。'
            )
        ),
        'DEL_SPAN' => array(
            "rule0" => array(
                'rule' => array(
                    'notEmpty'
                ),
                'message' => '削除期間を設定してください。'
            ),
            "rule1" => array(
                'rule' => array(
                    'numeric'
                ),
                'message' => '日数は数字で入力してください。'
            )
        )
    );

    /**
     * ユーザ一覧を取得するための関数
     *
     * @param string $_conditions            
     * @return array:
     * @access public
     * @author 作成者
     */
    public function User_Index($_conditions = false)
    {
        $result = array();
        
        // 条件が指定のチェック
        if ($_conditions == false) {
            
            // 条件が指定されていない場合
            $result = $this->find('all', array(
                'conditons' => array(
                    'T_USER.DEL_FLG' => '0'
                )
            ));
        } else {
            // 条件指定がある場合
            $result = $this->find('all', $_conditions);
        }
        
        return $result;
    }

    /**
     * ユーザやグループのフォルダを作る関数
     *
     * @param unknown $_id
     *            (作りたいユーザやグループのID)
     * @param unknown $r_dir
     *            (参照渡し フォルダの第一ルート)
     * @param unknown $c_dir
     *            (参照渡し フォルダの第二ルート)
     * @param string $_status
     *            (グループの場合の処理)
     * @return void
     * @access public
     * @author 作成者
     */
    public function Create_Directory($_id, &$r_dir, &$c_dir, $_status = null)
    {
        $r_dir = sprintf("%05d", (int) $_id / 100);
        $c_dir = Security::hash(uniqid() . mt_rand());
        $c_dir = substr($c_dir, 0, 10);
        
        if ($_status == null) {
            // ステータスに引数がない場合
            if (! is_dir(APP . "files/user")) {
                mkdir(APP . "files/user");
            }
            
            if (! is_dir(APP . "files/user/" . $r_dir)) {
                mkdir(APP . "files/user/" . $r_dir);
            }
            
            if (! is_dir(APP . "files/user/" . $r_dir . "/" . $c_dir)) {
                mkdir(APP . "files/user/" . $r_dir . "/" . $c_dir);
                mkdir(APP . "files/user/" . $r_dir . "/" . $c_dir . "/storage");
                mkdir(APP . "files/user/" . $r_dir . "/" . $c_dir . "/thumbnail");
            }
        } else {
            // グループの場合
            if (! is_dir(APP . "files/group")) {
                mkdir(APP . "files/group");
            }
            
            if (! is_dir(APP . "files/group/" . $r_dir)) {
                mkdir(APP . "files/group/" . $r_dir);
            }
            
            if (! is_dir(APP . "files/group/" . $r_dir . "/" . $c_dir)) {
                mkdir(APP . "files/group/" . $r_dir . "/" . $c_dir);
                mkdir(APP . "files/group/" . $r_dir . "/" . $c_dir . "/storage");
                mkdir(APP . "files/group/" . $r_dir . "/" . $c_dir . "/thumbnail");
            }
        }
    }

    /**
     * フォルダを削除する（本編では未使用）
     *
     * @param unknown $_id            
     * @param string $stat            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Delete_Directory($_id, $stat = "user")
    {
        if ($stat == 'user') {
            $_params = $this->find('first', array(
                'conditions' => array(
                    'Administrator.USR_ID' => $_id
                )
            ));
            
            $user['Administrator']['DIRECTORY1'] = "user/" . $_params['Administrator']['DIRECTORY1'];
            $user['Administrator']['DIRECTORY2'] = $_params['Administrator']['DIRECTORY2'];
        } else {
            
            App::import('Model', 'Group');
            $group = new Group();
            $_params = $group->find('first', array(
                'conditions' => array(
                    'Group.GRP_ID' => $_id
                )
            ));
            
            $user['Administrator']['DIRECTORY1'] = "group/" . $_params['Group']['DIRECTORY1'];
            $user['Administrator']['DIRECTORY2'] = $_params['Group']['DIRECTORY2'];
            
            $_params['Administrator']['DIRECTORY1'] = "group/" . $_params['Group']['DIRECTORY1'];
            $_params['Administrator']['DIRECTORY2'] = $_params['Group']['DIRECTORY2'];
        }
        if (($_params['Administrator']['DIRECTORY1'] != null && $_params['Administrator']['DIRECTORY2'] != null) && is_dir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'])) {
            if ($handle = opendir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/thumbnail")) {
                
                /* ディレクトリをループする際の正しい方法です */
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        echo "$file\n";
                        unlink(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/thumbnail/" . $file);
                    }
                }
            }
            
            rmdir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/thumbnail");
            
            if ($handle = opendir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/storage")) {
                /* ディレクトリをループする際の正しい方法です */
                
                while (false !== ($file = readdir($handle))) {
                    if ($file != '.' && $file != '..') {
                        echo "$file\n";
                        unlink(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/storage/" . $file);
                    }
                }
            }
            
            rmdir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/storage");
            rmdir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2']);
        }
        
        App::import('Model', 'Storage');
        $storage = new Storage();
        $result = $storage->find('all', array(
            'conditions' => array(
                'Storage.USR_ID' => $_id
            )
        ));
        
        $p = array();
        
        foreach ($result as $key => $val) {
            $storage->create();
            if (! $storage->delete($val['Storage']['FLE_ID'])) {
                return false;
            }
        }
    }

    /**
     * フォルダを空にする（本編では未使用）
     *
     * @param unknown $_id            
     * @param string $stat            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Truncate_Directory($_id, $stat = "user")
    {
        if ($stat == 'user') {
            $_params = $this->find('first', array(
                'conditions' => array(
                    'Administrator.USR_ID' => $_id
                )
            ));
            
            $user['Administrator']['DIRECTORY1'] = "user/" . $_params['Administrator']['DIRECTORY1'];
            $user['Administrator']['DIRECTORY2'] = $_params['Administrator']['DIRECTORY2'];
        } else {
            App::import('Model', 'Group');
            $group = new Group();
            $_params = $group->find('first', array(
                'conditions' => array(
                    'Group.GRP_ID' => $_id
                )
            ));
            
            $user['Administrator']['DIRECTORY1'] = "group/" . $_params['Group']['DIRECTORY1'];
            $user['Administrator']['DIRECTORY2'] = $_params['Group']['DIRECTORY2'];
        }
        if (($_params['Administrator']['DIRECTORY1'] != null && $_params['Administrator']['DIRECTORY2'] != null) && is_dir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'])) {
            
            if ($handle = opendir(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/storage")) {
                
                /* ディレクトリをループする際の正しい方法です */
                while (false !== ($file = readdir($handle))) {
                    
                    if ($file != '.' && $file != '..') {
                        echo "$file\n";
                        unlink(APP . "files/" . $user['Administrator']['DIRECTORY1'] . "/" . $user['Administrator']['DIRECTORY2'] . "/storage/" . $file);
                    }
                }
            }
        }
        
        if (isset($handle) && $handle) {
            closedir($handle);
        }
        
        App::import('Model', 'Storage');
        $storage = new Storage();
        $result = $storage->find('all', array(
            'conditions' => array(
                'Storage.USR_ID' => $_id
            )
        ));
        
        $p = array();
        foreach ($result as $key => $val) {
            $storage->create();
            if (! $storage->delete($val['Storage']['FLE_ID'])) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * サムネイルを保存するための関数
     *
     * @param unknown $_params
     *            (保存したいデータ)
     * @param unknown $_user
     *            (ユーザのデータ)
     * @param string $_status
     *            (新規保存か編集かのステータス)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Save_Image($_params, $_user, $_status = null)
    {
        // ストレージモデルの読み込み
        App::import('Model', 'Storage');
        $storage = new Storage();
        
        // ステータスのチェック
        if ($_status == 'edit') {
            
            // 編集である場合
            // フォルダを取得
            $root = $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/thumbnail/";
            
            // ファイルがアップロードされたかどうかの確認
            if ($_params['Profile']['FILE']['size'] > 0) {
                
                if (is_uploaded_file($_params['Profile']['FILE']['tmp_name'])) {
                    // IE用拡張子判断
                    $extension = strtolower(pathinfo($_params['Profile']['FILE']['name'], PATHINFO_EXTENSION));
                    
                    if ($extension === 'png' || $extension === 'jpeg' || $extension === 'jpg' || $extension === 'gif') {
                        
                        // 画像typeであるか確認
                        if ($_params['Profile']['FILE']['type'] === 'image/gif' || $_params['Profile']['FILE']['type'] === 'image/jpeg' || $_params['Profile']['FILE']['type'] === 'image/pjpeg' || $_params['Profile']['FILE']['type'] === 'image/png' || $_params['Profile']['FILE']['type'] === 'image/x-png') {
                            if ($_params['Profile']['FILE']['type'] === 'image/pjpeg') {
                                $_params['Profile']['FILE']['type'] = 'image/jpeg';
                            }
                            if ($_params['Profile']['FILE']['type'] === 'image/x-png') {
                                $_params['Profile']['FILE']['type'] = 'image/png';
                            }
                            $info = getimagesize($_params['Profile']['FILE']['tmp_name']);
                            
                            // 正しい画像ファイルであるかを確認
                            if ($info['mime'] == $_params['Profile']['FILE']['type']) {
                                
                                // 画像のリサイズ処理と保存
                                $storage->Image_Resize($_params['Profile']['FILE'], APP . "files/user/" . $root . "thumbnail", 'thumbnail');
                                $storage->Image_Resize($_params['Profile']['FILE'], APP . "files/user/" . $root . "preview", 'preview');
                                if (move_uploaded_file($_params['Profile']['FILE']['tmp_name'], APP . "files/user/" . $root . "original")) {
                                    $result = array();
                                    $result['USR_ID'] = $_user['User']['USR_ID'];
                                    $result['THUMBNAIL'] = $_params['Profile']['FILE']['name'];
                                    
                                    // ユーザ情報の更新
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
            
            // ユーザ作成時の場合
            $root = $_user['DIRECTORY1'] . "/" . $_user['DIRECTORY2'] . "/thumbnail/";
            
            // 画像を５種類の色からランダムで選択（暫定処理）
            $img = "img/sample/sample" . rand(1, 5) . ".jpg";
            
            // 画像ファイルの移動処理
            if (copy("img/common/original", APP . "files/user/" . $root . "original") && copy("img/common/preview", APP . "files/user/" . $root . "preview") && copy("img/common/thumbnail", APP . "files/user/" . $root . "thumbnail")) {
                $result = array();
                $result['USR_ID'] = $_user['USR_ID'];
                $result['THUMBNAIL'] = "default.jpg";
                
                // ユーザ情報更新
                if ($this->save($result)) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * サムネイルの削除
     *
     * @param unknown $_id            
     * @return void
     * @access public
     * @author 作成者
     */
    public function Delete_Thumbnail($_id)
    {
        $result = $this->find('first', array(
            'conditions' => array(
                'Administrator.USR_ID' => $_id
            ),
            'fields' => 'Administrator.USR_ID, Administrator.DIRECTORY1, Administrator.DIRECTORY2, Administrator.THUMBNAIL'
        ));
        
        if (is_dir(APP . "files/user/" . $result['Administrator']['DIRECTORY1'] . "/" . $result['Administrator']['DIRECTORY2'] . "/thumbnail")) {
            $hand = opendir(APP . "files/user/" . $result['Administrator']['DIRECTORY1'] . "/" . $result['Administrator']['DIRECTORY2'] . "/thumbnail");
            
            while ($strFile = readdir($hand)) {
                
                if ($strFile != '.' && $strFile != '..') {
                    // ディレクトリでない場合のみ
                    unlink(APP . "files/user/" . $result['Administrator']['DIRECTORY1'] . "/" . $result['Administrator']['DIRECTORY2'] . "/thumbnail/" . $strFile);
                }
            }
        }
    }

    /**
     * ユーザを保存するための関数
     *
     * @param unknown $_data
     *            (保存したい処理（$_data['Administrator']があることが必須）)
     * @param string $_auth
     *            (管理者権限を与えるかどうか)
     * @param string $_mail
     *            (メールのバリデーションが通っているかどうか)
     * @return Ambigous <NULL>|boolean
     * @access public
     * @author 作成者
     */
    public function User_Save($_data, $_auth = null, $_mail = true)
    {
        
        // 管理者権限を与えるかどうかのチェック
        if ($_auth != null) {
            
            // 管理者にする場合
            $_data['Administrator']['AUTHORITY'] = User::AUTHORITY_TRUE;
        } else {
            
            // 一般ユーザの場合
            $_data['Administrator']['AUTHORITY'] = User::AUTHORITY_FALSE;
        }
        
        // ステータスのチェック(承認制の場合のための処理)
        if (! isset($_data['Administrator']['STATUS'])) {
            $_data['Administrator']['STATUS'] = User::STATUS_ENABLED;
        }
        
        // 日付の保存
        $data['Administrator']['RANDOM_KEY'] = NULL;
        $_data['Administrator']['INSERT_DATE'] = date("Y-m-d H:i:s");
        $_data['Administrator']['LAST_UPDATE'] = date("Y-m-d H:i:s");
        $_data['Administrator']['DEL_FLG'] = 0;
        
        // グループモデルの読み込み
        App::import('Model', 'Group');
        $group = new Group();
        $result = array();
        
        // トランザクションの開始
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        
        // データのセーブとメールアドレスのチェック
        if ($this->save($_data['Administrator']) && $_mail) {
            // 成功
            // 保存したユーザIDの取得（編集の場合は$_dataから取得）
            $result['USR_ID'] = $this->getInsertID();
            if ($result['USR_ID'] == null) {
                $result['USR_ID'] = $_data['Administrator']['USR_ID'];
            }
            
            // ユーザのグループを作成
            $result['GRP_ID'] = $group->Create_My_Group($_data, $result['USR_ID']);
            
            // 作成したグループIDを更にユーザ情報に保存
            if ($this->save($result)) {
                // 成功
                $param['DIRECTORY1'] = 0;
                $param['DIRECTORY2'] = 0;
                $param['USR_ID'] = $result['USR_ID'];
                
                // ユーザごとのフォルダの作成
                $this->Create_Directory($param['USR_ID'], $param['DIRECTORY1'], $param['DIRECTORY2']);
                
                // ユーザごとにサムネイルの作成
                if ($this->Save_Image(null, $param)) {
                    // 成功
                    // 更にユーザのフォルダとサムネイルを情報として保存
                    if ($this->save($param)) {
                        $dataSource->commit($this);
                        return $result['USR_ID'];
                    } else {
                        return false;
                    }
                } else {
                    // 失敗
                    $dataSource->rollback($this);
                    return false;
                }
            }
        } else {
            // 失敗
            $dataSource->rollback($this);
            return false;
        }
    }

    /**
     * ユーザの編集処理
     *
     * @param unknown $params
     *            (保存したデータ)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function User_Edit($params)
    {
        
        // 最終更新日時を習得
        $params['Administrator']['LAST_UPDATE'] = date("Y-m-d H:i:s");
        $params['Administrator']['RANDOM_KEY'] = NULL;
        
        // セーブ
        if ($this->save($params)) {
            // 成功
            return true;
        } else {
            // 失敗
            return false;
        }
    }

    /**
     * 同じメールアドレスがあるかどうかの確認
     *
     * @param unknown $_mail
     *            (確認したメールアドレス)
     * @param string $_usrid
     *            (ユーザID)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function mail_search($_mail, $_usrid = null)
    {
        
        // $_useridのチェック
        if ($_usrid != null) {
            
            // そのユーザIDを抜かしたものと照合
            $result = $this->find('all', array(
                'fields' => array(
                    'Administrator.STATUS',
                    'Administrator.DEL_FLG'
                ),
                'conditions' => array(
                    'Administrator.MAIL' => $_mail,
                    'NOT' => array(
                        'Administrator.USR_ID' => $_usrid
                    )
                )
            ));
        } else {
            
            // すべてのユーザIDと照合
            $result = $this->find('all', array(
                'fields' => array(
                    'Administrator.STATUS',
                    'Administrator.DEL_FLG'
                ),
                'conditions' => array(
                    'Administrator.MAIL' => $_mail
                )
            ));
        }
        if ($result) {
            // データがあった場合
            return false;
        } else {
            // データがない場合
            return true;
        }
    }

    /**
     * ユーザのステータスを変更する
     *
     * @param unknown $_id
     *            (ユーザのID)
     * @param string $stat            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Change_STAT($_id, $stat = true)
    {
        
        // 現在の情報を取得
        $result = $this->find('first', array(
            'conditions' => array(
                'USR_ID' => $_id
            )
        ));
        if ($result['Administrator']['STATUS'] == User::STATUS_WITHDRAWN) {
            
            // 使用不可能になっている場合
            $params = array();
            $params['USR_ID'] = $_id;
            $params['STATUS'] = User::STATUS_ENABLED;
            
            // セーブ
            if ($this->save($params)) {
                return true;
            }
        } else {
            
            // 使用可能になっている場合
            $params = array();
            $params['USR_ID'] = $_id;
            $params['STATUS'] = User::STATUS_WITHDRAWN;
            if ($stat) {
                $params['NAME'] = NULL;
                $params['NAME_KANA'] = NULL;
                $params['UNIT'] = NULL;
                $params['POSTCODE2'] = NULL;
                $params['PASSWORD'] = NULL;
                $params['CNT_ID'] = NULL;
                $params['ADDRESS'] = NULL;
                $params['BUILDING'] = NULL;
                $params['PHONE_NO1'] = NULL;
                $params['PHONE_NO2'] = NULL;
                $params['PHONE_NO3'] = NULL;
                $params['M_PHONE_NO1'] = NULL;
                $params['M_PHONE_NO2'] = NULL;
                $params['M_PHONE_NO3'] = NULL;
                $params['DESCRIPTION'] = NULL;
            }
            // セーブ
            if ($this->save($params, false)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 退会
     *
     * @param unknown $_id            
     * @return void
     * @access public
     * @author 作成者
     */
    public function Delete_User($_id)
    {
        $result = $this->find('first', array(
            'conditions' => array(
                'Administrator.USR_ID' => $_id
            )
        ));
        
        foreach ($result['Administrator'] as $key => $val) {
            if ($key != 'USR_ID' && $key != 'NAME' && $key != 'MAIL') {
                $result['Administrator'][$key] = NULL;
            }
        }
        
        $result['Administrator']['AUTHORITY'] = User::AUTHORITY_FALSE;
        $result['Administrator']['STATUS'] = User::STATUS_WITHDRAWN;
        
        if ($this->save($result, false)) {}
    }
}
