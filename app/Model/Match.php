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
    
    /** *********  DBからの取得処理 *************
     *
     * 
     * 
     * *************************************** */
    
    /*試合情報の取得（チーム）
     * $team チーム名
     * $item 過去何回かを取得
     *      */
    public function getMatchDataByTeam($team,$item){
        $data = array(
           "conditions" => array(
                "AND" => array(
                   "OR" => array(
                         array(
                             "home_team" => $team
                         ),
                         array(
                             "away_team" => $team
                         ),
                     ),
                ),
           ),
           'order' => array("match_date DESC"),
           'limit' => (int)$item,      
        );
        
        $result = $this->find("all",$data);
        
        return $result;
    }
    
    
    /*チームリストの取得(テスト)*/
    public function  getTeamListByDb(){
        $data = array(
          'fields' => array('home_team','home_team'),
        );
        
        $result = $this->find('list',$data);
        //debug($result);
        
        return $result;
    }
}