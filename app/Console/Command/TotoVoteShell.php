<?php

/*Toto投票率取得シェル*/
App::uses('ComponentCollection','Controller');
App::uses('TotoComponent','Controller/Component');
App::uses('TotovotesController','Controller');

class TotoVoteShell extends AppShell{
    public $uses = array('POST','Totovote');    //使用するモデルを宣言
    
    /*TotoComponentの呼び出し*/
    //コントローラーの現在のアクションハンドラの前に呼び出し
    public function startup() {
        $collection = new ComponentCollection();
        $this->Toto = new TotoComponent($collection);
        parent::startup();
        $this->TotoController = new TotovotesController;
    }
    
    /*メイン処理*/
    public function main(){
       
       
    }
    
    /*Totoの投票率の取得・保存*/
    public function saveToto(){
       //Toto投票率のDB登録(ToToOne)
       $toto_vote =  $this->Toto->getTotoVoteByYJ();
       $this->TotoController->setTotoOnlyVote($toto_vote); 
        
    }
    
    /*Totoの試合情報の取得・保存*/
    public function saveTotoMatch(){
        
    }
    
    /*miniの投票率の取得・保存*/
    public function saveMini(){
        
        
    }
    
    /*goal3の投票率の取得・保存*/
    public function saveGoal3(){
        
    }
    
    

}

?>
