<?php

/*Toto投票率取得シェル*/
App::uses('ComponentCollection','Controller');
App::uses('MatchesComponent','Controller/Component');
App::uses('MatchController','Controller');

class MatchShell extends AppShell{
    public $uses = array('POST','Match');    //使用するモデルを宣言
    
    /*TotoComponentの呼び出し*/
    //コントローラーの現在のアクションハンドラの前に呼び出し
    public function startup() {
        $collection = new ComponentCollection();
        $this->Matches = new MatchesComponent($collection);
        parent::startup();
        $this->MatchController = new MatchController();
    }
    
    /*メイン処理*/
    public function main(){
     $this->out('Hello World!');  
       
    }
    
    
    /*Jリーグ(J1 J2)の試合結果を取得・保存*/
    public function saveJLeagueMatch(){
        /*J1の試合結果を取得・保存*/
        $match_info_j1 = $this->Matches->getMatchInfoJleague(GAME_MATCH_RESULT); //J1の情報を取得
        $result_j1 = $this->MatchController->setMatchesInfoJLeague($match_info_j1); //J1のの情報を保存
        debug($result_j1);
        
        /*J2の試合結果を取得*/  
        $match_info_j2 = $this->Matches->getMatchInfoJleague(GAME_MATCH_RESULT,"j2"); //J1の情報を取得
        $result_j2 = $this->MatchController->setMatchesInfoJLeague($match_info_j2,"j2");  //J2の情報を保存
        debug($result_j2);
    }
    
    /*ヤマザキナビスコカップの試合結果を保存*/
    public function saveNabisukoMatch(){
        /*ナビスコカップの試合結果を取得*/
        $match_info_nabisuko = $this->Matches->getMatchInfoJleague(YAMAZAKI_MATCH_RESULT,"ヤマザキナビスコ杯");
        //debug($match_info_nabisuko);
        $result_nabisuko = $this->MatchController->setMatchesNabisuko($match_info_nabisuko);
        //debug($result_nabisuko);
    }
    
    /*天皇杯の試合結果を保存*/
    public function saveEmperorMatch(){
        
    }

}

?>