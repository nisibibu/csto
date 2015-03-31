<?php

/*Toto投票率取得シェル*/
App::uses('ComponentCollection','Controller');
App::uses('LeagueComponent','Controller/Component');
App::uses('LeagueController','Controller');

class LeagueShell extends AppShell{
    public $uses = array('POST','League');    //使用するモデルを宣言
    
    /*TotoComponentの呼び出し*/
    //コントローラーの現在のアクションハンドラの前に呼び出し
    public function startup() {
        $collection = new ComponentCollection();
        $this->Leagues = new LeagueComponent($collection);
        parent::startup();
        $this->LeagueController = new LeagueController();
    }
    
    /*メイン処理*/
    public function main(){
       
       
    }
    
    
    /*Jリーグの順位表を保存*/
    public function saveJLeagueRanking(){
        /*J1の順位表を取得・保存*/
        $match_info_j1 = $this->Matches->getMatchInfoJleague(GAME_MATCH_RESULT); //J1の情報を取得
        $result_j1 = $this->MatchController->setMatchesInfoJLeague($match_info_j1); //J1のの情報を保存
        debug($result_j1);
        
        /*J2の順位表を取得・保存*/
        $match_info_j2 = $this->Matches->getMatchInfoJleague(GAME_MATCH_RESULT,"j2"); //J1の情報を取得
        $result_j2 = $this->MatchController->setMatchesInfoJLeague($match_info_j2);  //J2の情報を保存
        debug($result_j2);
    }
    
    /*Jリーグのゴールランキングを保存*/
    public function saveJLeagueGoalRanking(){
        /*取得*/
        $match_info_nabisuko = $this->Matches->getMatchInfoJleague(YAMAZAKI_MATCH_RESULT,"ヤマザキナビスコ杯");
        //debug($match_info_nabisuko);
        $result_nabisuko = $this->MatchController->setMatchesNabisuko($match_info_nabisuko);
        //debug($result_nabisuko);
    }
    
    /*チームの試合傾向を保存*/
    public function saveTeamTrend(){
        
    }

}

?>