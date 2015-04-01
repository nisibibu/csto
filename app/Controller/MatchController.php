<?php
App::uses('Folder','Utility');
App::uses('File', 'Utility');
App::uses('AppController','Controller');

use Goutte\Client;
App::uses('Component', 'Controller');

/*shellからモデルを使用する*/
//$match_model = ClassRegistry::init('Match');

class MatchController extends AppController{
    //var $name = 'Match';                    //コントローラー名の指定
    public $uses = array('Post','Match');    //使用するモデルを宣言
    public $components = array('Matches');     //コンポーネントの指定
    public $helpers = array("Form");         //ヘルパーの指定
    
    public $scaffold;
    
    /* index */
    public function index(){
        
        /*チームのリスト取得*/
        $team_list = $this->getTeamList();
        
        /*チームリストをビューへ渡す*/
        $this->set('team_list',$team_list);
        
        /**/
        if(!empty($form_data = $this->show())){
            /*チームの試合結果を指定した分だけ最新件数より取得する*/
            $team = $form_data['team'];
            $item = $form_data['count'];
            $match_result = $this->getMatchByTeam($team, $item);
            //var_dump($match_result);
            $this->set('match',$match_result);
        }else{
            /*POSTで指定されてこなかった場合の処理*/
            $team ="C大阪";
            $item = "3";
            $match_result = $this->getMatchByTeam($team, $item);
            //var_dump($match_result);
            $this->set('match',$match_result);
        }
        

        //$this->saveMatchJLeague();
        
        
        //ナビスコカップの情報を取得
        /*実装中*/
        //$nabisuko_result = $this->getMatcesNabisuko();
        //debug($nabisuko_result);
        //$this->setMatchesNabisuko($nabisuko_result);
        
        

        
        /*画面表示テスト*/
        //if($this->show()){
            //Formからのデータ受け取り
            //var_dump("POSTデータ受け取り");
            ////$data = $this->show();
            //debug($data);
        //}
       // else{
            
        //}
        
        /*ＤＢから取得テスト*/
        //$trend_g = $this->getTeamTrendGoal("鹿島", "2014");   //得点傾向
        //$trend_l = $this->getTeamTrendLos("仙台", "2014");    //失点傾向
        //$trend_w = $this->getTeamTrendWin("C大阪", "2014");   //勝利傾向
        //$goal_rank_t = $this->getGoalrankingByTeam("神戸", "5", "2014");  //指定チームのゴールランキング
        //var_dump($goal_rank_t);                               
        //$goal_rank = $this->getGoalRanking("2014");           //ゴールランキング
        //var_dump($goal_rank);
        //$league_ranking = $this->getLeagueRanking("2014", "j1");
        //debug($league_ranking);
        
        /*ヘルパーに初期値(前回入力値）をセットする
         * 参照 CakePHP実践入門 p.139
         * 
         *          */    
        //$id = $this->request->pass[0];  //
        if($this->request->is('post')){
            $data = array(
                'team'  => $this->request->data['match']['team'],
                'count' => $this->request->data['match']['count'],
            );
            if($this->Post->save($data)){
                //debug("保存しました");
                $this->Session->setFlash("保存しました");
                $this->redirect('/match/index');
            }
        }else{
            //var_dump("POSTされていないので初期値を設定します");
            $options = array(
            'condtions' => array(
                'matchTeam' => "C大阪",
                'count' => "5",
                )
            );
            $this->request->data = $this->Post->find(
                    'first',
                    $options
            );
        }   
        //$this->request->data = $this->Post->find('team',$options);
        //$this->request->data = array('team',$options);
        
        
        
    }


    /*チーム一覧の取得*/
    public function getTeamList(){
         App::uses('Match','Model');     //モデルクラスにMatchを指定
        
        //モデルクラスのインスタンスを生成
        $match = new Match('Match','matches');
        $team_list = $match->getTeamListByDb();
        
        return $team_list;
    }

    /*Jリーグの結果の取得
     * J1
     * J2
     *      */
    public function saveMatchJLeague(){
        /*Jリーグ試合結果取得・保存
         * from: suponichi
         *          */
        $match_info_j1 = $this->getMatchesJ1League();
        //debug($match_info);
        $result_j1 = $this->setMatchesInfoJLeague($match_info_j1); //j1を保存
        //debug($result_j1);
        $match_info_j2 = $this->getMatchesJ2League();
        //debug($match_info_j2);
        $result_j2 = $this->setMatchesInfoJLeague($match_info_j2,"j2");  //j2を保存
        //debug($result_j2);
    }


    /*Jリーグの試合結果を登録*/
    public function getMatchesJ1League(){
        //Jリーグの試合結果を取得
        $match_result_j = $this->Matches->getMatchInfoJleague(GAME_MATCH_RESULT);
        //debug($match_result_j);
        return $match_result_j;
    }
    
    public function getMatchesJ2League(){
         //Jリーグの試合結果を取得
        $match_result_j = $this->Matches->getMatchInfoJleague(GAME_MATCH_RESULT,"j2");
        return $match_result_j;
    }

    /* ナビスコ杯の試合情報を取得して返す
     *
     * 
     * return : ナビスコ杯の試合情報（1P分）
     *      */
    public function getMatcesNabisuko(){
        $result_y = $this->Matches->getMatchInfoJleague(YAMAZAKI_MATCH_RESULT,"ヤマザキナビスコ杯");
        return $result_y;
    }
    
    /*ナビスコ杯の試合結果を登録*/
    public function setMatchesNabisuko($nabisuko_result,$data_item=""){
        App::uses('Match','Model');     //モデルクラスにMatchを指定
        
        $data_item = $data_item;
        
        if(!$data_item){
            $data_item = array("section",
                               "match_date",
                               "home_team",
                                "score",
                                "home_score",
                                "away_score",
                                "away_team",
                                "start_time",
                                "stadium",
                                "match_year",
                                "match_month",
                                "league");
        }
        
        //debug($data_item);
        
        //モデルクラスのインスタンスを生成
        $match = new Match('Match','matches');
        //$match->setMatchesDb($match_info, $j_class,$data_item);
        $format_result = $match->formatMatces($nabisuko_result, $data_item);
        //debug($format_result);
        
        $league = "ヤマザキナビスコ杯";
        
        $result = $match->setMatches($format_result,$data_item,$league);
        
        return $result;
    }
    
    

    /*試合結果を登録（Jリーグ）*/
    public function setMatchesInfoJLeague($match_info,$league="j1",$data_item=""){
        App::uses('Match','Model');     //モデルクラスにMatchを指定
        
        if(!$data_item){
            $data_item = array("section",
                               "date_s",
                               "match_date",
                               "home_team",
                                "score",
                                "home_score",
                                "away_score",
                                "away_team",
                                "start_time",
                                "stadium",
                                "match_year",
                                "match_month",
                                "league");
        }
        
        //debug($league);
        
        //モデルクラスのインスタンスを生成
        $match = new Match('Match','matches');
        //$match->setMatchesDb($match_info, $j_class,$data_item);
        $format_result = $match->formatMatces($match_info, $data_item);
        //debug($format_result);
        $result = $match->setMatches($format_result,$data_item,$league);
        
        return $result;
    }
    
    /*チームの直近試合結果を件数指定して取得
     * $team チーム
     * $item 取得件数
     *      */
    public function getMatchByTeam($team,$item){
         App::uses('Match','Model');     //モデルクラスにTeamTrendを指定
        
        //モデルクラスのインスタンスを生成
        $match = new Match('Match','matches');
        $result =  $match->getMatchDataByTeam($team, $item);
        return $result;
    }
    
    /*リーグの順位情報の取得
     * $year    年度
     * $league  リーグ（j1,j2などを指定）
     *      */
    public function getLeagueRanking($year,$league){
        App::uses('Match','Model');     
        
        //モデルクラスのインスタンスを生成
        $league_rank = new Match('Match','league');
        $result = $league_rank->getLeagueRankingDb($year, $league);
        return $result;
    }
    
     /*指定チームの年度ランキング情報を取得*/
    public function  getTeamRanking($name,$year=""){
        App::uses('Match','Model');
        
        if($year == ""){
             $date = date("Y", time());//今年の年度を付与
             $year = $date;
        }
        
        //モデルクラスのインスタンスを生成
        $league_rank = new Match('Match','league');
        $result = $league_rank->getTeamRankingDb($name, $year);
        return $result;
    }
    
    
    /*Formデータの受け取り、返却*/
    public function show(){
        //POSTが送信されたかどうか
        if($this->request->is('POST')){
            $team = $this->request->data['match']['team'];;
            $count = $this->request->data["match"]['count'];
            $data['team'] = $team;
            $data['count'] = $count;
        }
        else{
            return false;
        }
        return $data;
    }
    
    /*チームの得点傾向の取得*/
    public function getTeamTrendGoal($team,$year){
        App::uses('Match','Model');     
        
        //モデルクラスのインスタンスを生成
        $trend_g = new Match('Match','teamtrendgoal');
        $result = $trend_g->getTeamTrendGoalDb($team,$year);
        return $result;
    }
    
    /*チームの失点傾向の取得*/
    public function getTeamTrendLos($team,$year){
         App::uses('Match','Model');     
        
        //モデルクラスのインスタンスを生成
        $trend_l = new Match('Match','teamtrendlos');
        $result = $trend_l->getTeamTrendLosDb($team,$year);
        return $result;
    }
    
     /*チームの勝利傾向の取得*/
    public function getTeamTrendWin($team,$year){
         App::uses('Match','Model');    
        
        //モデルクラスのインスタンスを生成
        $trend_w = new Match('Match','teamtrendwinning');
        $result = $trend_w->getTeamTrendWinDb($team,$year);
        return $result;
    }
    
    /*指定したチームの選手を得点ランキングに入っている選手を取得*/
    public function getGoalrankingByTeam($team,$count,$year){
        App::uses('Match','Model');    
        
        //モデルクラスのインスタンスを生成
        $goal_ranking = new Match('Match','goalranking');
        $result = $goal_ranking->getGoalrankingByTeamDb($team, $count, $year);
        return $result;
    }
    
    /*ゴールランキングの取得*/
    public function getGoalRanking($year,$count = 20,$league = "j1"){
        App::uses('Match','Model');    
        
        //モデルクラスのインスタンスを生成
        $goal_ranking = new Match('Match','goalranking');
        $result = $goal_ranking->getGoalRanking($year, $count,$league);
        return $result;
    }
    
    
}

?>