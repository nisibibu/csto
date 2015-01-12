<?php

//App::uses('AppModel', 'Model');
class Totovote extends AppModel{
    /*totovotesテーブルにtoto投票率の登録*/
    
    public $useTable = 'totovotes';  //モデルがtotovoteテーブルを使用するように指定
    public $useDbConfig = 'default';    //defaultの接続設定を指定
    
    public function setTotoVoteDb(){
        $data = array(
            'Totovote' =>array(
                'id' => 2,
                'heldtime' => '0'
            )
        
        );
        $this->Totovote->save($data);
    }
    
}
