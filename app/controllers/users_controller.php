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
 * ユーザ用のコントローラクラス
 *
 * @author 作成者
 */
class UsersController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "User";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "User",
        'Administrator',
        'Configuration',
        'Message',
        'Timeline',
        'Join',
        'Friend',
        'Group',
        'Notice',
        'Watch',
        'Read',
        'Request'
    );

    /**
     * 自動レンダリングをするかどうか指定
     *
     * @var boolean
     * @access public
     */
    public $autoLayout = true;

    /**
     * レイアウトファイル名を指定
     *
     * @var String
     * @access public
     */
    public $layout = 'login_layout';

    /**
     * コンポーネントを指定
     *
     * @var array
     * @access public
     */
    public $components = array(
        'Permission',
        'RequestHandler'
    );

    /**
     * コントローラのアクション前に実行
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function beforeFilter()
    {
        $this->Auth->autoRedirect = false;
        $this->Auth->allow('reset', 'reminder', 'regist', 'finish', 'active', 'mobile/mobile_users/login');
        $this->Auth->fields = array(
            'username' => 'MAIL',
            'password' => 'PASSWORD'
        );
    }

    /**
     * メールアドレスからアクティブユーザを取得
     *
     * @access private
     * @return array
     * @author ICZ
     */
    private function __getActiveUserFromEmail($email)
    {
        $option = array(
            'conditions' => array(
                'MAIL' => $email,
                'NOT' => array(
                    'STATUS' => 0
                )
            ),
            'fields' => array(
                'USR_ID',
                'MAIL',
                'NAME'
            )
        );
        return $this->User->find('first', $option);
    }

    /**
     * パスワード再設定用のメールを送信する
     *
     * @param string $key            
     * @param array $user            
     * @return number
     */
    private function __sendPasswordReminderMail($key, $user)
    {
        $url = Router::url('/users/reset/?key=' . $key, true);
        $subject = "【抹茶SNS】パスワード再設定のお知らせ";
        $content = array(
            'USER_NAME' => $user['User']['NAME'],
            'URL' => $url
        );
        
        $result = $this->Common->send_mail_beta($this->data['User']['MAIL'], $subject, $body, null, $content, 'password_reset', null, 0, 1);
        
        return $result;
    }

    /**
     * ランダムキーからユーザを取得する
     *
     * @param string $key            
     * @access private
     * @author ICZ
     */
    private function __getUserFromRandomKey($key)
    {
        $option = array(
            'conditions' => array(
                'RANDOM_KEY' => $key
            )
        );
        
        return $this->User->find('first', $option);
    }

    /**
     * パスワード再設定の妥当性チェック
     *
     * @param string $editPass            
     * @param string $editPassCheck            
     * @return boolean
     * @author ICZ
     * @access private
     */
    private function __judValidationResetPassword($editPass, $editPassCheck)
    {
        $chk = true;
        
        // 検証BL 妥当性チェック
        $passBetweenValidate = Validation::between($editPass, 6, 20);
        if (empty($passBetweenValidate)) {
            $this->User->validationErrors['EDIT_PASSWORD'] = 'パスワードは6～20文字で入力してください。';
            
            $chk = false;
        }
        
        // 検証BL 妥当性チェック
        $passValid = Validation::equalTo($editPass, $editPassCheck);
        if (empty($passValid)) {
            $this->User->validationErrors['EDIT_PASSWORD'] = 'パスワードが一致しません';
            $this->User->validationErrors['EDIT_PASSWORD_CHECK'] = 'パスワードが一致しません';
            
            $chk = false;
        }
        
        return $chk;
    }

    /**
     * 期限内か判断する
     *
     * @param datetime $lastDate            
     * @return number
     */
    private function __judExpirationDate($lastDate, $expDate)
    {
        $lDate = strtotime($lastDate);
        $nDate = strtotime(date("Y-m-d H:i:s"));
        $sDiff = $nDate - $lDate;
        return $sDiff / $expDate;
    }

    /**
     * 管理者かチェック
     *
     * @param unknown $userId            
     * @return boolean
     */
    private function __isAdmin($userId)
    {
        $chk = false;
        
        $auth = $this->Administrator->find('first', array(
            'fields' => array(
                'AUTHORITY'
            ),
            'conditions' => array(
                'Administrator.USR_ID' => $userId
            )
        ));
        
        if ($auth['Administrator']['AUTHORITY'] == 0) {
            $chk = true;
        }
        
        return $chk;
    }

    /**
     * 自分以外のメールアドレスの存在チェック
     *
     * @param number $userId            
     * @param string $email            
     * @return boolean
     */
    private function __isEmailExceptionMe($userId, $email)
    {
        $chk = false;
        
        $result = $this->User->find('count', array(
            'conditions' => array(
                'User.MAIL' => $email,
                'User.USR_ID <>' => $userId
            )
        ));
        
        if ($result != 0) {
            $chk = true;
        }
        
        return $chk;
    }

    /**
     * ログイン
     *
     * @return void
     * @access public
     * @author ICZ
     */
    public function login()
    {
        $this->set("main_title", "ログイン");
        $this->set("title_text", "ログイン");
        
        if ($this->RequestHandler->isPost()) {
            
            /*
             * 認証チェック
             */
            if ($this->Auth->User()) {
                
                $user = $this->Auth->User();
                
                /*
                 * 認証BL ユーザのステータスが退会済みの場合はエラー処理
                 */
                if ($user['User']['STATUS'] == User::STATUS_WITHDRAWN) {
                    
                    // エラー処理
                    $errorStr = 'ユーザ名またはパスワードが間違っているため、<br />ログインできませんでした。';
                    $redirectOption = array(
                        'controller' => 'users',
                        'action' => 'logout'
                    );
                    $this->Session->setFlash($errorStr, '');
                    $this->redirect($redirectOption);
                }
                
                /*
                 * 成功処理 セッションIDを書き換えた後、リダイレクト
                 */
                App::import('Helper', 'Session');
                $sessionHelper = new SessionHelper();
                $sessionHelper->__regenerateId();
                $this->redirect(array(
                    'plugin' => false,
                    'controller' => 'homes',
                    'action' => 'index'
                ));
            } else {
                
                // エラー処理
                $errorStr = 'ユーザ名またはパスワードが間違っているため、<br />ログインできませんでした。';
                $redirectOption = array(
                    'controller' => 'users',
                    'action' => 'logout'
                );
                $this->Session->setFlash($errorStr, '');
                $this->redirect($redirectOption);
            }
        }
        
        // ユーザによる登録可否の初期値を取得する
        $this->set("general_conf", $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'GENERAL'
            )
        )));
    }

    /**
     * ログアウト
     *
     * @return void
     * @access public
     * @author ICZ
     */
    public function logout()
    {
        $this->redirect($this->Auth->logout());
    }

    /**
     * パスワードリマインダメール送信
     *
     * @return void
     * @access public
     * @author ICZ
     */
    public function reminder()
    {
        $this->set("main_title", "パスワード再設定");
        $this->set("title_text", "パスワード再設定");
        
        if ($this->RequestHandler->isPost() && isset($this->data['User']['MAIL'])) {
            
            // メールアドレスからアクティブユーザを取得
            $user = $this->__getActiveUserFromEmail($this->data['User']['MAIL']);
            
            if ($this->data['User']['MAIL'] == null) {
                
                $this->User->validationErrors["MAIL"] = "メールアドレスを入力してください。";
            } elseif (empty($user)) {
                
                $this->User->validationErrors["MAIL"] = "入力されたメールアドレスは登録されていません。";
            } else {
                
                // セキュアキー発行
                $key = Security::hash(uniqid() . mt_rand());
                
                $this->User->id = $user['User']['USR_ID'];
                $this->data['User']['RANDOM_KEY'] = $key;
                $this->data['User']['LAST_UPDATE'] = date("Y-m-d H:i:s");
                
                if ($this->User->save($this->data)) {
                    
                    // 再設定メール送信
                    $result = $this->__sendPasswordReminderMail($key, $user);
                    
                    if ($result) {
                        
                        $this->Session->setFlash('メールの送信に成功しました', '');
                        $this->redirect('/users/login');
                    } else {
                        
                        $this->Session->setFlash('メールの送信に失敗しました', '');
                        $this->redirect('/users/reminder');
                    }
                }
            }
        }
    }

    /**
     * パスワードリマインダ再設定
     *
     * パスワード再設定メールに記載されたURLでこの画面にアクセスする
     *
     * @return void
     * @access public
     * @author ICZ
     */
    public function reset()
    {
        $this->set("main_title", "パスワード再設定");
        $this->set("title_text", "パスワード再設定");
        
        /*
         * 検証BL 認証用リクエストパラメータチェック
         */
        if (empty($this->params['url']['key']) && empty($this->data['User']['KEY'])) {
            $errorStr = '不正な処理です';
            $redirectOption = array(
                'controller' => '/'
            );
            $this->Session->setFlash($errorStr, '');
            $this->redirect($redirectOption);
        }
        
        if (isset($this->params['url']['key'])) {
            
            $secureKey = $this->params['url']['key'];
            $this->data['User']['KEY'] = $secureKey;
        } elseif (isset($this->data['User']['KEY'])) {
            
            $secureKey = $this->data['User']['KEY'];
        }
        
        /*
         * 検証BL 認証用リクエストパラメータチェック
         */
        $user = $this->__getUserFromRandomKey($secureKey);
        if (empty($user)) {
            $errorStr = '不正な処理です';
            $redirectOption = array(
                'controller' => '/'
            );
            $this->Session->setFlash($errorStr, '');
            $this->redirect($redirectOption);
        }
        
        /*
         * 有効期限内(1日)か判断する
         */
        $lastDate = $user['User']['LAST_UPDATE'];
        $expDate = 60 * 60 * 24;
        $diff = $this->__judExpirationDate($lastDate, $expDate);
        
        /*
         * 検証BL 有効期限かチェック
         */
        if ($diff > 1) {
            
            // パスワード・ランダムキー削除
            $this->User->id = $user['User']['USR_ID'];
            $this->data['User']['RANDOM_KEY'] = null;
            $this->data['User']['LAST_UPDATE'] = date("Y-m-d H:i:s");
            
            if ($this->User->save($this->data)) {
                $errorStr = 'URLの期限が切れています。';
                $redirectOption = array(
                    'controller' => '/'
                );
                $this->Session->setFlash($errorStr, '');
                $this->redirect($redirectOption);
            }
        }
        
        /*
         * 検証BL パスワードとパスワード確認が入力されている
         */
        if (isset($this->data['User']['EDIT_PASSWORD']) && isset($this->data['User']['EDIT_PASSWORD_CHECK'])) {
            
            /*
             * 検証BL 妥当性チェック
             */
            $result = $this->__judValidationResetPassword($this->data['User']['EDIT_PASSWORD'], $this->data['User']['EDIT_PASSWORD_CHECK']);
            
            if ($result) {
                
                $this->User->id = $user['User']['USR_ID'];
                $this->data['User']['RANDOM_KEY'] = null;
                $this->data['User']['PASSWORD'] = $this->Auth->password($this->data['User']['EDIT_PASSWORD']);
                $this->data['User']['LAST_UPDATE'] = date("Y-m-d H:i:s");
                
                if ($this->User->save($this->data)) {
                    
                    $this->Session->setFlash('パスワード変更完了しました。', '');
                    $this->redirect('/');
                } else {
                    $errorStr = 'システムエラー';
                    $redirectOption = array(
                        'controller' => '/'
                    );
                    $this->Session->setFlash($errorStr, '');
                    $this->redirect($redirectOption);
                }
            }
        }
    }

    /**
     * アクションメソッド：新規ユーザ登録完了
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function finish()
    {
        $this->set("main_title", "登録完了");
        $this->set("title_text", "登録完了");
        
        $stat = $this->params['pass'][0];
        $this->set("stat", $stat);
    }

    /**
     * アクションメソッド：新規ユーザ登録
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function regist()
    {
        $this->set("main_title", "新規登録");
        $this->set("title_text", "新規登録");
        /*
         * 新規受付の設定チェックを行う 1.新規登録と招待登録が無効な場合はエラー処理を行う 2.招待登録のみ有効の場合、トークンの妥当性チェックを行う
         */
        $gConf = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'GENERAL'
            )
        ));
        $iConf = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'INVIETE'
            )
        ));
        /*
         * 検証BL [条件]新規登録と招待登録が無効な場合 [真]エラー処理
         */
        if ($gConf['Configuration']['VALUE'] != 0 && $iConf['Configuration']['VALUE'] != 0) {
            $this->Session->setFlash('不正な操作が行われました', '');
            $this->redirect('../');
        }
        /*
         * 検証BL [条件]招待登録のみ有効の場合 [真]トークンの妥当性チェックを行う
         */
        if ($gConf['Configuration']['VALUE'] == 1 && $iConf['Configuration']['VALUE'] == 0) {
            /*
             * 検証ビジネスロジック 1.$_GET['token']がない → エラー処理 2.$_GET['token']がDBに登録されていない → エラー処理
             */
            if (! isset($_GET['token'])) {
                $this->Session->setFlash('不正な操作が行われました', '');
                $this->redirect('../');
            } else {
                $this->set("token", $_GET['token']);
                $request = $this->Request->find('first', array(
                    'conditions' => array(
                        'Request.TOKEN' => $_GET['token']
                    )
                ));
                if (empty($request)) {
                    $this->Session->setFlash('無効なトークンです', '');
                    $this->redirect('../');
                }
            }
        }
        if ($this->RequestHandler->isPost()) {
            /*
             * セキュリティー対策 削除禁止
             */
            $this->denyPrimaryKey("User");
            /*
             * リクエストパラメータの妥当性チェックを行う
             */
            $this->Administrator->set($this->data['User']);
            $error = $this->Administrator->invalidFields(array(
                'fieldList' => 'EDIT_PASSWORD'
            ));
            if (! isset($error['EDIT_PASSWORD'])) {
                // 暗号化されたパスワードをセット
                $this->data['User']['PASSWORD'] = $this->Auth->password($this->data['User']['EDIT_PASSWORD']);
            }
            $mail = $this->Administrator->Mail_Search($this->data['User']['MAIL']);
            $params = array();
            $apporovalConfig = $this->Configuration->find('first', array(
                'conditions' => array(
                    'NAME' => 'APPOROVAL'
                )
            ));
            $this->Administrator->set($this->data['User']["MAIL"]);
            $vlMail = $this->Administrator->invalidFields();
            // ユーザが有効化を行わずに再度登録した場合
            if ($mail) {
                $registedUser = $this->User->find('first', array(
                    'conditions' => array(
                        'User.MAIL' => $this->data['User']['MAIL']
                    )
                ));
                $registedRequest = $this->Request->find('first', array(
                    'conditions' => array(
                        'Request.USR_ID' => $registedUser['User']['USR_ID'],
                        'Request.TYPE' => array(
                            Request::TYPE_WAITING_ACTIVATION,
                            Request::TYPE_JOIN_SNS
                        )
                    )
                ));
                // 有効化待ち・承認待ち状態のユーザとリクエストがある場合
                if (! empty($registedUser) && ! empty($registedRequest)) {
                    // 承認待ちの確認
                    if ($registedRequest["Request"]["TYPE"] == Request::TYPE_JOIN_SNS) {
                        $this->Administrator->validationErrors["MAIL"] = "すでに登録申請されています。";
                        $this->User->validationErrors = $this->Administrator->validationErrors;
                        return false;
                    } else {
                        // 有効化待ちの確認
                        // リクエストの有効期限チェック
                        $lDate = strtotime($registedRequest['Request']['INSERT_DATE']);
                        $nDate = strtotime(date("Y-m-d H:i:s"));
                        $sDiff = $nDate - $lDate;
                        $diff = $sDiff / (60 * 60 * 24);
                        if ($diff > 1) {
                            // 有効期限が切れている場合はDB上のリクエストとユーザを削除
                            $this->Request->delete($registedRequest['Request']['REQ_ID']);
                            $this->User->delete($registedUser['User']['USR_ID']);
                            $mail = true;
                        } else {
                            // 有効期限内に再登録しようとした場合
                            $this->Administrator->validationErrors["MAIL"] = "すでに登録申請されています。メールに送信されたユーザ有効化URLから有効化を行ってください。";
                            $this->User->validationErrors = $this->Administrator->validationErrors;
                            return false;
                        }
                    }
                }
                // メールアドレスがあり、ユーザが有効の場合
                if ($registedUser['User']['STATUS'] == User::STATUS_ENABLED) {
                    $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは既に登録されています。";
                    $this->User->validationErrors = $this->Administrator->validationErrors;
                    return false;
                } elseif ($registedUser["User"]["STATUS"] === User::STATUS_WITHDRAWN) { // すでに退会しているユーザ
                    $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                    $this->User->validationErrors = $this->Administrator->validationErrors;
                    return false;
                }
            }
            if ($apporovalConfig['Configuration']['VALUE'] != 1) {
                $this->data['User']['STATUS'] = 2;
                $this->data['User']['DEL_FLG'] = 1;
                if (! $vlMail) {
                    $id = $this->User->User_Regist($this->data, null, $mail, true, true);
                    if ($id != false) {
                        // 成功
                        // メール送信
                        $subject = "【抹茶SNS】" . "参加申請受付のお知らせ";
                        if ($this->Common->send_mail_beta($this->data['User']['MAIL'], $subject, null, null, null, 'join_application_user', null, 0, 1)) {
                            $subject = "【抹茶SNS】" . "参加申請のお知らせ";
                            $content = array(
                                'USER_NAME' => $this->data['User']['NAME'],
                                'USER_MAIL' => $this->data['User']['MAIL']
                            );
                            if ($this->Common->send_mail_beta(null, $subject, null, null, $content, 'join_application_admin', null, 0, 1)) {
                                if ($gConf['Configuration']['VALUE'] == 1 && $iConf['Configuration']['VALUE'] == 0) {
                                    $request['Request']['TOKEN'] = null;
                                    $this->Request->save($request['Request']);
                                }
                            }
                            $this->Session->setFlash('申請が完了しました。管理者からの認証が必要になりますのでしばらくお待ちください。', '');
                            $this->redirect("login");
                        } else {
                            $this->Session->setFlash('メールの送信に失敗しました。正しいメールアドレスか確認してください。', '');
                            $this->redirect("login");
                        }
                    } else {
                        // 失敗
                        if (! $mail) {
                            $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                        }
                        $this->User->validationErrors = $this->Administrator->validationErrors;
                    }
                } else {
                    // 失敗
                    if (! $mail) {
                        $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                    }
                    $this->User->validationErrors = $this->Administrator->validationErrors;
                }
            } else {
                $this->data['User']['STATUS'] = 3;
                $this->data['User']['DEL_FLG'] = 1;
                $params = array();
                foreach ($this->data['User'] as $key => $val) {
                    $params['Administrator'][$key] = $val;
                }
                if (! $vlMail) {
                    $id = $this->User->User_Regist($this->data, null, $mail);
                    if ($id != false) {
                        // メール送信
                        $subject = "【抹茶SNS】" . "登録完了";
                        $content = array(
                            'URL' => Router::url(array(
                                'controller' => 'users',
                                'action' => 'active'
                            ), true) . '?token=' . $id
                        );
                        if ($this->Common->send_mail_beta($this->data['User']['MAIL'], $subject, $body = null, null, $content, 'register_complete', null, 0, 1)) {
                            // 成功
                            if ($gConf['Configuration']['VALUE'] == 1 && $iConf['Configuration']['VALUE'] == 0) {
                                $request['Request']['TOKEN'] = null;
                                $this->Request->save($request['Request']);
                            }
                            $this->Session->setFlash('登録したメールアドレスに送信されたURLからユーザの有効化を行ってください', '');
                            $this->redirect("login");
                        } else {
                            $this->Session->setFlash('メールの送信に失敗しました。正しいメールアドレスか確認してください。', '');
                            $this->redirect("login");
                        }
                    } else {
                        // 失敗
                        if (! $mail) {
                            $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                        }
                        $this->User->validationErrors = $this->Administrator->validationErrors;
                    }
                } else {
                    // 失敗
                    if (! $mail) {
                        $this->Administrator->validationErrors["MAIL"] = "そのメールアドレスは使用できません。";
                    }
                    $this->User->validationErrors = $this->Administrator->validationErrors;
                }
            }
        }
    }

    /**
     * ユーザ編集
     *
     * @return void
     * @access public
     * @author ICZ
     */
    public function edit()
    {
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ユーザ管理");
        
        $user = $this->Auth->user();
        
        /*
         * 検証BL ユーザIDの存在チェック
         */
        if (empty($user["User"]["USR_ID"])) {
            $errorStr = '不正な処理です';
            $redirectOption = array(
                'controller' => '/'
            );
            $this->Session->setFlash($errorStr, '');
            $this->redirect($redirectOption);
        }
        
        if ($this->checkPost()) {
            
            $this->data['User']['USR_ID'] = $user['User']['USR_ID'];
            $this->data['User']['STATUS'] = $user['User']['STATUS'];
            
            $this->Administrator->set($this->data['User']);
            $error = $this->Administrator->invalidFields(array(
                'fieldList' => 'MAIL'
            ));
            
            /*
             * 検証BL メールアドレスの妥当性チェック
             */
            if (empty($this->data['User']['MAIL'])) {
                
                $error['MAIL'] = "メールアドレスは必須です。";
                $this->User->validationErrors["MAIL"] = $error['MAIL'];
            } else {
                
                $isEmail = $this->__isEmailExceptionMe($user['User']['USR_ID'], $this->data['User']['MAIL']);
                
                if ($isEmail == true) {
                    $error['MAIL'] = "そのメールアドレスは使用できません。";
                    $this->User->validationErrors["MAIL"] = $error['MAIL'];
                }
            }
            
            /*
             * 検証BL 条件分岐によるパスワード変更
             */
            if ($this->data['User']['PASS_C'] == 1) {
                
                $error = $this->Administrator->invalidFields(array(
                    'fieldList' => 'EDIT_PASSWORD'
                ));
                
                if (isset($error['EDIT_PASSWORD'])) {
                    
                    $this->User->validationErrors["EDIT_PASSWORD"] = $error['EDIT_PASSWORD'];
                } else {
                    
                    $this->data['User']['PASSWORD'] = $this->Auth->password($this->data['User']['EDIT_PASSWORD']);
                }
            } else {
                
                unset($this->data['User']['PASSWORD']);
                unset($this->data['User']['EDIT_PASSWORD']);
            }
            
            if (empty($error)) {
                
                /*
                 * 検証BL 管理者チェック
                 */
                if ($this->__isAdmin($user["User"]["USR_ID"]) == false) {
                    $result = $this->User->User_Regist($this->data, null, true, false);
                } else {
                    $result = $this->User->User_Regist($this->data, true, true, false);
                }
                
                if ($result != false) {
                    
                    /*
                     * パスワードを変更した場合は2、メールアドレスのみの場合は1を返す custom_ajaxの処理で利用される
                     */
                    echo $this->data['User']['PASS_C'] ? 2 : 1;
                    exit();
                } else {
                    $errorStr = 'システムエラー';
                    $redirectOption = array(
                        'controller' => '/'
                    );
                    $this->Session->setFlash($errorStr, '');
                    $this->redirect($redirectOption);
                }
            }
            
            $pas = $this->data['User']['PASS_C'];
            $this->set('pass', $pas);
        } else {
            
            $this->data = $user;
            $this->set('pass', 0);
        }
        
        $withdrawal = $this->Configuration->find('first', array(
            'conditions' => array(
                'Configuration.NAME' => 'WITHDRAWAL'
            )
        ));
        
        $this->set('withdrawal', $withdrawal['Configuration']['VALUE']);
        $this->set('user', $user);
        $this->render('edit', false);
    }

    /**
     * 退会手続き
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function withdrawal()
    {
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        // POST&トークンチェック
        if ($this->checkPost()) {
            
            $userId = $user['User']['USR_ID'];
            $w = array(
                'Watch.USR_ID' => $userId
            );
            
            $this->Watch->create();
            $this->Watch->deleteAll($w, false, false);
            
            $this->Read->Read_Delete($userId);
            $r = array(
                'Read.USR_ID' => $userId
            );
            
            $this->Read->create();
            $this->Read->deleteAll($r, false, false);
            
            $this->Request->Delete_Request($userId, null);
            
            // メッセージ削除
            $this->Message->Delete_All($userId);
            
            // グループ参加情報を全て削除
            $this->Join->Delete_All($userId);
            
            // 友人関係を全て削除
            $this->Friend->Delete_All($userId);
            
            // ユーザグループ削除
            $this->Group->delete_my_group($userId);
            $this->Notice->Delete_Notice($userId);
            $this->Administrator->Delete_User($userId);
            $this->Timeline->Delete_All($userId, false, false);
            
            $this->Session->setFlash('退会が完了しました。<br />ご利用ありがとうございました。', '');
        }
        
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ユーザ招待");
        
        $result = $this->Group->find('all', array(
            'conditions' => array(
                'Group.USR_ID' => $user['User']['USR_ID'],
                'NOT' => array(
                    'Group.TYPE' => '2'
                )
            )
        ));
        
        $this->set('index_list', $result);
        $this->set('id', $user['User']['USR_ID']);
        $this->render('withdrawal', false);
    }

    /**
     * ユーザ招待
     *
     * @return boolean string
     * @access public
     * @author 作成者
     */
    public function invite()
    {
        $this->autoRender = false;
        $this->uses = null;
        
        // システム設定取得
        $_invite = $this->Configuration->find('first', array(
            'conditions' => array(
                'Configuration.NAME' => 'INVIETE'
            )
        ));
        $_general = $this->Configuration->find('first', array(
            'conditions' => array(
                'Configuration.NAME' => 'GENERAL'
            )
        ));
        $user = $this->Auth->user();
        
        // 権限チェック
        // 招待ありの場合はすべてのユーザが登録できる
        // 設定周りの定数化。すべて共通化できなくても有効・無効くらいは共通化するべき
        
        if ($_invite["Configuration"]["VALUE"] == 0) {
            $this->Permission->allowAllUser();
        } else {
            // 招待なしの場合は管理者のみがアクセスできる
            // 管理者の場合も招待リンクが消えている
            $this->Permission->allowAdmin();
        }
        
        if ($this->Permission->isDenied($user["User"]["USR_ID"])) {
            $this->redirect("/");
        }
        
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "ユーザ招待");
        
        // POST&トークンチェック
        if ($this->checkPost()) {
            $j = 0;
            $result = array();
            for ($i = 1; $i < 6; $i ++) {
                if ($this->data['User']["MAIL$i"] != null) {
                    $mail[$i] = $this->Administrator->Mail_Search($this->data['User']["MAIL$i"]);
                    if (! preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $this->data['User']["MAIL$i"])) {
                        $this->User->validationErrors["MAIL$i"] = "メールアドレス形式で入力してください";
                        $result[$i] = false;
                    } elseif (! $mail[$i]) {
                        $this->User->validationErrors["MAIL$i"] = "既に登録されているユーザです。";
                        $result[$i] = false;
                    } else {
                        if ($_invite['Configuration']['VALUE'] == 0 && $_general['Configuration']['VALUE'] == 1) {
                            // 招待ありで一般登録なしの場合はトークンを発行
                            $token = Security::hash(uniqid() . mt_rand());
                            $url = Router::url(array(
                                'controller' => 'users',
                                'action' => 'regist'
                            ), true) . "?token=" . $token;
                        } else {
                            $token = null;
                            $url = Router::url(array(
                                'controller' => 'users',
                                'action' => 'regist'
                            ), true);
                        }
                        $subject = "【抹茶SNS】" . $user['User']['NAME'] . "さんがあなたを抹茶SNSへ招待しています";
                        $content = array(
                            'USER_NAME' => $user['User']['NAME'],
                            'URL' => $url
                        );
                        if (Validation::email($this->data['User']["MAIL$i"])) {
                            if ($this->Common->send_mail_beta($this->data['User']["MAIL$i"], $subject, null, null, $content, 'invite', null, 0, 1)) {
                                $result[$i] = true;
                                if ($_invite['Configuration']['VALUE'] == 0 && $_general['Configuration']['VALUE'] == 1) {
                                    $data['TOKEN'] = $token;
                                    $data['MAIL'] = $this->data['User']["MAIL$i"];
                                    $data['TYPE'] = 5;
                                    $this->Request->create($data);
                                    $this->Request->save($data);
                                }
                            }
                        } else {
                            $result[$i] = false;
                        }
                    }
                } else {
                    $result[$i] = true;
                    $j ++;
                }
            }
            if ($result[1] == true && $result[2] == true && $result[3] == true && $result[4] == true && $result[5] == true) {
                if ($j != 5) {
                    return true;
                } else {
                    return "2";
                }
            } else {
                if ($result[1]) {
                    $this->data['User']["MAIL1"] = null;
                }
                if ($result[2]) {
                    $this->data['User']["MAIL2"] = null;
                }
                if ($result[3]) {
                    $this->data['User']["MAIL3"] = null;
                }
                if ($result[4]) {
                    $this->data['User']["MAIL4"] = null;
                }
                if ($result[5]) {
                    $this->data['User']["MAIL5"] = null;
                }
            }
        }
        $this->render('invite', false);
    }

    /**
     * ユーザのアクティブ化画面
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function active()
    {
        $this->set("main_title", "ユーザ登録");
        $this->set("title_text", "確認画面");
        $autoRender = false;
        /*
         * 認証ビジネスロジック 1.トークンの存在チェック(GETパラメータ) 2.トークンの存在チェック(DB)
         */
        if (! isset($_GET['token'])) {
            $this->Session->setFlash('不正な操作が行われました', '');
            $this->redirect('../');
        }
        $request = $this->Request->find('first', array(
            'conditions' => array(
                'Request.TOKEN' => $_GET['token']
            )
        ));
        if (empty($request)) {
            $this->Session->setFlash('無効なトークンです', '');
            $this->redirect('../');
        }
        /*
         * 有効期限内かを確認し、有効期限が切れていれば、有効期限切れ処理を行う
         */
        $lDate = strtotime($request['Request']['INSERT_DATE']);
        $nDate = strtotime(date("Y-m-d H:i:s"));
        $sDiff = $nDate - $lDate;
        $diff = $sDiff / (60 * 60 * 24);
        if ($diff > 1) {
            /*
             * 有効期限切れ処理を実行 1.ユーザ情報テーブルとリクエスト情報テーブルから削除 2.エラー表示 3.エラー処理実行
             */
            if ($this->Request->delete($request['Request']['REQ_ID']) && $this->User->delete($request['Request']['USR_ID'])) {
                $this->Session->setFlash('URLの期限が切れています。', '');
                $this->redirect('/');
            }
        }
        $params = array();
        foreach ($request['User'] as $key => $val) {
            $params['Administrator'][$key] = $val;
        }
        $params['Administrator']['STATUS'] = 1;
        if ($this->Administrator->User_Save($params, null)) {
            $request['Request']['TOKEN'] = null;
            $request['Request']['TYPE'] = 6;
            $this->Request->save($request['Request']);
        }
        $this->render('finish');
    }
}
