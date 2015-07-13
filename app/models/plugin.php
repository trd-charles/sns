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
 * プラグイン用のモデルクラス
 *
 * @author 作成者名
 *        
 */
class Plugin extends AppModel
{

    /**
     * モデルの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = 'Plugin';

    /**
     * テーブルの名前を指定
     *
     * @var String
     * @access public
     */
    public $useTable = 'T_PLUGIN';

    /**
     * プライマリーキーのカラムを指定
     *
     * @var String
     * @access public
     */
    public $primaryKey = 'PLU_ID';

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
     * プラグイン保存
     *
     * @param unknown $_data            
     * @return boolean
     * @access public
     * @author 作成者
     */
    public function Save_Plugin($_data)
    {
        // トランザクションの開始
        $dataSource = $this->getDataSource();
        $dataSource->begin($this);
        
        if ($_data['FILE'] != null) {
            $maxsize = Configure::read('FILE_ONE_MAX');
            
            // ファイルがアップロードされたかどうかの確認
            if ($_data['FILE']['size'] > 0) {
                if (is_uploaded_file($_data['FILE']['tmp_name'])) {
                    if ($_data['FILE']['size'] < $maxsize) {
                        $extension = strtolower(pathinfo($_data['FILE']['name'], PATHINFO_EXTENSION));
                        
                        if ($extension == 'zip') {
                            if (move_uploaded_file($_data['FILE']['tmp_name'], APP . "plugins/tmp/" . $_data['FILE']['name'])) {
                                $file = substr($_data['FILE']['name'], 0, strpos($_data['FILE']['name'], '.zip'));
                                $zip = new ZipArchive();
                                App::import('Xml');
                                $xml = new Xml();
                                
                                if ($zip->open(APP . "plugins/tmp/" . $_data['FILE']['name']) === TRUE) {
                                    $fp = $zip->getStream($file . '/setting/config.xml');
                                    
                                    if (! $fp) {
                                        return false;
                                    }
                                    $contents = null;
                                    
                                    while (! feof($fp)) {
                                        $contents .= fread($fp, 2);
                                    }
                                    
                                    $xml->load($contents);
                                    $txt = Set::reverse($xml);
                                    $zip->close();
                                }
                                
                                $params = array();
                                $params['NAME'] = $txt['Plugins']["name"];
                                $params['DISP_NAME'] = $txt['Plugins']["name"];
                                $params['FILE_NAME'] = $_data['FILE']['name'];
                                
                                if (isset($txt['Plugins']["description"])) {
                                    $params['DESCRIPTION'] = $txt['Plugins']["description"];
                                }
                                
                                if (isset($txt['Plugins']["author"])) {
                                    $params['AUTHOR'] = $txt['Plugins']["author"];
                                }
                                
                                if (isset($txt['Plugins']["url"])) {
                                    $params['URL'] = $txt['Plugins']["url"];
                                }
                                
                                if (isset($txt['Plugins']["db"]) && $txt['Plugins']["db"] != 0) {
                                    $params['DB'] = $txt['Plugins']["db"];
                                } else {
                                    $params['DB'] = null;
                                }
                                
                                if (isset($txt['Plugins']["not_stop"])) {
                                    $params['NOT_STOP'] = $txt['Plugins']["not_stop"];
                                } else {
                                    $params['NOT_STOP'] = 0;
                                }
                                
                                $params['STATUS'] = 0;
                                $params['INSERT_DATE'] = date("Y-m-d H:i:s");
                                $params['LAST_UPDATE'] = date("Y-m-d H:i:s");
                                $params['DEL_FLG'] = 0;
                                
                                if ($this->save($params)) {
                                    // 成功
                                    $dataSource->commit($this);
                                    return true;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return false;
    }

    /**
     * URLの取得
     *
     * @param unknown $controller            
     * @param unknown $model            
     * @param unknown $view            
     * @return void
     * @access public
     * @author 作成者
     */
    public function GetUrl(&$controller, &$model, &$view)
    {
        $result = $this->find('all', array(
            'conditions' => array(
                'STATUS' => '1'
            ),
            'fields' => 'NAME'
        ));
        
        foreach ($result as $key => $val) {
            $controller[$key] = APP . "plugins/" . $val['Plugin']['NAME'] . "/controllers/";
            $model[$key] = APP . "plugins/" . $val['Plugin']['NAME'] . "/models/";
            $view[$key] = APP . "plugins/" . $val['Plugin']['NAME'] . "/views/";
        }
    }

    /**
     * NotUse
     *
     * @return void
     * @access public
     * @author 作成者
     */
    public function NotUse()
    {
        $result = $this->find('all', array(
            'conditions' => array(
                'STATUS' => '2'
            ),
            'fields' => 'NAME'
        ));
        
        foreach ($result as $key => $val) {
            Router::connect('/' . $val['Plugin']['NAME'] . "/*", array(
                'controller' => $val['Plugin']['NAME'],
                'action' => 'index',
                'plugin' => null
            ));
        }
    }

    /**
     * Execute SQL file
     *
     * @link http://cakebaker.42dh.com/2007/04/16/writing-an-installer-for-your-cakephp-application/
     * @param object $db            
     * @param string $fileName            
     * @return void
     * @access private
     * @author 作成者
     */
    private function __executeSQLScript($db, $fileName)
    {
        $statements = file_get_contents($fileName);
        $statements = explode(';', $statements);
        
        $prefix = $db->config["prefix"];
        
        foreach ($statements as $statement) {
            if (trim($statement) != '') {
                // プレフィックス用
                $pattern = array(
                    '/(DROP TABLE IF EXISTS `)([a-z_]+)(`)/i',
                    '/(CREATE TABLE IF NOT EXISTS `)([a-z_]+)(`)/i',
                    '/(INSERT INTO `)([a-z_]+)(`)/i'
                );
                
                $statement = preg_replace($pattern, '$1' . $prefix . '$2$3', $statement);
                $db->query($statement);
            }
        }
    }
}
