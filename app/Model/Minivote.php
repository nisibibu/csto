<?php

App::uses('Vote', 'Model');
class Minivote extends Vote{
    /*totovotesテーブルにtoto投票率の登録*/
    public $useTable = 'minivotes';  //モデルがtotovoteテーブルを使用するように指定
    public $useDbConfig = 'default';    //defaultの接続設定を指定
    
    /*Toto(Toto mini含む）登録処理
     * totoone
     *      */
    public function setTotoVoteDb($statuses){
        
        /*DBへ保存（新規登録）*/
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


        $result = $this->saveAll($data);
        
        
        
    }
    
    
    /*miniの試合情報を登録*/
    public function setMiniMatchDb($statuses){
        $type;  //A or B
        if(array_key_exists("mini-A", $statuses) && array_key_exists("held_time", $statuses)){
            $type = "mini-A";
        }else if(array_key_exists("mini-B", $statuses) && array_key_exists("held_time", $statuses)){
            $type = "mini-B";
        }else{
            /*データがわたってきていない場合、そのまま返す*/
            return;
        }
        //debug($statuses);
        $held_time = $statuses['held_time'];
        
        $is_set = $this->isRecentlyset($held_time); //登録回登録済みかチェック
        debug($is_set);
        
        /*miniのデータ整形*/
        $mini_data=array();
        $mini_data = $this->formatMiniData($statuses[$type]);
        if(!$is_set){
            /*登録処理*/
            var_dump("登録処理");
            foreach ($mini_data as $status){
                $data[] = array(
                    'held_time' => $held_time,
                    'held_date' => $this->formatDate($status['held_date']),
                    'match_time' => $status['match_time'],
                    'no' => $status['no'],
                    'home_team' => $status['home_team'],
                    'away_team' => $status['away_team'],
                    'class' => $status['class'],
                    'stadium' => $status['stadium'],
                );
            }
            /* 登録処理 */
            //debug($data);
            $result = $this->saveAll($data);
            //debug($result);
        }else{
            /*更新処理*/
            var_dump("更新処理");
             foreach($mini_data as $status){
                $conditions = array(
                    'held_time' => $held_time,
                     'no' => $status['no'],
                );

                $today = date("Y-m-d H:i:s");
                $data = array(
                    'held_time' => $held_time,
                    'held_date' => "'".$this->formatDate($status['held_date'])."'",
                    'no' => (int)$status['no'],
                    'match_time' => "'". $status['match_time']."'",
                    'home_team' => "'". $status['home_team']."'",
                    'away_team' => "'".$status['away_team']."'",
                    'class' => "'".$status['class']."'",
                    'stadium' => "'".$status['stadium']."'",
                    'modified' => "'".$today."'",
                );
                                
                $result = $this->updateAll($data,$conditions);
            }
        }
        return $result;
    }    
    
    /*miniのデータ整形
     * mini-A or mini-B のデータ配列を受け取り
     * DB登録用に整形して返す
     *      */
    public function formatMiniData($statuses){
        /*data部分は引数にするか検討*/
        $data = array(
            "no",
            "held_date",
            "match_time",
            "stadium",
            "home_team",
            "VS",
            "away_team",
            "データ",
            "class",
        );
        
        $mini_data = array();
        foreach($statuses as $var){
            $temp = array();
            for($i = 0;  $i < count($var); $i++){
                $temp[$data[$i]] = $var[$i];
            }
            $mini_data[] = $temp;
        }
        return $mini_data;
    }
    
    /* 日付変換を行って返す
     * str:  03/08  
     * 
     * return: 2015/03/18
     *      */
    public function formatDate($str){
            $date_year = date("Y");
            //debug($date);
            $date = $date_year."/".$str;
            return $date;            
    }
    
    
    //public $useTable = "minivotes";
    
    /* 登録回（最新回）の登録済みか判定 */
    public function isRecentlyset($held_time){
        $is_set = FALSE;    //登録回（最新回）登録済みか
        $recent_held = $this->getRecentTime();
        
        //debug($held_time);
        if($held_time === (int)$recent_held){
            $is_set = TRUE;
        }
        debug($recent_held);
        return $is_set;
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
    public function getVoteTotoRecent($held_time=""){
        /*指定回ではない場合、DBから最新回を取得*/
        if(!isset($held_time)){
           $held_time = $this->getRecentTime();
        }
        
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
