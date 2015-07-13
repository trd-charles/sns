<?php

/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */

class AppController extends Controller
{

    public $autoLayout = false;

    var $helpers = array(
        'Session',
        'Form',
        'Html',
        'Js' => array(
            'jquery'
        ),
        'CustomHtml',
        'CustomJs',
        'time'
    );

    var $uses = array(
        'Request',
        'Message',
        'Notice',
        'Configuration',
        'User',
        'Administrator',
        'Join'
    );

    var $components = array(
        'Session',
        'Auth',
        'Cookie',
        'Common',
        'RequestHandler'
    );

    function beforeFilter()
    {
        // ***メンテナンス**********
        $flg = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'MAINTE_FLG'
            )
        ));
        $user = $this->Auth->user();
        if ($flg['Configuration']['VALUE'] == 1) {
            if ($user['User']['AUTHORITY'] != User::AUTHORITY_TRUE) {
                $this->redirect(array(
                    'controller' => 'errors',
                    'action' => 'index',
                    $id = 'mainte'
                ));
            }
        }
        
        // ***IP,ホスト制限**********
        $ip_user = $this->RequestHandler->getClientIP(); // IP取得
        $host_user = gethostbyaddr($_SERVER["REMOTE_ADDR"]); // ホスト取得
                                                             
        // 登録されているデータ取得、配列に格納
        $iphost = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'IPHOST'
            )
        ));
        $iphost = split("\n", $iphost['Configuration']['VALUE']);
        
        // ホスト照合（完全一致）
        foreach ($iphost as $val) {
            $val = str_replace(array(
                "\r\n",
                "\r",
                "\n"
            ), '', $val);
            $val = preg_quote($val);
            if (preg_match('/^' . $val . '$/', $host_user)) {
                $this->redirect(array(
                    'controller' => 'errors',
                    'action' => 'index',
                    $id = 'iphost'
                ));
            }
        }
        
        // IPアドレス照合 //
        foreach ($iphost as $val) {
            $val = str_replace(array(
                "\r\n",
                "\r",
                "\n"
            ), '', $val); // 改行削除
            $val_array = split("\\.", $val); // ピリオドで区切り配列に格納
            if ($val == "")
                continue; // 配列全て空なら抜ける
            
            if (count($val_array) == 4) {
                $val_last = end($val_array);
                $val = preg_quote($val);
                if (strlen($val_last) != 0) {
                    if (preg_match('/^' . $val . '$/', $ip_user)) {
                        $this->redirect(array(
                            'controller' => 'errors',
                            'action' => 'index',
                            $id = 'iphost'
                        ));
                    }
                } else {
                    if (preg_match('/^' . $val . '/', $ip_user)) {
                        $this->redirect(array(
                            'controller' => 'errors',
                            'action' => 'index',
                            $id = 'iphost'
                        ));
                    }
                }
            } elseif (count($val_array) < 4) {
                $val_last = end($val_array);
                if (strlen($val_last) != 0) {
                    $val = $val . '.';
                    $val = preg_quote($val);
                    if (preg_match('/^' . $val . '/', $ip_user)) {
                        $this->redirect(array(
                            'controller' => 'errors',
                            'action' => 'index',
                            $id = 'iphost'
                        ));
                    }
                } else {
                    $val = preg_quote($val);
                    if (preg_match('/^' . $val . '/', $ip_user)) {
                        $this->redirect(array(
                            'controller' => 'errors',
                            'action' => 'index',
                            $id = 'iphost'
                        ));
                    }
                }
            }
        }
        
        // プロキシサーバー経由でアクセスした際のcacheを行わない処理を追加
        $this->header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
        $this->header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        $this->header('Pragma: no-cache');
        
        $this->Auth->allow(array(
            'controller' => 'storages',
            'action' => 'thumbnail'
        ));
        $this->User_Check();
        
        $user = $this->Auth->user();
        $userid = $user['User']['USR_ID'];
        $conditions = array(
            "conditions" => array(
                "USR_ID" => $userid,
                "DEL_FLG" => "0"
            )
        );
        $isAuth = $this->Administrator->User_Index($conditions);
        $user = array();
        $user['User'] = $isAuth['0']['Administrator'];
        unset($user['PASSWORD']);
        $this->user = $user;
        
        /*
         * 検索リスト作成
         */
        
        $searchList = array(
            'user.1' => 'フォローユーザ'
        );
        
        /*
         * 参加中のグループを取得して検索リストに追加
         */
        $joinGroupData = array();
        $joinGroup = $this->Join->getJoinGroupIdAndName($this->user['User']['USR_ID'], $this->user['User']['AUTHORITY']);
        foreach ($joinGroup as $val) {
            $joinGroupData['group.' . $val['Join']['GRP_ID']] = mb_substr($val['Group']['NAME'], 0, 20);
            $searchList += $joinGroupData;
        }
        
        $this->set("searchList", $searchList);
        
        $this->set("user", $user);
        $this->set("request", $this->Request->Get_Request($this->user));
        $this->set("message", $this->Message->Get_Message($this->user, null, true));
        $this->set("notice", $this->Notice->Get_Notice($this->user));
        $this->set('invite_conf', $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'INVIETE'
            )
        )));
    }

    function Authority_Check()
    {
        if ($this->user['User']['AUTHORITY'] == User::AUTHORITY_TRUE) {
            return true;
        } else {
            return false;
        }
    }

    function month_days($m, $y)
    {
        if ($m == 4 || $m == 6 || $m == 9 || $m == 11)
            return 30;
        if ($m != 2)
            return 31;
        if ($y % 400 == 0)
            return 29;
        if ($y % 100 == 0)
            return 28;
        if ($y % 4 == 0)
            return 29;
        return 28;
    }

    function User_Check()
    {
        $user = $this->Auth->user();
        if ($user != null) {
            $user_n = $this->User->find('first', array(
                'fields' => array(
                    'User.DEL_FLG',
                    'User.STATUS'
                ),
                'conditions' => array(
                    'User.USR_ID' => $user['User']['USR_ID']
                )
            ));
            if ($user_n['User']['STATUS'] != 1) {
                $this->Session->setFlash('ユーザが使用不可能になっています。', '');
                $this->redirect(array(
                    'controller' => 'users',
                    'action' => 'logout'
                ));
            }
        }
    }
    
    // CSRF対策、トークンチェック
    function isCorrectToken($_token)
    {
        if ($_token === session_id()) {
            return true;
        } else {
            $this->data = null;
            $this->Session->setFlash('正規の画面からご利用ください。', '');
            $this->redirect(array(
                'controller' => 'users',
                'action' => 'logout'
            ));
            return false;
        }
    }
    
    // POSTかどうかの判定
    // すべてのアクションの中で、POST/GETの判定に使う
    // POSTかつフォームのデータがあり、トークンチェックも正しい場合にtrue
    // それ以外はfalse
    function checkPost()
    {
        if ($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
            if (isset($this->data) && $this->data && $this->isCorrectToken($this->data['Security']['token'])) {
                return true;
            }
        }
        return false;
    }

    function checkAjaxPost()
    {
        if ($this->RequestHandler->isPost() || $this->RequestHandler->isPut()) {
            if (isset($this->params['form']) && $this->params['form'] && $this->isCorrectToken($this->params['form']['token'])) {
                return true;
            }
        }
        return false;
    }
    
    // リクエストパラメータに主キーが含まれる場合はエラーにする
    function denyPrimaryKey($modelName)
    {
        $primaryKey = $this->{$modelName}->primaryKey;
        
        if (isset($this->data[$modelName][$primaryKey])) {
            $this->{$modelName}->invalidate($modelName . "." . $primaryKey, "不正な操作が行われました");
            $this->Session->setFlash("不正な操作が行われました");
            $this->redirect("/" . $this->params["url"]["url"]);
        }
    }
}


