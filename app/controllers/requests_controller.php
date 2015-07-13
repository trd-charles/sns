<?php

/**
 * Matcha-SNS
 *
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author
 *
 * @version $Id$
 */
/**
 * 申請管理用のコントローラクラス
 *
 * @author 作成者
 */
class RequestsController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Request";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Request"
    );

    /**
     * 自動レンダリングをするかどうか指定
     *
     * @var boolean
     * @access public
     */
    public $autoLayout = true;

    /**
     * コンポーネントを指定
     *
     * @var array
     * @access public
     */
    public $components = array(
        "Permission"
    );

    /**
     * SNS参加申請、グループ参加申請
     *
     * SNS参加申請、グループ参加申請に対して許可するか否か判断するポップアップで使うコントローラ
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function judge()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        if ($this->params['pass'][0] == 'r' || $this->params['pass'][0] == 'p' || $this->params['pass'][0] == 'c') {
            
            // トークンチェック
            if ($this->checkPost() == false) {
                $this->redirect("/home/");
            }
            
            // 存在チェック
            if ($this->Request->existsID($this->data["Request"]["REQ_ID"] == false)) {
                $this->redirect("/home/");
            }
            
            $request = $this->Request->find("first", array(
                "conditions" => array(
                    "REQ_ID" => $this->data["Request"]["REQ_ID"]
                )
            ));
            
            // 権限チェック
            // グループに関する申請はグループ管理者も許可する
            if ($request["Request"]["TYPE"] == Request::TYPE_INVITE_GROUP || $request["Request"]["TYPE"] == Request::TYPE_INVITE_SELECT_GROUP) {
                // TODO グループ周りの申請許可はシステム管理者ができていいのか？
                $this->Permission->allowOwner("Request", $request["Request"]["REQ_ID"]);
            } elseif ($request["Request"]["TYPE"] == Request::TYPE_JOIN_GROUP) {
                $this->Permission->allowGroupAdmin($request["Request"]["GRP_ID"]);
            } else {
                $this->Permission->allowOwner("Request", $request["Request"]["REQ_ID"]);
                $this->Permission->allowAdmin();
            }
            
            if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
                $this->redirect("/home/");
            }
            
            // 申請情報を変更
            if ($this->Request->Change_Request($this->data['Request']['REQ_ID'], $this->params['pass'][0], $user)) {
                // 成功
                return $this->data['Request']['no'];
            } else {
                // 失敗
                return false;
            }
        } else {
            
            // 初期表示
            // パスからIDを取得
            if (isset($this->params['pass'][1])) {
                $reqid = $this->params['pass'][1];
            }
            
            // 情報を取得
            $result = $this->Request->find('first', array(
                'conditions' => array(
                    'REQ_ID' => $reqid
                )
            ));
            if ($result["Request"]['TYPE'] == Request::TYPE_JOIN_SNS || $result["Request"]['TYPE'] == Request::TYPE_JOIN_SNS_DENY) {
                
                // タイプが参加申請の場合
                // 権限チェック
                $this->Permission->allowAdmin();
                if ($this->Permission->isAllowed($user["User"]["USR_ID"])) {
                    $this->set("no", $this->params['pass'][0]);
                    $this->set("list", $result);
                    $this->render('user', false);
                } else {
                    echo '権限がありません';
                }
            } elseif ($result["Request"]['TYPE'] == Request::TYPE_JOIN_GROUP) {
                
                // その他の場合
                // $this->Permission->allowOwner("Request", $reqid);
                
                $this->Permission->allowGroupAdmin($result["Request"]['GRP_ID']);
                
                if ($this->Permission->isAllowed($user["User"]["USR_ID"])) {
                    $this->set("no", $this->params['pass'][0]);
                    $this->set("list", $result);
                    $this->render('judge', false);
                } else {
                    echo '権限がありません';
                }
            } else {
                
                // その他の場合
                $this->Permission->allowOwner("Request", $reqid);
                if ($this->Permission->isAllowed($user["User"]["USR_ID"])) {
                    $this->set("no", $this->params['pass'][0]);
                    $this->set("list", $result);
                    $this->render('judge', false);
                } else {
                    echo '権限がありません';
                }
            }
        }
    }
}
