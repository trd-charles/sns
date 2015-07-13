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
 * 検索用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Search extends AppModel
{

    /**
     * 検索ステータスの関する定数
     *
     * @author 作成者
     */
    const SEARCH_STATUS_FORROW_USER = 'user'; // フォローしているユーザを検索
    const SEARCH_STATUS_JOIN_GROUP = 'group'; // 参加しているグループを検索
    const SEARCH_STRING_MIN_COUNT = 2; // 検索で許可する最少文字数
    
    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = array(
        'Search'
    );

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_TIME_LINE';

    /**
     * コンポーネントを指定
     *
     * @var array
     * @access public
     */
    public $components = array();

    /**
     * ビヘイビアを指定
     *
     * @var array
     * @access public
     */
    public $actsAs = array();

    /**
     * バリデーションの設定
     *
     * @var array
     * @access public
     */
    public $validate = array();

    /**
     * キーワードを検索できるSQLの条件文を返す
     *
     * @param unknown $keyword            
     * @return array
     */
    public function addKeyword($keyword)
    {
        $data = array();
        
        if (! empty($keyword)) {
            $data = array(
                'Timeline.MESSAGE LIKE' => '%' . $keyword . '%'
            );
        }
        
        return $data;
    }

    /**
     * フォローしているユーザを検索できるSQLの条件文を返す
     *
     * @param unknown $userId            
     * @return array
     * @access public
     */
    public function addConditionFollowUser($userId, $authority)
    {
        $data = array();
        
        App::import('Model', 'friend');
        $Friend = new Friend();
        
        $friend = $Friend->Get_Friend($userId);
        
        $followUserData = array();
        foreach ($friend as $val) {
            $followUserData[] = array(
                'Timeline.USR_ID' => $val['Friend']['F_USR_ID']
            );
        }
        
        if (count($followUserData) != 0) {
            $data = array(
                'OR' => $followUserData
            );
        } else {
            // 管理者でない場合は自分のみ表示
            if ($authority != 0) {
                $data = array(
                    'Timeline.USR_ID' => $userId
                );
            } else {
                $data = array();
            }
        }
        
        return $data;
    }

    /**
     * 指定グループを検索できるSQLの条件文を返す
     *
     * @param number $userId            
     * @param number $groupId            
     * @return array
     * @access public
     */
    public function addConditionSelectGroup($groupId)
    {
        $data = array();
        
        if (isset($groupId)) {
            $joinGroupData = array(
                'Timeline.VAL_ID' => $groupId
            );
            $data = array(
                'AND' => $joinGroupData
            );
        }
        
        return $data;
    }

    /**
     * 参加しているグループを検索できるSQLの条件文を返す
     *
     * @param unknown $userId            
     * @return array
     * @access public
     */
    public function addConditionJoinGroup($userId)
    {
        $data = array();
        
        App::import('Model', 'join');
        $Join = new Join();
        
        $join = $Join->Join_Group($userId);
        
        $joinGroupData = array();
        foreach ($join as $val) {
            $joinGroupData[] = array(
                'Timeline.VAL_ID' => $val['Join']['GRP_ID']
            );
        }
        
        if (count($joinGroupData) != 0) {
            $data = array(
                'OR' => $joinGroupData
            );
        } else {
            // 管理者でない場合は自分のみ表示
            if ($authority != 0) {
                $data = array(
                    'Timeline.USR_ID' => $userId
                );
            } else {
                $data = array();
            }
        }
        
        return $data;
    }
}
