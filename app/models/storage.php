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
 * ファイル用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Storage extends AppModel
{

    /**
     * ステータスに関する定数
     *
     * @var number
     * @author 作成者
     */
    const STATUS_PRIVATE = 0; // 非公開
    const STATUS_PUBLIC = 1; // 公開
    
    /**
     * 削除に関する定数
     *
     * @var unknown
     * @author ICZ
     */
    const FLG_DELETE = 1; // 論理削除
    
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Storage';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_FILE';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'FLE_ID';

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
     * レコードのソート順の初期設定
     *
     * @var array
     * @access public
     */
    public $order = array(
        'Storage.INSERT_DATE DESC'
    );

    /**
     * ファイルの保存
     *
     * @param unknown $m_class
     *            (アップロードされた場所)
     * @param unknown $_data
     *            (保存したいデータ)
     * @param unknown $_user
     *            (ユーザID)
     * @param unknown $_grpid
     *            (グループのID)
     * @param number $_public
     *            (非公開=0, 公開=1)
     * @param string $error            
     * @return boolean unknown
     * @access public
     * @author 作成者
     */
    public function Save_File($m_class, $_data, $_user, $_grpid, $_public = 1, &$error = null)
    {
        $params = array();
        $coment = null;
        
        // 保存場所の設定
        $root = $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/storage/";
        
        // トランザクションの開始
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        
        // データの調整
        if (! isset($_data['Storage'])) {
            $_data['Storage']['FILE'] = $_data['FILE'];
        }
        
        // コメントがある場合
        if (isset($_data['Timeline']['MESSAGE']) && $_data['Timeline']['MESSAGE']) {
            $coment = $_data['Timeline']['MESSAGE'];
        }
        
        $maxsize = Configure::read('FILE_ONE_MAX');
        
        // ファイルがアップロードされたかどうかの確認
        if ($_data['Storage']['FILE']['size'] > 0) {
            
            if (is_uploaded_file($_data['Storage']['FILE']['tmp_name'])) {
                
                if ($_data['Storage']['FILE']['size'] < $maxsize) {
                    
                    // アップロードされたファイルをランダムで改訂
                    $file_name = Security::hash(uniqid() . mt_rand());
                    $file_name = substr($file_name, 0, 10);
                    // ファイルのリサイズ処理
                    $this->Image_Resize($_data['Storage']['FILE'], APP . "files/user/" . $root . $file_name . "_thum", 'storage_thumbnail');
                    $this->Image_Resize($_data['Storage']['FILE'], APP . "files/user/" . $root . $file_name . "_pre", 'storage_preview');
                    
                    // アップロード
                    if (move_uploaded_file($_data['Storage']['FILE']['tmp_name'], APP . "files/user/" . $root . $file_name)) {
                        
                        $extension = strtolower(pathinfo($_data['Storage']['FILE']['name'], PATHINFO_EXTENSION));
                        // データの保存
                        $params['ORIGINAL_NAME'] = $_data['Storage']['FILE']['name'];
                        $params['RAND_NAME'] = $file_name;
                        $params['EXTENSION'] = $extension;
                        $params['USR_ID'] = $_user['User']['USR_ID'];
                        $params['GRP_ID'] = $_grpid;
                        $ver = phpversion();
                        
                        if ($_data['Storage']['FILE']['type'] === 'image/jpeg' || $_data['Storage']['FILE']['type'] === 'image/gif' || $_data['Storage']['FILE']['type'] === 'image/pjpeg' || $_data['Storage']['FILE']['type'] === 'image/png' || $_data['Storage']['FILE']['type'] === 'image/x-png') {
                            
                            $info = getimagesize(APP . "files/user/" . $root . $file_name);
                            
                            if ($info) {
                                
                                $params['F_TYPE'] = $info['mime'];
                            } else {
                                
                                return false;
                            }
                        } else {
                            
                            $params['F_TYPE'] = $_data['Storage']['FILE']['type'];
                        }
                        
                        $params['F_SIZE'] = $_data['Storage']['FILE']['size'];
                        
                        // アップロード場所がタイムラインの場合
                        if ($m_class == 'Profile' || $m_class == 'Home' || $m_class == 'Group' || $m_class == 'Note') {
                            
                            $params['PUBLIC'] = $_public;
                        }
                        
                        // 一覧に表示するかどうか
                        if ($m_class == 'Note') {
                            
                            $params['DISP'] = 0;
                        } else {
                            
                            $params['DISP'] = 1;
                        }
                        
                        $params['INSERT_DATE'] = date("Y-m-d H:i:s");
                        $params['LAST_UPDATE'] = date("Y-m-d H:i:s");
                        
                        if ($result = $this->save($params)) {
                            
                            // タイムラインモデルの取得
                            App::import('Model', 'Timeline');
                            $Timeline = new Timeline();
                            
                            // タイムラインにアップロードした情報を書き込み
                            if ($m_class == 'Note') {
                                
                                $dataSource->commit($this);
                                return $file_name;
                            } else 
                                
                                if ($id = $Timeline->Save_File($result, $_user, $this->getInsertID(), $m_class, $coment)) {
                                    // 成功
                                    $dataSource->commit($this);
                                    return true;
                                } else {
                                    
                                    // 失敗
                                    unlink(APP . "files/user/" . $root . $file_name);
                                    $dataSource->rollback($this);
                                    return false;
                                }
                        } else {
                            
                            // 失敗
                            unlink(APP . "files/user/" . $root . $file_name);
                            $dataSource->rollback($this);
                            return false;
                        }
                    }
                }
                $error = 1;
            }
        }
        // 失敗
        $dataSource->rollback($this);
        return false;
    }

    /**
     * ファイル取得
     *
     * @param unknown $_userid
     *            (ユーザーのID)
     * @param string $frag            
     * @param string $_user            
     * @return array ($conditions)
     * @access public
     * @author 作成者
     */
    public function Get_File($_userid, $frag = null, $_user = null)
    {
        $result = array();
        // 自分のファイル or not
        if (! $frag) {
            // 自分のファイル
            $result = array(
                'Storage.USR_ID' => $_userid,
                'Storage.DISP' => 1,
                'Storage.DEL_FLG' => '0'
            );
        } else {
            // 管理者 or not
            if ($_user['User']['AUTHORITY'] != 0) {
                // 公開されている全てのファイルが対象
                // 対象外は
                // ・非公開ステータスのファイル(自分の非公開ファイルも)
                // ・非公開グループのファイル
                $result = array(
                    'or' => array(
                        '1' => array(
                            'Group.TYPE' => array(
                                0,
                                2
                            ),
                            'Storage.PUBLIC' => 1,
                            'Storage.DEL_FLG' => 0,
                            'Storage.DISP' => 1,
                            'User.STATUS' => 1
                        )
                    )
                );
            } else {
                // 全てのファイル
                $result = array(
                    'Storage.DISP' => 1
                );
            }
        }
        return $result;
    }

    /**
     * ファイルIDからファイル情報を取得する
     *
     * @param number $fileId            
     * @return array
     * @access private
     * @author ICZ
     */
    private function __getFileDataFromFileId($fileId, $fields)
    {
        $result = $this->find('first', array(
            'conditions' => array(
                'Storage.FLE_ID' => $fileId
            ),
            'fields' => $fields
        ));
        
        return $result;
    }

    /**
     * ファイルを書き換える権限があるかチェックする
     *
     * @param number $fileUserId            
     * @param array $executer            
     * @return boolean
     * @access private
     * @author ICZ
     */
    private function __judActPermission($fileUserId, $executer)
    {
        $chk = false;
        if ($fileUserId == $executer['User']['USR_ID'] || $executer['User']['AUTHORITY'] == 0) {
            $chk = true;
        }
        return $chk;
    }

    /**
     * ファイルを物理削除する
     *
     * @param array $fileInfo            
     * @param array $user            
     * @return boolean
     * @access private
     * @author ICZ
     */
    private function __unlinkFile($fileInfo, $user)
    {
        $chk = false;
        
        if (unlink(APP . "files/user/" . $user['User']['DIRECTORY1'] . "/" . $user['User']['DIRECTORY2'] . "/storage/" . $fileInfo['Storage']['RAND_NAME'])) {
            
            if (file_exists(APP . "files/user/" . $user['User']['DIRECTORY1'] . "/" . $user['User']['DIRECTORY2'] . "/storage/" . $fileInfo['Storage']['RAND_NAME'] . "_thum")) {
                unlink(APP . "files/user/" . $user['User']['DIRECTORY1'] . "/" . $user['User']['DIRECTORY2'] . "/storage/" . $fileInfo['Storage']['RAND_NAME'] . "_thum");
            }
            
            if (file_exists(APP . "files/user/" . $user['User']['DIRECTORY1'] . "/" . $user['User']['DIRECTORY2'] . "/storage/" . $fileInfo['Storage']['RAND_NAME'] . "_pre")) {
                unlink(APP . "files/user/" . $user['User']['DIRECTORY1'] . "/" . $user['User']['DIRECTORY2'] . "/storage/" . $fileInfo['Storage']['RAND_NAME'] . "_pre");
            }
            
            $chk = true;
        }
        
        return $chk;
    }

    /**
     * 論理削除を行う
     *
     * DBでは論理削除だが、ファイルデータは物理削除を行う
     * 返り値：権限なし = not、 失敗 = false、 成功 = fileId
     *
     * @param number $fileId            
     * @param array $user            
     * @return string
     * @access public
     * @author ICZ
     */
    public function LogicalDelete($fileId, $user)
    {
        
        /*
         * 検証BL 妥当性チェック
         */
        if (empty($fileId)) {
            
            return 'not';
        }
        
        $fields = array(
            "Storage.USR_ID",
            "Storage.FLE_ID",
            "Storage.RAND_NAME"
        );
        $fileInfo = $this->__getFileDataFromFileId($fileId, $fields);
        
        /*
         * 検証BL ファイルの操作権限があるかどうかチェックする
         */
        if ($this->__judActPermission($fileInfo['Storage']['USR_ID'], $user) == false) {
            
            return 'not';
        } else {
            
            /*
             * 論理削除処理を実行する
             */
            $data = array(
                'Storage' => array(
                    'FLE_ID' => $fileId,
                    'DEL_FLG' => $this::FLG_DELETE
                )
            );
            $fields = array(
                'DEL_FLG'
            );
            
            if ($this->save($data, false, $fields)) {
                
                /*
                 * ファイルの物理削除処理を実行する
                 */
                if ($this->__unlinkFile($fileInfo, $user) == false) {
                    return false;
                } else {
                    return $fileId;
                }
            } else {
                
                return false;
            }
        }
    }

    /**
     * ファイルの削除
     *
     * 成功の場合そのID、失敗の場合 'false'、権限がない場合 'not'を返す
     *
     * @param unknown $_fleid
     *            (ファイルのID)
     * @param string $_user
     *            (ユーザの情報)
     * @return string
     * @access public
     * @author 作成者
     */
    public function Delete_File($_fleid, $_user = null)
    {
        // ファイルの情報の取得
        $result = $this->find('first', array(
            'conditions' => array(
                'Storage.FLE_ID' => $_fleid
            ),
            'fields' => 'Storage.USR_ID,
				Storage.FLE_ID,
				Storage.RAND_NAME'
        ));
        
        if ($result['Storage']['USR_ID'] == $_user['User']['USR_ID'] || $_user['User']['AUTHORITY'] == 0) {
            
            // 自分が所有しているファイルだったら
            // トランザクションの開始
            $dataSource = $this->getDataSource();
            $dataSource->begin($this);
            
            // データベース情報の削除
            if ($this->delete($_fleid, false)) {
                
                if (! file_exists(APP . "files/user/" . $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/storage/" . $result['Storage']['RAND_NAME'])) {
                    // ファイルが存在しない場合
                    $dataSource->commit($this);
                    return $_fleid;
                }
                
                // ファイルの削除
                if (unlink(APP . "files/user/" . $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/storage/" . $result['Storage']['RAND_NAME'])) {
                    
                    if (file_exists(APP . "files/user/" . $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/storage/" . $result['Storage']['RAND_NAME'] . "_thum")) {
                        unlink(APP . "files/user/" . $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/storage/" . $result['Storage']['RAND_NAME'] . "_thum");
                    }
                    
                    if (file_exists(APP . "files/user/" . $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/storage/" . $result['Storage']['RAND_NAME'] . "_pre")) {
                        unlink(APP . "files/user/" . $_user['User']['DIRECTORY1'] . "/" . $_user['User']['DIRECTORY2'] . "/storage/" . $result['Storage']['RAND_NAME'] . "_pre");
                    }
                    
                    // 成功
                    $dataSource->commit($this);
                    return $_fleid;
                } else {
                    
                    // 失敗
                    $dataSource->rollback($this);
                    return 'false';
                }
            } else {
                
                $dataSource->rollback($this);
                return 'false';
            }
        } else {
            
            return 'not';
        }
    }

    /**
     * 画像のリサイズ処理
     *
     * @param unknown $data
     *            (リサイズするデータ)
     * @param unknown $path
     *            (アップロード先)
     * @param string $status
     *            (変更画像の種類)
     * @return void
     * @access public
     * @author 作成者
     */
    public function Image_Resize($data, $path, $status = null)
    {
        // IE用拡張子判断
        $extension = strtolower(pathinfo($data['name'], PATHINFO_EXTENSION));
        $img_size = Configure::read('IMAGE_SIZE');
        
        if ($extension === 'png' || $extension === 'jpeg' || $extension === 'jpg' || $extension === 'gif') {
            
            // 画像typeであるか確認
            if ($data['type'] === 'image/jpeg' || $data['type'] === 'image/gif' || $data['type'] === 'image/pjpeg' || $data['type'] === 'image/png' || $data['type'] === 'image/x-png') {
                
                if ($data['type'] === 'image/pjpeg') {
                    
                    $data['type'] = 'image/jpeg';
                }
                
                if ($data['type'] === 'image/x-png') {
                    
                    $data['type'] = 'image/png';
                }
                
                $info = getimagesize($data['tmp_name']);
                
                // 正しい画像ファイルであるかを確認
                if ($info['mime'] == $data['type']) {
                    
                    // 画像のサイズを取得
                    list ($o_width, $o_height) = getimagesize($data['tmp_name']);
                    
                    if ($data['type'] === 'image/jpeg') {
                        
                        $image = imagecreatefromjpeg($data['tmp_name']);
                    } else 
                        
                        if ($data['type'] === 'image/png') {
                            
                            $image = imagecreatefrompng($data['tmp_name']);
                        } else 
                            
                            if ($data['type'] === 'image/gif') {
                                
                                $image = imagecreatefromgif($data['tmp_name']);
                            }
                    // 縦長か横長かをチェック
                    if ($o_width > $o_height) {
                        // 横長の場合
                        if ($status == 'storage_thumbnail') {
                            $rate = $img_size['Storage']['thumbnail'][0] / $o_width;
                        } elseif ($status == 'storage_preview') {
                            $rate = $img_size['Storage']['preview'][0] / $o_width;
                        }
                        if ($status == 'thumbnail') {
                            $rate = $img_size['User']['thumbnail'][0] / $o_width;
                        } elseif ($status == 'preview') {
                            $rate = $img_size['User']['preview'][0] / $o_width;
                        }
                        if ($rate > 1) {
                            $height = $o_height;
                            $width = $o_width;
                        } else {
                            $height = $o_height * $rate;
                            $width = $o_width * $rate;
                        }
                        if ($height < 1) {
                            $height = 1;
                        }
                    } else {
                        // 縦長の場合
                        if ($status == 'storage_thumbnail') {
                            $rate = $img_size['Storage']['thumbnail'][1] / $o_height;
                        } elseif ($status == 'storage_preview') {
                            $rate = $img_size['Storage']['preview'][1] / $o_height;
                        }
                        if ($status == 'thumbnail') {
                            $rate = $img_size['User']['thumbnail'][1] / $o_height;
                        } elseif ($status == 'preview') {
                            $rate = $img_size['User']['preview'][1] / $o_height;
                        }
                        if ($rate > 1) {
                            $height = $o_height;
                            $width = $o_width;
                        } else {
                            $height = $o_height * $rate;
                            $width = $o_width * $rate;
                        }
                        if ($width < 1) {
                            $width = 1;
                        }
                    }
                    $new_image = ImageCreateTrueColor($width, $height);
                    if ($status == 'storage_thumbnail' || $status == 'storage_preview') {
                        if ($data['type'] === 'image/jpeg') {
                            ImageCopyResampled($new_image, $image, 0, 0, 0, 0, $width, $height, $o_width, $o_height);
                            ImageJPEG($new_image, $path, 100);
                        } else {
                            imagealphablending($new_image, false);
                            imageSaveAlpha($new_image, true);
                            $fillcolor = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
                            imagefill($new_image, 0, 0, $fillcolor);
                            ImageCopyResampled($new_image, $image, 0, 0, 0, 0, $width, $height, $o_width, $o_height);
                            Imagepng($new_image, $path);
                        }
                        imagedestroy($image);
                        imagedestroy($new_image);
                    } else {
                        if ($status == 'thumbnail') {
                            $tmp_image = ImageCreateTrueColor($img_size['User']['thumbnail'][0], $img_size['User']['thumbnail'][1]);
                            $x = ($img_size['User']['thumbnail'][0] - $width) / 2;
                            $y = ($img_size['User']['thumbnail'][1] - $height) / 2;
                        }
                        if ($status == 'preview') {
                            $tmp_image = ImageCreateTrueColor($img_size['User']['preview'][0], $img_size['User']['preview'][1]);
                            $x = ($img_size['User']['preview'][0] - $width) / 2;
                            $y = ($img_size['User']['preview'][1] - $height) / 2;
                        }
                        $img = ImageCreateTrueColor($o_width, $o_height);
                        $color = imagecolorallocate($img, 255, 255, 255);
                        for ($x_line = 0; $x_line < $o_width; $x_line ++) {
                            for ($y_line = 0; $y_line < $o_height; $y_line ++) {
                                // インデックスの取得
                                $index = imagecolorat($image, $x_line, $y_line);
                                // 色情報の取得
                                $image_data = imagecolorsforindex($image, $index);
                                $alpha = $image_data['alpha'];
                                $col_tmp = imagecolorallocate($img, $image_data['red'], $image_data['green'], $image_data['blue']);
                                // 色情報の取得
                                if ($alpha == 127) {
                                    // ピクセルの描画
                                    imagesetpixel($img, $x_line, $y_line, $color);
                                } else {
                                    imagesetpixel($img, $x_line, $y_line, $col_tmp);
                                }
                            }
                        }
                        $backcol = imagecolorallocate($tmp_image, 255, 255, 255);
                        imagefill($tmp_image, 0, 0, $backcol);
                        ImageCopyResampled($new_image, $img, 0, 0, 0, 0, $width, $height, $o_width, $o_height);
                        imagecopy($tmp_image, $new_image, $x, $y, 0, 0, $width, $height);
                        ImageJPEG($tmp_image, $path, 100);
                        imagedestroy($img);
                        imagedestroy($image);
                        imagedestroy($new_image);
                        imagedestroy($tmp_image);
                    }
                }
            }
        }
    }

    /**
     * 公開ステータス変更
     *
     * @param unknown $_fleid
     *            (ファイルのID)
     * @return string (保存した結果)
     * @access public
     * @author 作成者
     */
    public function Change_Public($_fleid)
    {
        // ファイル情報の取得
        $result = $this->find('first', array(
            'fields' => array(
                'FLE_ID',
                'PUBLIC'
            ),
            'conditions' => array(
                'Storage.FLE_ID' => $_fleid
            )
        ));
        
        if (! $result) {
            // 情報を取得できなかった場合
            return false;
        }
        
        if ($result['Storage']['PUBLIC'] == Storage::STATUS_PRIVATE) {
            
            // 現在のステータスが非公開の場合
            $result['Storage']['PUBLIC'] = Storage::STATUS_PUBLIC;
            $this->save($result);
            return $result;
        } elseif ($result['Storage']['PUBLIC'] == Storage::STATUS_PUBLIC) {
            
            // 現在のステータスが公開の場合
            $result['Storage']['PUBLIC'] = Storage::STATUS_PRIVATE;
            $this->save($result);
            return $result;
        }
    }

    /**
     * ファイル検索
     *
     * @param unknown $_userid            
     * @param string $_name            
     * @param string $_all            
     * @return array ($conditions)
     * @access public
     * @author 作成者
     */
    public function Search_File($_userid, $_name = null, $_all = null)
    {
        if ($_all == 'all') {
            
            // 参加情報モデルの読み込み
            App::import('Model', 'Join');
            
            $join = new Join();
            // 参加しているグループの取得とリスト化
            $jg = $join->Join_Group($_userid);
            $gid_or = array();
            
            foreach ($jg as $key => $val) {
                $gid_or[$key] = $val['Join']['GRP_ID'];
            }
            
            $result = array(
                'or' => array(
                    '1' => array(
                        'Storage.USR_ID' => $_userid,
                        'Storage.ORIGINAL_NAME LIKE' => "%$_name%",
                        'Storage.DISP' => 1,
                        'Storage.DEL_FLG' => '0'
                    ),
                    '2' => array(
                        'Storage.USR_ID NOT' => $_userid,
                        'Group.TYPE' => Group::TYPE_PUBLIC,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                        'Storage.ORIGINAL_NAME LIKE' => "%$_name%",
                        'Storage.DEL_FLG' => '0',
                        'Storage.DISP' => 1,
                        'User.STATUS' => 1
                    ),
                    '3' => array(
                        'Storage.USR_ID NOT' => $_userid,
                        'Group.TYPE' => Group::TYPE_PRIVATE,
                        'Group.GRP_ID' => $gid_or,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                        'Storage.ORIGINAL_NAME LIKE' => "%$_name%",
                        'Storage.DEL_FLG' => '0',
                        'Storage.DISP' => 1,
                        'User.STATUS' => 1
                    ),
                    '4' => array(
                        'Storage.USR_ID NOT' => $_userid,
                        'Group.TYPE' => NULL,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                        'Storage.ORIGINAL_NAME LIKE' => "%$_name%",
                        'Storage.DEL_FLG' => '0',
                        'Storage.DISP' => 1,
                        'User.STATUS' => 1
                    ),
                    '5' => array(
                        'Storage.USR_ID NOT' => $_userid,
                        'Group.TYPE' => Group::TYPE_PERSONAL,
                        'Storage.PUBLIC' => Storage::STATUS_PUBLIC,
                        'Storage.ORIGINAL_NAME LIKE' => "%$_name%",
                        'Storage.DEL_FLG' => '0',
                        'Storage.DISP' => 1,
                        'User.STATUS' => 1
                    )
                )
            );
        } else {
            
            $result = array(
                'Storage.USR_ID' => $_userid,
                'Storage.ORIGINAL_NAME LIKE' => "%$_name%",
                'Storage.DISP' => 1,
                'Storage.DEL_FLG' => '0'
            );
        }
        return $result;
    }
}
