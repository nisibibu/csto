<?php

/*Toto投票率取得シェル*/
App::uses('ComponentCollection','Controller');
App::uses('TotoComponent','Controller/Component');

class TotoVoteShell extends AppShell{
    public $uses = array('POST','Totovote');    //使用するモデルを宣言
    
    /*TotoComponentの呼び出し*/
    //コントローラーの現在のアクションハンドラの前に呼び出し
    public function startup() {
        $collection = new ComponentCollection();
        $this->Toto = new TotoComponent($collection);
        parent::startup();
    }
    
    /*メイン処理*/
    public function main(){
       $toto_vote =  $this->Toto->getTotoVote();
       $this->Totovote->setTotoVoteDb();
       //$this->Totovote->upTotoVoteDb();
    }
    
    
}

?>
