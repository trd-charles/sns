<?php

/**
 * 権限の管理、チェックを行うコンポーネント
 * pyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
class PermissionComponent extends Object
{

    /**
     * 許可されたユーザの情報
     * 動作が許可されているユーザIDの配列
     */
    var $allowUsers = array();

    /**
     * ユーザモデル
     * システム管理者の取得などで使用
     */
    var $userModel;

    /**
     * メッセージモデル
     * メッセージ送信者・受信者の取得で使用
     */
    var $messageModel;

    /**
     * グループ参加情報のモデル
     * グループ管理者や参加者の取得で使用
     */
    var $joinModel;

    /**
     * フォロー・フォロワー情報のモデル
     * フォロー・フォロワーの取得に使用
     */
    var $friendModel;

    /**
     * コンポーネントの初期化
     * コントローラでコンポーネントが指定されたタイミングで呼ばれる
     * 使用するモデルの読み込みなど
     */
    function initialize(&$controller, $settings = array())
    {
        $this->userModel = $this->getModel("User");
        $this->joinModel = $this->getModel("Join");
        $this->messageModel = $this->getModel("Message");
        $this->friendModel = $this->getModel("Friend");
    }

    /**
     * 許可するユーザの追加
     *
     * @param $user_id 許可するユーザのID            
     */
    function allow($user_id)
    {
        $this->allowUsers[] = $user_id;
    }

    /**
     * すべてのユーザを許可するユーザに追加
     */
    function allowAllUser()
    {
        $userList = $this->userModel->find("list", array(
            "conditions" => array(
                "STATUS" => User::STATUS_ENABLED
            )
        ));
        
        foreach ($userList as $user_id) {
            $this->allow($user_id);
        }
    }

    /**
     * システム管理者を許可するユーザに追加
     */
    function allowAdmin()
    {
        $adminList = $this->userModel->find("list", array(
            "conditions" => array(
                "AUTHORITY" => User::AUTHORITY_TRUE
            )
        ));
        
        foreach ($adminList as $id) {
            $this->allow($id);
        }
    }

    /**
     * 指定されたグループIDの
     * グループ管理者を許可するユーザに追加
     *
     * @param $group_id グループID            
     */
    function allowGroupAdmin($group_id)
    {
        $modelName = $this->joinModel->alias;
        $groupAdmin = $this->joinModel->find("first", array(
            "conditions" => array(
                $modelName . ".GRP_ID" => $group_id,
                $modelName . ".STATUS" => Join::STATUS_ADMINISTRATOR
            )
        ));
        
        if (isset($groupAdmin[$modelName]["USR_ID"])) {
            $this->allow($groupAdmin[$modelName]["USR_ID"]);
        }
    }

    /**
     * 指定されたグループの参加者(管理者を除く)を
     * 許可するユーザに追加
     *
     * @param $group_id グループID            
     */
    function allowGroupParticipant($group_id)
    {
        $modelName = $this->joinModel->alias;
        
        $groupParticipant = $this->joinModel->find("list", array(
            
            "conditions" => array(
                $modelName . ".GRP_ID" => $group_id,
                $modelName . ".STATUS" => Join::STATUS_JOINED
            ),
            "fields" => array(
                $modelName . ".USR_ID"
            )
        ));
        
        foreach ($groupParticipant as $id) {
            $this->allow($id);
        }
    }

    /**
     * メッセージの送信者の登録
     * 引数で渡されたメッセージIDの送信者を許可するユーザ一覧に追加する
     *
     * @param $message_id メッセージID            
     *
     */
    function allowSendUser($message_id)
    {
        $data = $this->messageModel->find("first", array(
            "conditions" => array(
                $this->messageModel->primaryKey => $message_id
            )
        ));
        if (isset($data["Message"]["S_USR_ID"])) {
            $this->allow($data["Message"]["S_USR_ID"]);
        }
    }

    /**
     * メッセージの受信者の登録
     * 引数で渡されたメッセージIDの受信者を許可するユーザ一覧に追加する
     *
     * @param $message_id メッセージID            
     *
     */
    function allowRecieveUser($message_id)
    {
        $data = $this->messageModel->find("first", array(
            "conditions" => array(
                $this->messageModel->primaryKey => $message_id
            )
        ));
        if (isset($data["Message"]["R_USR_ID"])) {
            $this->allow($data["Message"]["R_USR_ID"]);
        }
    }

    /**
     * 投稿を閲覧することができるユーザ全員を許可する
     *
     * 通常の投稿、公開グループ投稿 => 全員
     * コメント => 紐づいている投稿を閲覧できるユーザ
     * 非公開グループ投稿 => グループ参加者
     *
     * @param $timeline_id 投稿のID            
     */
    function allowReader($timeline_id)
    {
        $timelineModel = $this->getModel("Timeline");
        $timeline = $timelineModel->find("first", array(
            "conditions" => array(
                $timelineModel->alias . "." . $timelineModel->primaryKey => $timeline_id
            )
        ));
        
        if ($timeline["Timeline"]["ACT_ID"] == Timeline::ACT_ID_COMMENT) {
            $this->allowReader($timeline["Timeline"]["VAL_ID"]);
            return;
        }
        
        if ($timeline["Timeline"]["ACT_ID"] == Timeline::ACT_ID_GROUP || $timeline["Timeline"]["ACT_ID"] == Timeline::ACT_ID_FILE_GROUP) {
            
            $group_id = $timeline["Timeline"]["VAL_ID"];
            $groupModel = $this->getModel("Group");
            $group = $groupModel->find("first", array(
                "conditions" => array(
                    $groupModel->alias . "." . $groupModel->primaryKey => $group_id
                )
            ));
            
            if ($group["Group"]["TYPE"] == Group::TYPE_PRIVATE) {
                $this->allowAdmin();
                $this->allowGroupAdmin($group_id);
                $this->allowGroupParticipant($group_id);
                return;
            }
        }
        
        $this->allowAllUser();
    }

    /**
     * 引数で渡されたモデルと主キーで指定されたデータの所有者をユーザ一覧に追加する
     * タイムラインであれば投稿者など
     *
     * @param $modelName モデル名            
     * @param $id データの主キー(ID)            
     */
    function allowOwner($modelName, $id)
    {
        $model = $this->getModel($modelName);
        
        $data = $model->find("first", array(
            "conditions" => array(
                $model->primaryKey => $id
            )
        ));
        
        if (isset($data[$model->alias]["USR_ID"])) {
            $this->allow($data[$model->alias]["USR_ID"]);
        } else 
            if (isset($data["User"])) {
                $this->allow($data["User"]["USR_ID"]);
            }
    }

    /**
     * 引数で渡されたユーザがフォローしているユーザ全員を許可するユーザに追加
     *
     * @param $user_id ユーザ名            
     */
    function allowFollower($user_id)
    {
        $follower = $this->friendModel->find("all", array(
            "conditions" => array(
                "F_USR_ID" => $user_id
            )
        ));
        
        foreach ($follower as $user) {
            $this->allow($user['Friend']['USR_ID']);
        }
    }

    /**
     * 許可されているユーザ一覧の取得
     *
     * @return 許可されているユーザのIDが入った配列
     *
     */
    function getAllowUsers()
    {
        return $this->allowUsers;
    }

    /**
     * 許可されているユーザリストのクリア
     */
    function clearList()
    {
        $this->allowUsers = array();
    }

    /**
     * 許可されているかのチェック
     * 引数で渡されたユーザIDが許可されたユーザ一覧にあるかを確認する
     *
     * @param
     *            許可されていればtrue, いなければfalse
     */
    function isAllowed($user_id)
    {
        return in_array($user_id, $this->allowUsers);
    }

    /**
     * 許可されていないかのチェック
     * 引数で渡されたユーザIDが許可されたユーザ一覧にあるかを確認する
     *
     * @param
     *            許可されていなければtrue, いればfalse
     */
    function isDenied($user_id)
    {
        return ! $this->isAllowed($user_id);
    }

    /**
     * モデル名からモデルを取得する
     *
     * @param $name モデル名            
     *
     * @return モデル
     */
    function &getModel($name)
    {
        $model = null;
        
        if (PHP5) {
            $model = ClassRegistry::init($name);
        } else {
            $model = & ClassRegistry::init($name);
        }
        
        return $model;
    }
}
