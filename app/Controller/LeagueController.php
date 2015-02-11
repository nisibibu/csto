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
        /*League情報を取得*/
        //$j_class = "j1";
        //$league_info_j1 = $this->League->getLeagueInfo($j_class);   //J1の情報を取得
        //$this->setLeagueInfo($league_info_j1, $j_class);;
        //J2のリーグ情報を登録
        //$j_class = "j2";
        
        //$league_info_j2 = $this->League->getLeagueInfo($j_class); 
        //$this->setLeagueInfo($league_info_j2, $j_class);
        
        /*ゴールランキングの取得*/
        $j_class = "j1";
        $param = "8";
        
        
        $goal_ranking_info = $this->League->getGoalRanking(GOAL_RANKING_J1,$param);
        //var_dump($goal_ranking_info);
        $this->setgoalRankingInfo($goal_ranking_info, $j_class);
        
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
        
    }
    
}

?>