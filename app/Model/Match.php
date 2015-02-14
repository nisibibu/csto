<?php

/* 
 *  試合情報のモデル
 * * /
 */
App::uses('AppModel','Model');

class Match extends AppModel{
    
    /*試合情報の登録（１節）*/
    public function setMatchesDb($statuses,$j_class,$data_item){
        /*１節まとめて登録*/
        $temp = array();
        $j = 0;  
        foreach ($statuses as $status){
            $i = 0;
            foreach($status as $var){
               //１項目の処理
               $tmp = array(
                   $data_item[$i] => $var,
               );
               $i++;
               $temp = $temp + $tmp; 
            }
            $league = array(
                "league" => $j_class,
            );
            $temp = $temp + $league;
            $data[$j] = $temp;
            $temp = array();
            $j++;
        }
        //debug($data);
        
        $result = $this->saveAll($data); 
         
        debug($result);
    }
    
    /*速報の登録*/
    public function setMatchQuickDb($statuses,$j_class){
        
        
    }
}