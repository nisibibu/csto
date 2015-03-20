<?php

/*Toto投票率取得シェル*/
App::uses('ComponentCollection','Controller');
App::uses('MatchComponent','Controller/Component');
App::uses('MatchController','Controller');

class MatchShell extends AppShell{
    public $uses = array('POST','Match');    //使用するモデルを宣言
    
    /*TotoComponentの呼び出し*/
    //コントローラーの現在のアクションハンドラの前に呼び出し
    public function startup() {
        $collection = new ComponentCollection();
        $this->Match = new MatchComponent($collection);
        parent::startup();
        $this->MatchController = new MatchController();
    }
    
    /*メイン処理*/
    public function main(){
       
       
    }
    
    
    /*Jリーグ(J1 J2)の試合結果を取得・保存*/
    public function saveJLeagueMatch(){
        /*試合情報(j1 j2)の一括取得・保存*/
        $this->MatchController->saveMatchJLeague();
        
    }
    
    /*ヤマザキナビスコカップの試合結果を保存*/
    public function saveYamazakiMatch(){
        
    }
    
    /*天皇杯の試合結果を保存*/
    public function saveEmperorMatch(){
        
    }

}

?>