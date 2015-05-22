<?php
App::uses('Folder','Utility');
App::uses('File', 'Utility');

//Goutteの読み込み
//require_once '../Vendor/goutte/goutte.phar';
use Goutte\Client;
App::uses('Component', 'Controller');

class LeagueController extends AppController{
    public $uses = array('POST','League');    //使用するモデルを宣言
     /*コンポーネントの指定*/
    public $components = array('League');
    
    /* index */
    public function index(){
        /*
            以下テスト用コード
         *          */
        
        //$leaguej1 = $this->League->getLeagueInfo("j1");
        //$this->setLeagueInfo($leaguej1,"j1");
        
        /*ゴールランキングの取得*/
        //$j_class = "j1";
        //$param = "8";
        //$goal_ranking_j1 = array();
        
        /**! J1のデータ確認（？）debug表示されない、var_dump表示確認可能 /**/
        $goal_ranking_j1 = $this->League->getGoalRanking(GOAL_RANKING_J1); //J1ゴールランキングの取得
        //debug($goal_ranking_j1);
        //$j_class = "j1";
        //$this->setgoalRankingInfo($goal_ranking_j1, $j_class);   //DBへ保存
        
        //$j_class = "j2";
//        $param = "03";
        //$goal_ranking_j2 = $this->League->getGoalRanking(GOAL_RANKING_J2); //J1ゴールランキングの取得
        //debug($goal_ranking_j2);
        //$this->setgoalRankingInfo($goal_ranking_j2, $j_class);
         
         
    }
    
    /* Jリーグの情報をスクレイピングで取得して、ＤＢに保存する
     * @param string $league
     * 
     * @return boolean $result
     */
    public function saveJLeagueInfo($league){
       /*League情報を取得*/
        $league_info = $this->League->getLeagueInfo($league);
        //debug($league_info);
        $result = $this->setLeagueInfo($league_info, $league); 
        return $result;
    }
    
    /* Jリーグのゴールランキングの情報をスクレイピングで取得して、ＤＢに保存する
     * @param string $league
     * 
     * @return boolean $result
     */
    public function saveJGoalRanking($league){
        
    }
    
    
    
    /*リーグ情報を登録*/
    public function setLeagueInfo($league_info , $j_class){
        App::uses('League','Model');     //モデルクラスにTeamTrendを指定
        
        //モデルクラスのインスタンスを生成
        $league = new League('League','league');
        $league->setLeagueDb($league_info, $j_class);
    }
    
    /*得点ランキングを登録*/
    public function setgoalRankingInfo($goal_ranking_info,$j_class){
        App::uses('League','Model');     //モデルクラスにTeamTrendを指定
        
        //モデルクラスのインスタンスを生成
        $league = new League('League','goalranking');
        $league->setGoalRankingDb($goal_ranking_info, $j_class);
    }
    
    
    /*期待値ランキングを登録*/
    public function setExpectation(){
        /*後で実装する*/
    }
    
}

?>