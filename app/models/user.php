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
 * ユーザ用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class User extends AppModel
{

    /**
     * ユーザステータスに関する定数
     *
     * @var number
     * @author 作成者
     */
    const STATUS_ALL = - 1; // 全ユーザ（セレクトボックスなどで使う）
    const STATUS_WITHDRAWN = 0; // 退会済み
    const STATUS_ENABLED = 1; // 有効化済み
    const STATUS_WAITING_ACCEPTANCE = 2; // 管理者からの承認待ち
    const STATUS_WAITING_ACTIVATION = 3; // ユーザーの有効化待ち
    
    /**
     * ユーザ権限に関する定数
     *
     * @var number
     * @author 作成者
     */
    const AUTHORITY_TRUE = 0; // 管理人=0
    const AUTHORITY_FALSE = 1; // 非管理人=1
    
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'User';

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
        'UNIT' => array(
            'rule0' => array(
                'rule' => array(
                    'maxLengthJP',
                    30
                ),
                'message' => '部署名は30文字までです'
            )
        ),
        'ADDRESS' => array(
            'rule0' => array(
                'rule' => array(
                    'maxLengthJP',
                    50
                ),
                'message' => '住所は50文字までです'
            )
        ),
        'POSTCODE1' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => '正しい郵便番号を入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    3
                ),
                'message' => '正しい郵便番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい郵便番号を入力してください'
            ),
            "rule4" => array(
                'rule' => array(
                    'post_codes',
                    'POSTCODE2'
                ),
                'message' => '正しい郵便番号を入力してください'
            )
        ),
        'POSTCODE2' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => 'スペース以外も入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    4
                ),
                'message' => '正しい郵便番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい郵便番号を入力してください'
            ),
            "rule4" => array(
                'rule' => array(
                    'post_codes',
                    'POSTCODE1'
                ),
                'message' => '正しい郵便番号を入力してください'
            )
        ),
        'PHONE_NO1' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => '電話番号を入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    4
                ),
                'message' => '正しい電話番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい電話番号を入力してください'
            ),
            "rule4" => array(
                'rule' => array(
                    'phone_no_max',
                    'PHONE_NO2',
                    'PHONE_NO3',
                    10,
                    11
                ),
                'message' => '正しい電話番号を入力してください'
            )
        ),
        'PHONE_NO2' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => '電話番号を入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    4
                ),
                'message' => '正しい電話番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい電話番号を入力してください'
            )
        ),
        'PHONE_NO3' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => '電話番号を入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    4
                ),
                'message' => '正しい電話番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい電話番号を入力してください'
            )
        ),
        'M_PHONE_NO1' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => '携帯番号を入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    4
                ),
                'message' => '正しい携帯番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい携帯番号を入力してください'
            ),
            "rule4" => array(
                'rule' => array(
                    'phone_no_max',
                    'M_PHONE_NO2',
                    'M_PHONE_NO3',
                    10,
                    11
                ),
                'message' => '正しい携帯番号を入力してください'
            )
        ),
        'M_PHONE_NO2' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => '携帯番号を入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    4
                ),
                'message' => '正しい携帯番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい携帯番号を入力してください'
            )
        ),
        'M_PHONE_NO3' => array(
            "rule0" => array(
                'rule' => array(
                    'spaceOnly'
                ),
                'message' => '携帯番号を入力してください'
            ),
            "rule2" => array(
                'rule' => array(
                    'maxLengthJP',
                    4
                ),
                'message' => '正しい携帯番号を入力してください'
            ),
            "rule3" => array(
                'rule' => 'Numberonly',
                'message' => '正しい携帯番号を入力してください'
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
            )
        ),
        'DESCRIPTION' => array(
            "rule0" => array(
                'rule' => array(
                    'maxLengthJP',
                    150
                ),
                'message' => '自己紹介は150文字までです。'
            )
        ),
        'MAIL' => array(
            "rule0" => array(
                'rule' => array(
                    'maxLengthJP',
                    256
                ),
                'message' => '正しいメールアドレスを入力してください。'
            ),
            "rule1" => array(
                'rule' => array(
                    'email'
                ),
                'message' => '正しいメールアドレスを入力してください。'
            ),
            "rule1" => array(
                'rule' => array(
                    'notEmpty'
                ),
                'message' => '正しいメールアドレスを入力してください。'
            )
        )
    );

    /**
     * ハッシュパスワード生成
     *
     * @param array $data            
     * @param string $enforce            
     * @return array
     * @access public
     * @author 作成者
     */
    public function hashPasswords($data, $enforce = false)
    {
        if ($enforce && isset($this->data[$this->alias]['password'])) {
            if (! empty($this->data[$this->alias]['password'])) {
                $this->data[$this->alias]['password'] = Security::hash($this->data[$this->alias]['password'], null, true);
            }
        }
        return $data;
    }

    /**
     * beforeSave()
     *
     * 保存の前処理のロジック
     *
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function beforeSave()
    {
        $this->hashPasswords(null, true);
        return true;
    }

    /**
     * ユーザを保存するための関数
     *
     * @param array $_data            
     * @param string $_auth            
     * @param string $_mail            
     * @param string $_rec            
     * @param string $_approval            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function User_Regist($_data, $_auth = null, $_mail = true, $_rec = true, $_approval = false)
    {
        
        /*
         * リクエストモデル
         */
        App::import('Model', 'Request');
        $request = new Request();
        
        /*
         * 管理者か否かで権限のステータスを切り替える
         */
        if ($_auth != null) {
            $_data['User']['AUTHORITY'] = User::AUTHORITY_TRUE;
        } else {
            $_data['User']['AUTHORITY'] = User::AUTHORITY_FALSE;
        }
        
        if (! isset($_data['User']['STATUS'])) {
            $_data['User']['STATUS'] = User::STATUS_WAITING_ACCEPTANCE;
        }
        
        $_data['User']['RANDOM_KEY'] = null;
        $_data['User']['INSERT_DATE'] = date("Y-m-d H:i:s");
        $_data['User']['LAST_UPDATE'] = date("Y-m-d H:i:s");
        
        // トランザクションの開始
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        
        if (isset($_mail["Administrator"]['USR_ID']) && $_mail["Administrator"]["STATUS"] == 2) {
            $_data['User']['USR_ID'] = $_mail["Administrator"]['USR_ID'];
        }
        
        // 更新可能フィールド
        $fieldList = array(
            "NAME",
            "MAIL",
            "STATUS",
            "USR_ID",
            "PASSWORD",
            "AUTHORITY",
            "RANDOM_KEY",
            "INSERT_DATE",
            "UPDATE_DATE"
        );
        
        if ($result = $this->save($_data['User'], true, $fieldList) && $_mail) {
            if (isset($_mail["Administrator"]['USR_ID']) && $_mail["Administrator"]["STATUS"] == 2) {
                $usrid = $_mail["Administrator"]['USR_ID'];
            } else {
                $usrid = $this->getInsertID();
            }
            
            if ($_rec) {
                if ($token = $request->User_Request($usrid, $_data['User']['NAME'], true, $_approval)) {
                    $dataSource->commit($this);
                    return $token;
                } else {
                    // 失敗
                    $dataSource->rollback($this);
                    return false;
                }
            } else {
                $dataSource->commit($this);
                $usrid = $this->getInsertID();
                
                if ($usrid == null) {
                    $usrid = $_data['User']['USR_ID'];
                }
                
                return $usrid;
            }
        } else {
            // 失敗
            $dataSource->rollback($this);
            return false;
        }
    }
}
