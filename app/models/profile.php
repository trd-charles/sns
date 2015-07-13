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
 * プロフィール用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Profile extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Profile';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = false;

    /**
     * プロフィール保存
     *
     * @param unknown $_data
     *            (保存したいデータ)
     * @param unknown $_user
     *            (ユーザのID)
     * @param unknown $error            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Save_Profile($_data, $_user, &$error)
    {
        // ユーザモデルの読み込み
        App::import('Model', 'User');
        $user = new User();
        
        // 入力可能フィールド
        $fieldList = array(
            "NAME",
            "UNIT",
            "POSTCODE1",
            "POSTCODE2",
            "CNT_ID",
            "ADDRESS",
            "PHONE_NO1",
            "PHONE_NO2",
            "PHONE_NO3",
            "M_PHONE_NO1",
            "M_PHONE_NO2",
            "M_PHONE_NO3",
            "DESCRIPTION",
            "USR_ID"
        );
        
        // 保存
        if ($user->save($_data, true, $fieldList)) {
            // 成功
            return true;
        } else {
            // 失敗
            $error = $user->invalidFields();
            return false;
        }
    }

    /**
     * タイムラインの所得
     *
     * 条件１　Timeline.ACT_ID=>1,Timeline.USR_ID=>自分のID　自分のコメント、他人へのコメントを含む
     * 条件２　Timeline.ACT_ID=>1,Timeline.VAL_ID=>自分のグループID　自分の場所へのコメント
     * 条件３　Timeline.ACT_ID=>4,Storage.GRP_ID=>自分のグループID,'Storage.PUBLIC' =>
     * 1　自分の場所へのファイル
     *
     * @param unknown $_user
     *            (取得したいユーザのID)
     * @param string $_grpid
     *            (取得したいグループのID)
     * @param string $while            
     * @param string $_tmlid            
     * @param string $_usrid            
     * @return array ($conditions ページング条件)
     * @access public
     * @author 作成者
     */
    public function Get_Timeline($_user, $_grpid = null, $while = null, $_tmlid = null, $_usrid = null)
    {
        // プロフィールのタイムラインがログインユーザである
        if ($_usrid == null) {
            
            // 実際の構文作成
            if ($while == 1) {
                
                // 発言のみ取得
                $conditions = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.USR_ID' => $_user['User']['USR_ID']
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.VAL_ID' => $_grpid
                        )
                    )
                );
            } elseif ($while == 2) {
                
                // ファイルアップロードのみ取得
                $conditions = array(
                    'or' => array(
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.GRP_ID' => $_grpid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        )
                    )
                );
            } else {
                $conditions = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.USR_ID' => $_user['User']['USR_ID']
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.VAL_ID' => $_grpid
                        ),
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.GRP_ID' => $_grpid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        )
                    )
                );
            }
        } else {
            // プロフィールのタイムラインがログインユーザでない
            // 実際の構文作成
            if ($while == 1) {
                // 発言のみ取得
                $conditions = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.USR_ID' => $_usrid
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.VAL_ID' => $_grpid
                        )
                    )
                );
            } elseif ($while == 2) {
                // ファイルアップロードのみ取得
                $conditions = array(
                    'or' => array(
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.GRP_ID' => $_grpid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        )
                    )
                );
            } else {
                $conditions = array(
                    'or' => array(
                        '1' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.USR_ID' => $_usrid
                        ),
                        '2' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_TIMELINE,
                            'Timeline.VAL_ID' => $_grpid
                        ),
                        '3' => array(
                            'Timeline.ACT_ID' => Timeline::ACT_ID_FILE_TIMELINE,
                            'Storage.GRP_ID' => $_grpid,
                            'Storage.PUBLIC' => Storage::STATUS_PUBLIC
                        )
                    )
                );
            }
        }
        
        return $conditions;
    }
}
