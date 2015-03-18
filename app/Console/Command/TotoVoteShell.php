<?php

/*Toto投票率取得シェル*/
App::uses('ComponentCollection','Controller');
App::uses('TotoComponent','Controller/Component');
App::uses('TotovotesController','Controller');
App::uses('TotoVotesComponent','Controller/Component');

class TotoVoteShell extends AppShell{
    public $uses = array('POST','Totovote','Minivote','Goal3vote');    //使用するモデルを宣言
    
    /*TotoComponentの呼び出し*/
    //コントローラーの現在のアクションハンドラの前に呼び出し
    public function startup() {
        $collection = new ComponentCollection();
        $this->Toto = new TotoComponent($collection);
        //$collection_2 = new ComponentCollection();
        $this->Totovotes = new TotoVotesComponent($collection);
        parent::startup();
        $this->TotoController = new TotovotesController;
        $this->TotoVotesController = new TotovotesController;
    }
    
    /*メイン処理*/
    public function main(){
       
       
    }
    
    /*Totoの投票率の取得・保存*/
    public function saveToto(){
       //Toto投票率のDB登録
       $toto_vote =  $this->Toto->getTotoVoteByYJ();
       $this->TotoController->setTotoOnlyVote($toto_vote);
        
    }
    
    /*totoの試合情報(今回開催)のすべてを取得・保存*/
    public function saveTotoAllMatch(){
        /*試合情報の一括取得*/
        $recent_held = $this->TotoController->getRecentHeld();
        //debug($recent_held);
        //$recent_held++;
        $match_info = $this->Totovotes->getTotoMatchInfo(TOTO_OFFICIAL,$recent_held);
        //$vote = $this->Toto->getTotoVoteByYJ();
        //debug($match_info['mini-B']);
        
        /*情報を保存*/
        $this->TotoVotesController->setTotoMatch($match_info);
        
    }
    
    
    /*Totoの試合情報の取得・保存*/
    private function saveTotoMatch($match_info,$held_time){
        
    }
    
    
    /*miniの投票率の取得・保存*/
    private function saveMini(){
        
        
    }
    
    /*miniの試合情報の取得・保存*/
    private function saveMiniMatch($match_info,$held_time){
        debug($match_info);
        debug($held_time);
    }


    /*goal3の投票率の取得・保存*/
    private function saveGoal3(){
        
    }
    
    /*goal3(2)の試合情報の取得・保存*/
    private function saveGoal3Match($match_info,$held_time){
        
    }

}

?>
