<?php

App::uses('ComponentCollection','Controller');
App::uses('MatchesComponent','Controller/Component');
App::uses('MatchController','Controller');

class StatShell extends AppShell{
     public $uses = array('POST','Stat');    //使用するモデルを宣言
    
    /*MatchComponentの呼び出し*/
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
    
    
    /*Jリーグ(J1 J2)のスタッツ情報を取得・保存*/
    public function saveJleagueStats(){
        App::import('Model','Stat');
        $stats = new Stat();
        
        /*J1のスタッツを取得・保存*/
        $stats_info_j1 = $this->Matches->getStatuRecentMatch("j1"); //J1の情報を取得
        $stats->setStats($stats_info_j1, "j1");
        $stats_concomitant_j1 = $this->Matches->getStatsSupo("j1"); //J1の付随情報を取得
        $stats->setStatsConcomitant($stats_concomitant_j1, "j1");
        
        
        /*J2のスタッツを取得・保存*/
        $stats_info_j2 = $this->Matches->getStatuRecentMatch("j2"); //J2の情報を取得
        $stats->setStats($stats_info_j2, "j2");
        $stats_concomitant_j2 = $this->Matches->getStatsSupo("j2"); //J2の付随情報を取得
        $stats->setStatsConcomitant($stats_concomitant_j2, "j2");
    }
}