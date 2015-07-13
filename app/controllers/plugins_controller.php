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
 * プラグイン用のコントローラクラス
 *
 * @author 作成者
 */
class PluginsController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Plugin";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        'Plugin'
    );

    /**
     * 自動レンダリングをするかどうか指定
     *
     * @var boolean
     * @access public
     */
    public $autoLayout = true;

    /**
     * ページネーションの初期設定
     *
     * @var array
     * @access public
     */
    public $paginate = array(
        'page' => 1,
        'conditions' => array(),
        'sort' => '',
        'limit' => 30,
        'order' => 'Plugin.INSERT_DATE DESC',
        'recursive' => 0
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
     * プラグイン一覧
     *
     * プラグイン一覧を表示する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function index()
    {
        // 初期化
        $this->set("main_title", "プラグイン");
        $this->set("title_text", "プラグイン");
        
        $user = $this->Auth->user();
        
        $list = array();
        $list = $this->Plugin->find('all');
        
        $this->set('index_list', $list);
    }

    /**
     * プラグイン追加
     *
     * プラグインを追加する
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function add()
    {
        // 初期化
        $this->autoRender = false;
        $this->uses = null;
        
        $user = $this->Auth->user();
        
        $this->set("main_title", "プラグイン");
        $this->set("title_text", "プラグイン");
        
        if ($this->checkPost()) {
            $this->denyPrimaryKey("Plugin");
            if (($check = $this->Plugin->Save_Plugin($this->data['Plugin'])) === true) {
                
                $this->Session->setFlash('プラグインを追加しました。');
                
                return 'true';
                exit();
            } else {
                return 'アップロードに失敗しました。' . $check;
            }
        }
        
        $this->render('add', false);
    }

    /**
     * プラグインインストール
     *
     * プラグインをインストールする
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function install()
    {
        $this->autoRender = false;
        
        $id = $this->params['pass'][0];
        
        // セキュリティチェック
        if ($this->checkAjaxPost() && ! empty($id) && $this->Plugin->existsID($id)) {
            
            if ($id == null) {
                $this->Session->setFlash('インストールに失敗しました。', '');
                $this->redirect(array(
                    'controller' => 'plugins',
                    'action' => 'index'
                ));
            }
            
            $result = $this->Plugin->find('first', array(
                'conditions' => array(
                    'Plugin.PLU_ID' => $id
                )
            ));
            
            $file = APP . "plugins/tmp/" . $result['Plugin']['FILE_NAME'];
            
            if ($result['Plugin']['STATUS'] == 0) {
                $zip = new ZipArchive();
                $zip->open($file);
                $zip->extractTo(APP . "plugins");
                $zip->close();
                
                $params['PLU_ID'] = $id;
                $params['STATUS'] = 1;
                
                $this->Plugin->save($params);
                
                $output = shell_exec('chmod -R 777 ' . APP . 'plugins/');
                
                if ($result['Plugin']['DB'] != null) {
                    App::import('Core', 'File');
                    App::import('Model', 'ConnectionManager');
                    $db = ConnectionManager::getDataSource('default');
                    $this->Plugin->__executeSQLScript($db, APP . 'plugins/' . $result['Plugin']['NAME'] . "/setting/sql/init.sql");
                }
                
                $this->Session->setFlash('プラグインのインストールが完了しました。');
                
                // キャッシュの削除
                unlink(TMP . DS . 'cache/persistent/cake_core_default_ja');
                unlink(TMP . DS . 'cache/persistent/cake_core_dir_map');
                unlink(TMP . DS . 'cache/persistent/cake_core_file_map');
                unlink(TMP . DS . 'cache/persistent/cake_core_object_map');
                
                $this->redirect(array(
                    'controller' => 'plugins',
                    'action' => 'index'
                ));
            } elseif ($result['Plugin']['STATUS'] == 1) {
                
                $params['PLU_ID'] = $id;
                $params['STATUS'] = 2;
                $this->Plugin->save($params);
                $this->Session->setFlash('プラグインを利用停止しました。');
            } elseif ($result['Plugin']['STATUS'] == 2) {
                
                $params['PLU_ID'] = $id;
                $params['STATUS'] = 1;
                
                $this->Plugin->save($params);
                $this->Session->setFlash('プラグインの利用を開始しました。');
            }
        }
    }
}
