<?php
App::uses('Folder','Utility');
App::uses('File', 'Utility');

//Goutteの読み込み
//require_once '../Vendor/goutte/goutte.phar';
use Goutte\Client;
App::uses('Component', 'Controller');

class TotovotesController extends AppController{
    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
     /*コンポーネントの指定*/
    public $components = array('Twitter','Toto','Rss','TeamTrend','TotoResult','League');
    
    /* index */
    public function index(){
        //今回のtotoマッチングと投票率の取得
        //$url_vote = "http://www.totoone.jp/blog/datawatch/index.php?id=248";
        //$toto_vote = $this->Toto->getTotoVote($url_vote);
        //debug($toto_vote);
        
        //リンクを全て表示（toto投票率取得元）
        
        //$past_vote_url = "http://www.totoone.jp/blog/datawatch/archives.php?off=100";
        //$links = $this->Toto->getPastTotoVote($past_vote_url);
        //debug($links);
        
        //$this->setTotoVote($toto_vote);
        
        //get 1p toto vote
        //$toto_vote = $this->Toto->getTotoVote($links[2]['link_loc']);
        
//        foreach ($links as $var){
//                $toto_vote =$this->Toto->getTotoVote($var['link_loc']);
//                
//                $this->setTotoVote($toto_vote);
//        }
        /*チームの傾向情報を取得*/
       // $this->TeamTrend->getTeamTrendGoal();
        
        /*Totoの結果を取得*/
        //$url = "http://www.totoone.jp/kekka/index.php?id=248";
        //$this->TotoResult->getTotoResult($url);

        /*League情報を取得*/
        $league_info = $this->League->getJ1LeagueInfo();   //J1の情報を取得
        debug($league_info);
        //$this->League->getGoalRanking();
    }
    
    
    //toto投票率を登録
    public function setTotoVote($toto_vote){
        //debug($toto_vote);
        if($toto_vote[0]['held_time'] === 0){
            return;
        }
        //Totoコンポーネントを使用してmodelクラスのsaveメソッド呼び出し
        $this->Totovote->setTotoVoteDb($toto_vote);
    }
}

?>