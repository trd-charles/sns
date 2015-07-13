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
class SearchesController extends AppController
{

    /**
     * コントローラの名前を指定
     *
     * @var String
     * @access public
     */
    public $name = "Search";

    /**
     * 使用するモデルのクラス名を配列で指定
     *
     * @var array
     * @access public
     */
    public $uses = array(
        "Home",
        "Timeline",
        'Storage',
        'Group',
        'Read',
        'Watch',
        'Friend',
        'Join',
        'Notice',
        'User',
        'Search'
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
        'Permission',
        'RequestHandler'
    );

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
        'limit' => 20,
        'order' => 'Timeline.LAST_DATE DESC',
        'recursive' => 0
    );

    /**
     * 検索キーワード
     *
     * @var string
     */
    private $inputKeyword = null;

    /**
     * 検索条件
     *
     * @var array
     */
    private $conditions = array();

    private $executionStatus = false;

    /**
     * 検索するID
     *
     * @var number
     */
    private $searchStatus = 0;

    /**
     * 検索カテゴリ
     *
     * @var string
     */
    private $searchCategory = null;

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
        
        if (isset($this->data["Home"]["KEYWORD"])) {
            $this->inputKeyword = $this->data["Home"]["KEYWORD"];
        }
        
        if (isset($this->data['Search']['P_KEYWORD'])) {
            $this->inputKeyword = $this->data['Search']['P_KEYWORD'];
        }
        
        if (isset($this->data['Home']['FILTER'])) {
            
            $filter = explode(".", $this->data['Home']['FILTER']);
            $this->searchCategory = $filter[0];
            $this->searchStatus = $filter[1];
        }
        
        if (isset($this->data['Search']['SEARCH_STATUS'])) {
            $this->searchStatus = $this->data['Search']['SEARCH_STATUS'];
        }
        
        if (isset($this->data['Search']['SEARCH_CATEGORY'])) {
            $this->searchCategory = $this->data['Search']['SEARCH_CATEGORY'];
        }
        
        if (mb_strlen($this->inputKeyword) < search::SEARCH_STRING_MIN_COUNT) {
            
            $err_comment = "2文字以上で検索してください。";
            $this->set("err_comment", $err_comment);
        } else {
            
            // SQLの条件文を取得
            $this->conditions = $this->__judConditions();
            
            $this->paginate['conditions'] = $this->conditions;
            
            // ReadMore用のキーワードをセット
            if (isset($this->data['Search']['P_KEYWORD'])) {
                $this->set('keyword', $this->inputKeyword);
            }
            
            // 検索ステータスをセット
            if (isset($this->searchStatus)) {
                
                $this->set('searchStatus', $this->searchStatus);
            }
            
            // 検索カテゴリをセット
            if (isset($this->searchCategory)) {
                $this->set('searchCategory', $this->searchCategory);
            }
            
            $this->executionStatus = true;
        }
    }

    /**
     * SQLの条件を返す
     *
     * @return array
     * @access private
     */
    private function __judConditions()
    {
        $conditions = array();
        
        // 追加条件初期値
        $andCondition = array();
        
        /*
         * 条件追加：検索キーワード
         */
        if (isset($this->data['Home']['KEYWORD'])) {
            $keyword = $this->data['Home']['KEYWORD'];
        }
        
        /*
         * 検索条件変更
         */
        if ($this->searchCategory == search::SEARCH_STATUS_FORROW_USER) {
            
            /*
             * 条件追加：フォローしているユーザ
             */
            $andCondition += $this->Search->addConditionFollowUser($this->Auth->User('USR_ID'), $this->Auth->User('AUTHORITY'));
            $andCondition += array(
                'Timeline.ACT_ID !=' => 3
            );
        } elseif ($this->searchCategory == search::SEARCH_STATUS_JOIN_GROUP) {
            
            /*
             * 認証BL：管理者以外がそのグループに参加しているか
             */
            if ($this->Auth->User('AUTHORITY') != user::AUTHORITY_TRUE) {
                $result = $this->Join->isJoinFromGroupIdAndUserId($this->searchStatus, $this->Auth->User('USR_ID'));
                if ($result == false) {
                    $errorStr = '不正な処理です';
                    $redirectOption = array(
                        'controller' => '/'
                    );
                    $this->Session->setFlash($errorStr, '');
                    $this->redirect($redirectOption);
                }
            }
            
            /*
             * 条件追加：選択したグループ
             */
            $andCondition += $this->Search->addConditionSelectGroup($this->searchStatus);
            $andCondition += array(
                'Timeline.ACT_ID' => timeline::ACT_ID_GROUP
            );
        } else {
            
            $errorStr = '不正な処理です';
            $redirectOption = array(
                'controller' => '/'
            );
            $this->Session->setFlash($errorStr, '');
            $this->redirect($redirectOption);
        }
        
        $commentCondition = $andCondition;
        $andCondition = array_merge($andCondition, (array) $this->Search->addKeyword($this->inputKeyword));
        
        // SQL条件文結合処理
        $and = array(
            'AND' => $andCondition
        );
        
        // 検索条件に合っているコメントを検索
        $searchList = $this->Timeline->find('all', array(
            'fields' => 'Timeline.VAL_ID',
            'conditions' => array(
                'Timeline.ACT_ID' => Timeline::ACT_ID_COMMENT,
                $this->Search->addKeyword($this->inputKeyword)
            )
        ));
        
        // コメントの発言元をリスト化
        $timelineList = array();
        foreach ($searchList as $key => $val) {
            $timelineList[$key] = $searchList[$key]['Timeline']['VAL_ID'];
        }
        
        // SQL条件文再結合処理
        $conditions = array(
            'or' => array(
                '1' => array(
                    'ACT_ID !=' => timeline::ACT_ID_COMMENT,
                    $and
                ),
                '2' => array(
                    'TML_ID' => $timelineList,
                    $commentCondition
                )
            )
        );
        
        return $conditions;
    }

    public function index()
    {
        $this->set("main_title", "検索結果");
        $this->set("title_text", "検索結果");
        
        /*
         * 認証BL トークンがない場合は不正処理とし強制ログアウトする
         */
        $this->isCorrectToken($this->data['Security']['token']);
        
        $list = array();
        $post_num = 0;
        $com_num = 0;
        
        if ($this->executionStatus == true) {
            
            $list = $this->paginate('Timeline');
            
            // コメントを付け、コメント数を格納する
            $com_num = $this->Timeline->Comment_Search($list);
            
            if (isset($list[0])) {
                $this->set("first", $list[0]['Timeline']['TML_ID']);
                $this->set("lastid", $list[count($list) - 1]['Timeline']['TML_ID']);
            } else {
                $this->set("first", 0);
            }
            
            // お気に入りとウォッチリストの取得
            $user = $this->Auth->User();
            $this->Read->Read_Search($list, $user);
            $this->Watch->Watch_Search($list, $user);
            
            // ReadMore用ページカウント
            $this->__readMorePageCount();
        }
        
        // 検索文字列の出力
        $this->set("keyword", $this->data['Home']['KEYWORD']);
        $this->set("list", $list);
        $this->set("match_post", $post_num);
        $this->set("match_comment", $com_num);
        
        // ReadMoreの設定
        $this->set("date_frag", isset($this->params['pass'][0]) && $this->params['pass'][0] == 'calender' ? 0 : 1);
        $this->set("m_class", 'Home');
        
        // コメント数
        $this->set("match_comment", $com_num);
    }

    /**
     * Ajax通信後に実行されるメソッド（Read More用）
     *
     * @return void
     * @access public
     *        
     */
    public function getMessages()
    {
        $this->autoRender = false;
        $this->uses = null;
        
        $list = array();
        
        // ページネーション
        $list = $this->paginate('Timeline');
        
        // コメントを付ける
        $this->Timeline->Comment_Search($list);
        
        // お気に入りとウォッチリストの取得
        $user = $this->Auth->user();
        $this->Read->Read_Search($list, $user);
        $this->Watch->Watch_Search($list, $user);
        
        $this->set("list", $list);
        
        // PageMoreの処理
        $this->__readMorePageCount();
        
        $this->render('../elements/timeline/searchResult_timeline', false);
    }

    /**
     * 現在のページ数と総ページ数を計算し、ReadMoreを行うか判定し結果をビューに渡す
     *
     * @return void
     * @access private
     */
    private function __readMorePageCount()
    {
        $thisPage = $this->params['paging']['Timeline']['page'];
        $maxPage = $this->params['paging']['Timeline']['pageCount'];
        
        if ($maxPage == $thisPage) {
            
            $result = false;
        } elseif ($maxPage > $thisPage) {
            
            $result = $thisPage + 1;
        } else {
            
            $result = false;
        }
        
        if ($result != false) {
            
            $this->set("readMore", $result);
        }
    }
}
