<?php

/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
class CommonComponent extends Object
{

    var $components = array(
        'Qdmail'
    );

    function startup(& $controller)
    {
        $this->_controller = $controller;
    }

    /**
     * メール送信関連の処理
     *
     * @param $to メール送信先アドレス            
     * @param $subject メールのサブジェクト            
     * @param $body メールの本文            
     * @param $from 送信元アドレス            
     * @param $contents テンプレート用のデータ            
     * @param $template テンプレート名            
     * @param $layout レイアウト名            
     * @param $mail_form メール形式(null
     *            or 0：text形式, 1:html形式, 2:両方)
     * @param $smtp_enable smtpを使用させるかどうか            
     *
     * @return bool メールが送信できたかどうか
     */
    function send_mail_beta($to = null, $subject, $body = null, $from = null, $content = null, $template = null, $layout = null, $mail_form = null, $smtp_enable = null)
    {
        App::import('Model', 'User');
        $User = new User();
        
        $this->Qdmail = new QdmailComponent();
        App::import('Controller', 'App');
        $dummy = new AppController();
        $this->Qdmail->startup($dummy);
        
        if ($smtp_enable == 1) {
            // Configurationから設定データを取得
            App::import('Model', 'Configuration');
            $Configuration = new Configuration();
            $smtp_status = $Configuration->find('first', array(
                'conditions' => array(
                    'NAME' => 'SMTP_STATUS'
                )
            ));
            
            // SMTPサーバーを使用する場合
            if ($smtp_status['Configurations']['VALUE'] == 1) {
                $this->Qdmail->smtp(true);
                $protocol = Configure::read('MailProtocolCode');
                
                $smtp_security = $Configuration->find('first', array(
                    'conditions' => array(
                        'NAME' => 'SMTP_SECURITY'
                    )
                ));
                $smtp_host = $Configuration->find('first', array(
                    'conditions' => array(
                        'NAME' => 'SMTP_HOST'
                    )
                ));
                if ($smtp_security['Configurations']['VALUE'] == 1) {
                    $smtp_host['Configurations']['VALUE'] = "ssl://" . $smtp_host['Configurations']['VALUE'];
                } else 
                    if ($smtp_security['Configurations']['VALUE'] == 2) {
                        $smtp_host['Configurations']['VALUE'] = "tls://" . $smtp_host['Configurations']['VALUE'];
                    }
                $smtp_protocol = $Configuration->find('first', array(
                    'conditions' => array(
                        'NAME' => 'SMTP_PROTOCOL'
                    )
                ));
                $smtp_port = $Configuration->find('first', array(
                    'conditions' => array(
                        'NAME' => 'SMTP_PORT'
                    )
                ));
                $smtp_from = $Configuration->find('first', array(
                    'conditions' => array(
                        'NAME' => 'SMTP_FROM'
                    )
                ));
                $sender_name = $Configuration->find('first', array(
                    'conditions' => array(
                        'NAME' => 'FROM_NAME'
                    )
                ));
                $sender_mail = $Configuration->find('first', array(
                    'conditions' => array(
                        'NAME' => 'FROM_MAIL'
                    )
                ));
                if ($smtp_protocol['Configurations']['VALUE'] == 0) {
                    $smtpparam = array(
                        'host' => $smtp_host['Configurations']['VALUE'],
                        'port' => $smtp_port['Configurations']['VALUE'],
                        'from' => $sender_mail['Configurations']['VALUE'],
                        'protocol' => $protocol[$smtp_protocol['Configurations']['VALUE']]
                    );
                } else 
                    if ($smtp_protocol['Configurations']['VALUE'] == 1) {
                        $smtp_user = $Configuration->find('first', array(
                            'conditions' => array(
                                'NAME' => 'SMTP_USER'
                            )
                        ));
                        $smtp_pass = $Configuration->find('first', array(
                            'conditions' => array(
                                'NAME' => 'SMTP_PASS'
                            )
                        ));
                        $smtpparam = array(
                            'host' => $smtp_host['Configurations']['VALUE'],
                            'port' => $smtp_port['Configurations']['VALUE'],
                            'from' => $sender_mail['Configurations']['VALUE'],
                            'protocol' => $protocol[$smtp_protocol['Configurations']['VALUE']],
                            'user' => $smtp_user['Configurations']['VALUE'],
                            'pass' => $smtp_pass['Configurations']['VALUE']
                        );
                    }
                $this->Qdmail->smtpServer($smtpparam);
                $from = array(
                    $sender_mail['Configurations']['VALUE'],
                    $sender_name['Configurations']['VALUE']
                );
            } else {
                App::import('Model', 'User');
                $User = new User();
                
                $admin = $User->find('first', array(
                    'conditions' => array(
                        'AUTHORITY' => 0
                    )
                ));
                if ($from == null) {
                    $sender = $User->find('first', array(
                        'conditions' => array(
                            'AUTHORITY' => 0
                        )
                    ));
                    $from = array(
                        $sender['User']['MAIL'],
                        $sender['User']['NAME']
                    );
                }
                // メールを送信するユーザーリストの取得
                $this->Qdmail->mtaOption("-f " . $admin['User']['MAIL']);
            }
        } else {
            $admin = $User->find('first', array(
                'conditions' => array(
                    'AUTHORITY' => 0
                )
            ));
            if ($from == null) {
                $sender = $User->find('first', array(
                    'conditions' => array(
                        'AUTHORITY' => 0
                    )
                ));
                $from = array(
                    $sender['User']['MAIL'],
                    $sender['User']['NAME']
                );
            }
            // メールを送信するユーザーリストの取得
            $this->Qdmail->mtaOption("-f " . $admin['User']['MAIL']);
        }
        // Qdmailの設定
        $this->Qdmail->subject($subject);
        $this->Qdmail->from($from);
        $this->Qdmail->kana(true);
        // テンプレートを使用する場合
        if ($template) {
            if ($content == null) {
                $content = array();
            }
            $webroot = $Configuration->find('first', array(
                'conditions' => array(
                    'NAME' => 'WEBROOT'
                )
            ));
            // webrootを配列に入れる
            $content = array_merge($content, array(
                'WEBROOT' => $webroot['Configurations']['VALUE']
            ));
            if ($to == null) {
                $to_u = $User->find('all', array(
                    'conditions' => array(
                        'AUTHORITY' => 0
                    ),
                    'fields' => array(
                        'MAIL'
                    )
                ));
                foreach ($to_u as $to) {
                    $this->Qdmail->to($to['User']['MAIL'], null, true);
                }
            } else {
                $this->Qdmail->to($to);
            }
            
            if ($mail_form == 1) {
                $this->Qdmail->cakeHtml($content, $template, $layout);
            } else 
                if ($mail_form == 2) {
                    $this->Qdmail->cakeHtml($content, $template, $layout);
                    $this->Qdmail->cakeText($content, $template, $layout);
                } else {
                    $this->Qdmail->cakeText($content, $template, $layout);
                }
            
            $this->Qdmail->errorDisplay(false);
            $this->Qdmail->smtpObject()->error_display = false;
            return $this->Qdmail->send();
            
            // テンプレートを使用しない場合
        } else {
            if ($to == null) {
                $to_u = $User->find('all', array(
                    'conditions' => array(
                        'AUTHORITY' => 0
                    ),
                    'fields' => array(
                        'MAIL'
                    )
                ));
                foreach ($to_u as $to) {
                    $this->Qdmail->to($to['User']['MAIL'], null, true);
                }
            } else {
                $this->Qdmail->to($to);
            }
            
            if ($mail_form == 1) {
                $this->Qdmail->html($body);
            } else {
                $this->Qdmail->text($body);
            }
            $this->Qdmail->errorDisplay(false);
            $this->Qdmail->smtpObject()->error_display = false;
            return $this->Qdmail->send();
        }
    }
}
