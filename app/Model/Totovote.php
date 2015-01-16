<?php

//App::uses('AppModel', 'Model');
class Totovote extends AppModel{
    /*totovotesテーブルにtoto投票率の登録*/
    
    public $useTable = 'totovotes';  //モデルがtotovoteテーブルを使用するように指定
    public $useDbConfig = 'default';    //defaultの接続設定を指定
    
    public function setTotoVoteDb(){
        /*DBへ保存*/
        /*
        foreach ($statuses as $status){
            $data[] = array(
                'heldtime' => '0'
        
            );
        }
        */
        $data[] = array(
                'heldtime' => '758',
                'helddate' => '2014-12-08',
                'no' => '2',
                'card' =>'鹿島 VS ガンバ'
            );
        $this->saveAll($data);
    }
    
    public function upTotoVoteDb(){
        $data = array(
                'helddate' => "'758'",
                'no' => "'1'",
                'card' => "'鹿島 VS 鳥栖'",
            ); 
        $conditions = array(
                'heldtime' => 758,
                'no' => 1,
            );
        $this->updateAll($data, $conditions);
    }
    
}
