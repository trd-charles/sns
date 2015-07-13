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
 * 環境設定用のコントローラクラス
 *
 * @author 作成者
 */
class ConfigurationsController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Configuration";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Configuration",
        "User"
    );

    /**
     * 使用するヘルパを指定
     *
     * @var array
     * @access public
     */
    public $helpers = array(
        'javascript'
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
        'Session',
        'Auth',
        'Cookie'
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
        parent::beforeFilter();
        if (! $this->Authority_Check()) {
            $this->Session->setFlash('管理者以外アクセスできません');
            $this->redirect("/homes");
        }
    }

    /**
     * 環境設定変更
     *
     * 環境設定を変更する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "環境設定変更");
        
        // サブミットが押された場合の処理
        if ($this->checkAjaxPost()) {
            $this->autoRender = false;
            $this->autoLayout = false;
            
            // データを保存
            if ($this->Configuration->save_conf($this->params['form'])) {
                $this->Session->setFlash('設定を変更しました');
                echo "true";
            }
        }
        
        $invite_v = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'INVIETE'
            )
        ));
        
        $apporoval_v = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'APPOROVAL'
            )
        ));
        
        $general_v = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'GENERAL'
            )
        ));
        
        $withdrawal_v = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'WITHDRAWAL'
            )
        ));
        
        $ngword_v = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'NGWORD'
            )
        ));
        
        $this->data['Configuration']['INVITE'] = $invite_v['Configuration']['VALUE'];
        $this->data['Configuration']['APPOROVAL'] = $apporoval_v['Configuration']['VALUE'];
        $this->data['Configuration']['GENERAL'] = $general_v['Configuration']['VALUE'];
        $this->data['Configuration']['WITHDRAWAL'] = $withdrawal_v['Configuration']['VALUE'];
        $this->data['Configuration']['NGWORD'] = $ngword_v['Configuration']['VALUE'];
        
        // 変数のセット
        $this->set("invite", Configure::read('INVITE'));
        $this->set("apporoval", Configure::read('APPOROVAL'));
        $this->set("general", Configure::read('GENERAL'));
        $this->set("withdrawal", Configure::read('WITHDRAWAL'));
    }

    /**
     * アクセス制限編集
     *
     * アクセス制限を編集する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function access()
    {
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "アクセス制限編集");
        
        if ($this->checkAjaxPost()) {
            $this->autoRender = false;
            $this->autoLayout = false;
            
            $mainte_flg_v = $this->Configuration->find('first', array(
                'conditions' => array(
                    'NAME' => 'MAINTE_FLG'
                )
            ));
            
            $iphost_v = $this->Configuration->find('first', array(
                'conditions' => array(
                    'NAME' => 'IPHOST'
                )
            ));
            
            if (empty($iphost_v)) {
                $iphost_v['Configuration']['NAME'] = 'IPHOST';
            }
            
            $mainte_flg_v['Configuration']['VALUE'] = $this->params['form']['MAINTE_FLG'];
            $iphost_v['Configuration']['VALUE'] = $this->params['form']['IPHOST'];
            
            $result = array();
            array_push($result, $mainte_flg_v, $iphost_v);
            if ($this->Configuration->saveAll($result)) {
                $this->Session->setFlash('設定を変更しました');
                echo "true";
            }
        }
        
        $mainte_flg = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'MAINTE_FLG'
            )
        ));
        
        $iphost = $this->Configuration->find('first', array(
            'conditions' => array(
                'NAME' => 'IPHOST'
            )
        ));
        
        $this->data['Configuration']['MAINTE_FLG'] = $mainte_flg['Configuration']['VALUE'];
        $this->data['Configuration']['IPHOST'] = $iphost['Configuration']['VALUE'];
        $this->set('mainte_flg', Configure::read('MAINTENANCE_FLAG'));
    }

    /**
     * メール設定
     *
     * メールを設定する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function mail()
    {
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "メール設定");
        
        $data_name = array(
            'FROM_NAME',
            'FROM_MAIL',
            'SMTP_STATUS',
            'SMTP_PROTOCOL',
            'SMTP_SECURITY',
            'SMTP_HOST',
            'SMTP_PORT',
            'SMTP_USER',
            'SMTP_PASS'
        );
        
        // ラジオボタン用に配列をセット
        $this->set('mail_radio', Configure::read('MAIL_RADIO'));
        
        // フォームのデータがセットされているた場合の処理
        if ($this->checkPost()) {
            
            // サブミットボタンの名前を取得→$btname
            $buttons = array(
                'CONFIRM',
                'BACK',
                'SAVE'
            );
            
            foreach ($buttons as $button) {
                if (array_key_exists($button, $this->params['form']) || array_key_exists($button . '_x', $this->params['form']) || array_key_exists($button . '_y', $this->params['form'])) {
                    $btname = $button;
                    break;
                }
            }
            
            // 編集画面から来たか、確認画面から来たか
            if ($btname == 'CONFIRM') {
                
                // 編集画面から来た場合
                // バリデーションチェック
                $errors = $this->Configuration->validate_mail($this->data['Configuration']);
                if (empty($errors)) {
                    
                    // 確認画面へデータを渡す(入力データ)
                    $this->Configuration->set($this->data);
                    $this->Session->setFlash('以下の内容でよろしいですか？');
                    $this->render('mail_confirm');
                } else {
                    
                    // 編集画面へ戻す（バリデーションエラー+入力データ)
                    // フォームデータの補完
                    foreach ($data_name as $d_name) {
                        
                        if (! array_key_exists($d_name, $this->data['Configuration'])) {
                            $data_v = $this->Configuration->find('first', array(
                                'conditions' => array(
                                    'NAME' => $d_name
                                )
                            ));
                            
                            $this->data['Configuration'][$d_name] = $data_v['Configuration']['VALUE'];
                        }
                    }
                    
                    // データをセット
                    $this->Configuration->set($this->data);
                    
                    $errormsg = '入力内容を確認してください。';
                    $errors_fn = array_keys($errors);
                    foreach ($errors as $error_fieldname => $error_message) {
                        $errormsg = $errormsg . "<br>・" . $error_message;
                    }
                    
                    $this->Session->setFlash($errormsg);
                    $this->render('mail');
                }
            } else {
                
                // 確認画面から来た場合
                // 保存ボタンの場合
                if ($btname == 'SAVE') {
                    
                    // データを保存
                    if ($this->Configuration->save_mail_conf($this->data, $data_name)) {
                        $this->Session->setFlash('設定を変更しました');
                    } else {
                        $this->Session->setFlash('設定を変更できませんでした(save_mail_confでエラー)');
                        print_r($this->data);
                    }
                    
                    $this->redirect('/configurations/mail');
                    // 戻るボタンの場合
                } else {
                    
                    // フォームデータの補完
                    foreach ($data_name as $d_name) {
                        if (! array_key_exists($d_name, $this->data['Configuration'])) {
                            $data_v = $this->Configuration->find('first', array(
                                'conditions' => array(
                                    'NAME' => $d_name
                                )
                            ));
                            $this->data['Configuration'][$d_name] = $data_v['Configuration']['VALUE'];
                        }
                    }
                    
                    // データをセット
                    $this->Configuration->set($this->data);
                    $this->render('mail');
                }
            }
        } else {
            
            // データベースから値を取得して、dataに渡す
            foreach ($data_name as $d_name) {
                
                if (empty($this->data['Configuration'][$d_name])) {
                    $data_v = $this->Configuration->find('first', array(
                        'conditions' => array(
                            'NAME' => $d_name
                        )
                    ));
                    
                    // もしデータベース上の送信者名、アドレスが空白であった場合は、AUTHORITYが0のユーザの名前、アドレスを表示させる。
                    if ($d_name == 'FROM_NAME') {
                        if ($data_v['Configuration']['VALUE'] == '') {
                            $admin_v = $this->User->find('first', array(
                                'conditions' => array(
                                    'AUTHORITY' => '0'
                                )
                            ));
                            $data_v['Configuration']['VALUE'] = $admin_v['User']['NAME'];
                        }
                    } else 
                        if ($d_name == 'FROM_MAIL') {
                            if ($data_v['Configuration']['VALUE'] == '') {
                                $admin_v = $this->User->find('first', array(
                                    'conditions' => array(
                                        'AUTHORITY' => '0'
                                    )
                                ));
                                $data_v['Configuration']['VALUE'] = $admin_v['User']['MAIL'];
                            }
                        }
                    
                    $this->data['Configuration'][$d_name] = $data_v['Configuration']['VALUE'];
                }
            }
        }
    }

    /**
     * メール設定
     *
     * メール送信を確認する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function mail_sendtest()
    {
        // 初期化
        $this->set("main_title", "管理者メニュー");
        $this->set("title_text", "メール設定");
        
        // フォームのデータがセットされているた場合の処理
        if ($this->checkPost()) {
            $to = $this->data['Configuration']['TO_MAIL'];
            
            switch ($this->data['Configuration']['MAIL_TEMPLATE']) {
                case 0:
                    $subject = 'メール送信確認メール';
                    $template = 'mail_sendtest';
                    break;
                default:
                    $subject = 'メール送信確認メール';
                    $template = 'mail_sendtest';
                    break;
            }
            ;
            
            if ($this->Common->send_mail_beta($to, $subject, null, null, null, $template, null, 0, 1)) {
                $this->Session->setFlash('送信に成功しました。');
            } else {
                $this->Session->setFlash('送信に失敗しました。');
            }
            
            $this->redirect('');
        }
    }
}
