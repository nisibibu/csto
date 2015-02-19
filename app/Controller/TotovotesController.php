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
        //$league_info = $this->League->getLeagueInfo();   //J1の情報を取得
        //debug($league_info);
        //$this->League->getGoalRanking();
        
        /*TOTO投票率の取得*/
        $param = "?id=0698";
        $vote_result = $this->Toto->getTotoVoteDetail(TOTO_VOTE_YJ,$param);
        
        /*totoの投票率取得（テスト）*/
        //$t_result =  $this->Toto->getTotoVoteByYJ();
        //$this->setTotoOnlyVote($t_result);
        
        /*miniの取得（テスト）*/
        //$m_vote_result = $this->Toto->getMiniVoteByYJ();
        //debug($m_vote_result);
        //$this->setMiniVote($m_vote_result);
        
        /*GOAL3の取得（テスト）*/
        $g3_result = $this->Toto->getGoal3VoteByYJ();
        //debug($g3_result);
        //$this->setGoal3Vote($g3_result);
        
        /*最新回の取得テスト*/
        $db_name = "totovotes";
        $held_time =  $this->getRecent($db_name);
        //debug($heldtime);
        $result = $this->getNowTotoVoteInfo($held_time);
        debug($result);
    }
    
    /*現在（直近）のTotoの情報（全て）を取得*/
    protected function getNowTotoVoteInfo($held_time){
         App::uses('Totovote','Model');     
        
        //debug($vote_result);
        
        //モデルクラスのインスタンスを生成
        $vote = new Totovote('Totovote','totovotes');
        $result =  $vote->getVoteTotoRecent($held_time);
        
        return $result;
    }


    //Totoの投票率（のみ）を登録
    protected function setTotoOnlyVote($vote_result){
         App::uses('Totovote','Model');     
        
        //debug($vote_result);
        
        //モデルクラスのインスタンスを生成
        $vote = new Totovote('Totovote','totovotes');
        $vote->setTotoOnlyVoteDb($vote_result);
    }
    
    /*DBに登録されている開催回の一番古い数値を返す*/
    protected function  getOldest($db_name){
        App::uses('Totovote','Model');     
        
        //debug($vote_result);
        
        //モデルクラスのインスタンスを生成
        $vote = new Totovote('Totovote',$db_name);
        $result = $vote->getOldestTime();
        
        return $result;
    }


    /*開催回の最新回開催の数字を返す*/
    protected function getRecent($db_name){
        App::uses('Totovote','Model');     
        
        //モデルクラスのインスタンスを生成
        $vote = new Totovote('Totovote',$db_name);
        $result = $vote->getRecentTime();
        
        return $result;
    }


    //miniToto(A B)を登録
    protected function setMiniVote($vote_result){
        App::uses('Totovote','Model');
        
        //debug($vote_result);
        
        //モデルクラスのインスタンスを生成
        $vote = new Totovote('Totovote','minivotes');
        $vote->setMiniVoteDb($vote_result);
    }


    //totoGoal3を登録
    protected function setGoal3Vote($vote_result){
         App::uses('Totovote','Model');    
        
        //モデルクラスのインスタンスを生成
        $vote = new Totovote('Totovote','goal3votes');
        $vote->setGoal3VoteDb($vote_result);
    }
    
    /*toto投票率を登録
     *
     *      */
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