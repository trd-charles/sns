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
 * 環境設定のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Configuration extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Configurations';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_CONFIGURE';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'CON_ID';

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
     * 環境設定を保存するための関数
     *
     * @param unknown $_params
     *            (保存したいデータ)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function save_conf($_params)
    {
        
        // 現在のデータを取得
        $invite_v = $this->find('first', array(
            'conditions' => array(
                'NAME' => 'INVIETE'
            )
        ));
        $apporoval_v = $this->find('first', array(
            'conditions' => array(
                'NAME' => 'APPOROVAL'
            )
        ));
        $general_v = $this->find('first', array(
            'conditions' => array(
                'NAME' => 'GENERAL'
            )
        ));
        $withdrawal_v = $this->find('first', array(
            'conditions' => array(
                'NAME' => 'WITHDRAWAL'
            )
        ));
        $ngword_v = $this->find('first', array(
            'conditions' => array(
                'NAME' => 'NGWORD'
            )
        ));
        
        if (empty($ngword_v)) {
            $ngword_v['Configuration']['NAME'] = 'NGWORD';
        }
        
        // 取得したデータを上書き
        $invite_v['Configuration']['VALUE'] = $_params['INVITE'];
        $apporoval_v['Configuration']['VALUE'] = $_params['APPOROVAL'];
        $general_v['Configuration']['VALUE'] = $_params['GENERAL'];
        $withdrawal_v['Configuration']['VALUE'] = $_params['WITHDRAWAL'];
        $ngword_v['Configuration']['VALUE'] = $_params['NGWORD'];
        
        $result = array();
        array_push($result, $invite_v, $apporoval_v, $general_v, $withdrawal_v, $ngword_v);
        
        // 保存
        if ($this->saveAll($result)) {
            return true;
        }
        return false;
    }

    /**
     * 環境設定を保存するための関数
     *
     * @param unknown $data
     *            (保存したいデータ)
     * @param unknown $data_name
     *            (保存したいデータの名前の配列)
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function save_mail_conf($data, $data_name)
    {
        // $dataの要素キー→NAME、データ→VALUEとして、保存する（存在しない場合は新しく作成する）
        $result = array();
        
        foreach ($data_name as $d_name) {
            
            if (array_key_exists($d_name, $data['Configuration'])) {
                $data_v = $this->find('first', array(
                    'conditions' => array(
                        'NAME' => $d_name
                    )
                ));
                
                if (empty($data_v)) {
                    $data_v['Configuration']['NAME'] = $d_name;
                }
                
                $data_v['Configuration']['VALUE'] = $data['Configuration'][$d_name];
                array_push($result, $data_v);
            }
        }
        
        // 画像やリンクのためのwebrootを取得し、データベースに保存する
        $data_v = $this->find('first', array(
            'conditions' => array(
                'NAME' => 'WEBROOT'
            )
        ));
        
        if (empty($data_v)) {
            $data_v['Configuration']['NAME'] = 'WEBROOT';
        }
        
        if ($data_v['Configuration']['VALUE'] != $data['Configuration']['WEBROOT']) {
            $data_v['Configuration']['VALUE'] = $data['Configuration']['WEBROOT'];
            array_push($result, $data_v);
        }
        
        // 保存
        if ($this->saveAll($result)) {
            return true;
        }
        return false;
    }

    /**
     * エラーメッセージ用の配列を作成
     *
     * @param unknown $data            
     * @return array:
     * @access public
     * @author 作成者
     */
    public function validate_mail($data)
    {
        // エラーメッセージ用の空の配列を作成
        $errors = array();
        
        // バリデーション
        if ($data['SMTP_STATUS'] == '1') {
            if ($data['FROM_NAME'] == '') {
                $errors = array_merge($errors, array(
                    'FROM_NAME' => '送信者名は必須項目です。'
                ));
            }
            
            if ($data['FROM_MAIL'] == '') {
                $errors = array_merge($errors, array(
                    'FROM_MAIL' => 'メールアドレスは必須項目です。'
                ));
            } else 
                if (strpos($data['FROM_MAIL'], '@') == false) {
                    $errors = array_merge($errors, array(
                        'FROM_MAIL' => '正しいメールアドレスを入力してください。'
                    ));
                }
            
            if ($data['SMTP_HOST'] == '') {
                $errors = array_merge($errors, array(
                    'SMTP_HOST' => 'SMTPホストは必須項目です。'
                ));
            }
            
            if ($data['SMTP_PORT'] == '') {
                $errors = array_merge($errors, array(
                    'SMTP_PORT' => 'ポート番号は必須項目です。'
                ));
            } else 
                if (! is_numeric($data['SMTP_PORT'])) {
                    $errors = array_merge($errors, array(
                        'SMTP_PORT' => 'ポート番号には半角数字を入力してください。'
                    ));
                }
            
            if ($data['SMTP_PROTOCOL'] == '1') {
                if ($data['SMTP_USER'] == '') {
                    $errors = array_merge($errors, array(
                        'SMTP_USER' => 'SMTPユーザは必須項目です。'
                    ));
                }
                if ($data['SMTP_PASS'] == '') {
                    $errors = array_merge($errors, array(
                        'SMTP_PASS' => 'SMTPパスワードは必須項目です。'
                    ));
                }
            }
        }
        return $errors;
    }
}
