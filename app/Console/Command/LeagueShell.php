<?php

/*リーグ情報登録シェル*/
App::uses('AppController','Controller');
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
        $league_info_j1 = $this->Leagues->getLeagueInfo("j1");; //J1の情報を取得
        $result_j1 = $this->LeagueController->setLeagueInfo($league_info_j1, "j1");; //J1の情報を保存
        //debug($league_info_j1);
        
        /*J2の順位表を取得・保存*/
        $league_info_j2 = $this->Leagues->getLeagueInfo("j2");; //J2の情報を取得
        $result_j2 = $this->LeagueController->setLeagueInfo($league_info_j2, "j2");; //J2の情報を保存
        //debug($result_j2);
    }
    
    /*Jリーグのゴールランキングを保存*/
    public function saveJLeagueGoalRanking(){
        /*取得*/
        
        
    }
    
    /*チームの試合傾向を保存*/
    public function saveTeamTrend(){
        /**/
    }

}

?>