<?php
App::uses('Folder','Utility');
App::uses('File', 'Utility');
App::uses('AppController','Controller');

//Goutteの読み込み
//require_once '../Vendor/goutte/goutte.phar';
use Goutte\Client;
App::uses('Component', 'Controller');

/*shellからモデルを使用する*/
//$toto_vote_model = ClassRegistry::init('Vote');

class TotovotesController extends AppController{
    public $uses = array('POST','Live','Totovote',"Minivote","Goal3vote");    //使用するモデルを宣言
     /*コンポーネントの指定*/
    public $components = array('Twitter','Toto',"TotoVotes",'Rss','TeamTrend','TotoResult','League');
    
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
        
        /*開催しているくじのみ登録処理*/
        $held_kind = $this->getHeldKind();
        //debug($held_kind);

        
        /*最新回の取得テスト*/
        //$db_name = "goal3votes";    //goal3のテスト
        //$held_time =  $this->getRecent($db_name);
        //debug($heldtime);
        //$result = $this->getNowTotoVoteInfo($db_name);
        //debug($result);
        
        
        $recent_held = $this->getRecentHeld();
        $recent_held = $recent_held;
        //debug($recent_held);
        
        $match_info = $this->TotoVotes->getTotoMatchInfo(TOTO_OFFICIAL,$recent_held);    //toto開催回（自体の情報）の取得
        
        //debug($match_info);
        //$this->setTotoMatch($match_info);
        $toto_info = $this->getRecentTotoinfo();
        
    }
    
    /*最新回の情報を取得（totovotes minivotes)*/
    public function getRecentTotoinfo(){
        $toto_vote = new Totovote();
        $recent_time = $toto_vote->getRecentTime();
        $result = $toto_vote->getVoteTotoRecent($recent_time);
        debug($result);
    }
    
    /*DBから最新回を取得*/
    public function getRecentHeld(){
        /*開催回情報の取得*/
        $vote = new Totovote('Totovote',"totovotes");
        $recent_held = (int)$vote->getRecentTime();
        
        return $recent_held;
    }
    
    /*直近回の開催くじの種類を取得*/
    public function getHeldKind(){
        $vote_kind = $this->Toto->getTotoVoteDetail(TOTO_VOTE_YJ);
        //debug($vote_kind);
        
        $held_kind = array();   //返却用
        /*開催くじ種類を連想配列に整形*/
        $pattern = "#(mini toto-A)|(mini toto-B)|(totoGOAL3)|(totoGOAL2)|(toto)#";
        foreach($vote_kind as $var){
            preg_match($pattern, $var,$m);
            if(isset($m[0])){
                $held_kind[$m[0]] = $var;
            }
            
        }
        
        
        return $held_kind;
    }
    
    /*Yahoo よりtoto投票率を取得*/
    public function getTotoVoteByY(){
        /*totoの投票率取得（テスト）*/
        $t_result =  $this->Toto->getTotoVoteByYJ();
        /*取得出来たら保存処理*/
        //$this->setTotoOnlyVote($t_result);
    }
    
    /*Yahoo よりmini投票率を取得*/
    public function getMiniVoteByY(){
         /*miniの取得（テスト）*/
        $m_vote_result = $this->Toto->getMiniVoteByYJ();
        debug($m_vote_result);
        //$this->setMiniVote($m_vote_result);
    }
    
    /*Yahoo よりgoal3投票率を取得*/
    public function getGoal3VoteByY(){
        /*GOAL3の取得（テスト）*/
        //$g3_result = $this->Toto->getGoal3VoteByYJ();
        //debug($g3_result);
        //$this->setGoal3Vote($g3_result);
    }
    
    
    
    /*今回のtotoの試合情報をセット*/
    public function setTotoMatch($match_info){
        App::uses('Totovote','Model');     
        App::uses('Minivote','Model');
        App::uses('Goal3vote','Model');
        
        /* 取得している情報をそれぞれ登録処理
         * くじの情報の存在チェック
         * */
        if(array_key_exists("toto", $match_info)){
            //totoの試合情報の登録
             //モデルクラスのインスタンスを生成
            $toto = new Totovote();
            $toto_status = array();
            $toto_status['held_time'] = $match_info['held_time'];
            $toto_status['toto'] = $match_info["toto"];
            $toto->setTotoMatchDb($toto_status);
        }
        if(array_key_exists("mini-A", $match_info)){
            //mini-Aの試合情報の登録
            //var_dump("mini-Aの登録処理");
            //モデルクラスのインスタンスを生成
            $mini_a = new Minivote();
            $mini_a_status = array();
            $mini_a_status['held_time'] = $match_info['held_time'];
            $mini_a_status['mini-A'] = $match_info["mini-A"];
            //debug($mini_a_status);
            $mini_a->setMiniMatchDb($mini_a_status);
        }
        if(array_key_exists("mini-B", $match_info)){
            //mini-Bの試合情報の登録
            //モデルクラスのインスタンスを生成
            $mini_b = new Minivote();
            $mini_b_status = array();
            $mini_b_status['held_time'] = $match_info['held_time'];
            $mini_b_status['mini-B'] = $match_info["mini-B"];
            //debug($status);
            $mini_b->setMiniMatchDb($mini_b_status);
        }
        if(array_key_exists("goal", $match_info)){
            //goal3(2)の試合情報の登録
            //var_dump("goal3の登録処理");
            //モデルクラスのインスタンスを生成
            $status = array();
            $status['held_time'] = $match_info['held_time'];
            $status['goal'] = $match_info["goal"];
            //debug($status);
            $goal3 = new Goal3vote();
            $goal3->setGoal3MatchDb($status);
        }
        
//        $held = $this->getRecentAll();  //最新回の取得
//        //debug($held);
//       
//        //$match->setTotoMatchAllDb($match_info);   //試合情報の登録
//        //$match->isRecentlyset("minivotes", $match_info['held_time']);
    }
    
    /*今回（最新回）の取得
     * totovotesテーブル
     * minivotesテーブル
     * goal3votesテーブル
     * から最新回を取得して、今回の回を特定
     * 
     *      */
    public function getRecentAll(){
        App::uses('Totovote','Model');
        App::uses('Minivote','Model');
        App::uses('Goal3vote','Model');
        
        $held_time_toto;
        $held_time_mini;
        $held_time_goal;
        
        //モデルクラスのインスタンスを生成
        $toto = new Totovote();
        $held_time_toto =  $toto->getRecentTime();
        
        $mini = new Minivote();
        $held_time_mini = $mini->getRecentTime();
        
        $goal = new Goal3vote();
        $held_time_goal = $goal->getRecentTime();
        
//        debug($held_time_toto);
//        debug($held_time_mini);
//        debug($held_time_goal);
        
        
        $result = $held_time_toto;
        if($result < $held_time_mini){
            $result = $held_time_mini;
        }elseif ($result < $held_time_goal) {
            $result = $held_time_goal;
        }
        
        
        return $result;
        
    }
    
    
    /*現在（直近）のTotoの情報（全て）を取得
     * $db_name 対象テーブル名
     * totovotes toto
     * minivotes miniA and miniB
     * goal3votes goal3 adn goal2
     *      */
    protected function getNowTotoVoteInfo($db_name){
        App::uses('Totovote','Model');     
        
        $held_time =  $this->getRecent($db_name);
        
        //モデルクラスのインスタンスを生成
        $vote = new Totovote('Totovote',$db_name);
        $result =  $vote->getVoteTotoRecent($held_time);
        
        return $result;
    }


    //Totoの投票率（のみ）を登録
    public function setTotoOnlyVote($vote_result){
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