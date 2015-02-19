<?php

//App::uses('AppModel', 'Model');
class Totovote extends AppModel{
    /*totovotesテーブルにtoto投票率の登録*/
    public $useTable = 'totovotes';  //モデルがtotovoteテーブルを使用するように指定
    public $useDbConfig = 'default';    //defaultの接続設定を指定
    
    /*Toto(Toto mini含む）登録処理
     * totoone
     *      */
    public function setTotoVoteDb($statuses){
        /*DBへ保存*/
        //$data = array();
        //debug($statuses);
        foreach ($statuses as $status){
            $data[] = array(
                'held_time' => $status['held_time'],
                'held_date' => $status['held_date'],
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
    
    /*Totoの投票率（のみ）登録
     * yahoo 
     * 
     *      */
     public function setTotoOnlyVoteDb($statuses){
        //debug($statuses);
        foreach ($statuses as $status){
            //debug($status);
            
            $data[] = array(
                'held_time' => $status['held_time'],
                'held_date' => $status['held_date'],
                'no' => (int)$status['no'],
                'home_team' => $status['home_team'],
                'away_team' => $status['away_team'],
                '1_vote' => $status['1_vote'],
                '0_vote' => $status['0_vote'],
                '2_vote' => $status['2_vote'],
                'year' => $status['year'],
                'month' => $status['year'],
            );
            
             
        }
        $result = $this->saveAll($data);
        debug($result);
    }
    
    
    /* miniの投票率を登録
     * yahoo
     *      */
    public function setMiniVoteDb($statuses){
        //debug($statuses);
        foreach ($statuses as $status){
            //debug($status);
            
            $data[] = array(
                'held_time' => $status['held_time'],
                'held_date' => $status['held_date'],
                'no' => (int)$status['no'],
                'home_team' => $status['home_team'],
                'away_team' => $status['away_team'],
                '1_vote' => $status['1_vote'],
                '0_vote' => $status['0_vote'],
                '2_vote' => $status['2_vote'],
                'class' => $status['class'],
                'year' => $status['year'],
                'month' => $status['month'],
            );
            
             
        }
        $result = $this->saveAll($data);
        debug($result);
    }

    /*Goal3(2)の投票率の登録
     * yahoo
     *      */
    public function setGoal3VoteDb($statuses){
        //debug($statuses);
        foreach ($statuses as $status){
            $data[] = array(
                'held_time' => $status['held_time'],
                'held_date' => $status['held_date'],
                'no' => $status['no'],
                'team' => $status['team'],
                'position' => $status['position'],
                '0_vote' => $status['0_vote'],
                '1_vote' => $status['1_vote'],
                '2_vote' => $status['2_vote'],
                '3_vote' => $status['3_vote'],
                'year' => $status['year'],
                'month' => $status['month'],
            );
        }
        $result = $this->saveAll($data);
        debug($result);
    }

    /*更新処理
     *
     * 
     *      */
    //更新処理（テスト）
    public function upTotoVoteDb(){
        $data = array(
                'held_date' => "'758'",
                'no' => "'1'",
                'card' => "'鹿島 VS 鳥栖'",
            ); 
        $conditions = array(
                'held_time' => 758,
                'no' => 1,
            );
        $this->updateAll($data, $conditions);
    }
    
    /************************************
     * DB からの取得処理
     * **********************************/
    
    
    /*DBに登録されている一番古い開催回の取得*/
    public function getOldestTime(){
        $old_held = array();
        $data = array(
            'fields' => array('MIN(held_time) AS old_held')
            );
        $result = $this->find('first',$data);
        
        if(isset($result[0]['old_held'])){
            //var_dump("開催回取得");
            $recent_held = $result[0]['old_held'];
        }
        
        return $recent_held;
    }
    
    /*今回（直近）の開催回を取得して返却*/
    public function getRecentTime(){
        $recent_held = array();
        $data = array(
            'fields' => array('MAX(held_time) AS recent_held')
            );
        $result = $this->find('first',$data);
        
        if(isset($result[0]['recent_held'])){
            //var_dump("開催回取得");
            $recent_held = $result[0]['recent_held'];
        }
        //$debug($recent_held);
        return $recent_held;
    }
    
    
    
     /*今回（直近の）totoの試合情報（投票率含む）を取得***
     * 
     *************************************************/
    public function getVoteTotoRecent($held_time){
        $result = array();
        $data = array(
           "conditions" => array(
                "AND" => array(
                   "held_time" => $held_time
                ),
           ),
           'order' => array("no ASC"),  
        );
        //debug($data);
        $result = $this->find("all",$data);
        //debug($result);
        
        return $result;
    }
    
}
