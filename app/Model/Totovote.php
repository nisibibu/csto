<?php

//App::uses('AppModel', 'Model');
class Totovote extends AppModel{
    /*totovotesテーブルにtoto投票率の登録*/
    public $useTable = 'totovotes';  //モデルがtotovoteテーブルを使用するように指定
    public $useDbConfig = 'default';    //defaultの接続設定を指定
    
    //登録処理
    public function setTotoVoteDb($statuses){
        /*DBへ保存*/
        //$data = array();
        //debug($statuses);
        foreach ($statuses as $status){
            $data[] = array(
                'heldtime' => $status['held_time'],
                'helddate' => $status['held_date'],
                'no' => $status['No'],
                'card' => $status['card'],
                'all_vote' => $status['all_vote'],
                '1_vote' => $status['1_vote'],
                '0_vote' => $status['0_vote'],
                '2_vote' => $status['2_vote'],
            );
        }
        
        //debug($data);
        
        
        $this->saveAll($data);
    }
    
    //更新処理
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
