<?php

//App::uses('AppModel', 'Model');
class Goal3vote extends AppModel{
    /*totovotesテーブルにtoto投票率の登録*/
    public $useTable = 'goal3votes';  //モデルがtotovoteテーブルを使用するように指定
    public $useDbConfig = 'default';    //defaultの接続設定を指定
    
    /*Toto(Toto mini含む）登録処理
     * totoone
     *      */
    public function setTotoVoteDb($statuses){
        
        /*DBへ保存（新規登録）*/
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
    
    /* Goal3の試合情報を登録・更新
     *
     * 
     *      */
    public function setGoal3MatchDb($statuses){
        $type;  //goal3 or goal2
        //debug($statuses);
        debug(count($statuses['goal']));
        if(array_key_exists("goal", $statuses) && array_key_exists("held_time", $statuses)){
            if(count($statuses['goal']) === 3){
                $type = "goal3";
            }else if(count($statuses)['goal'] === 2){
                $type = "goal2";
            }else{
                return; //データが揃っていない場合、そのまま抜ける
            }
        }else{
            /*データがわたってきていない場合、そのまま返す*/
            return;
        }
        //debug($statuses);
        $held_time = $statuses['held_time'];
        
        $is_set = $this->isRecentlyset($held_time); //登録回登録済みかチェック
        debug($is_set);
        
        /*goal3のデータ整形*/
        $goal3_data=array();
        $goal3_data = $this->formatGoal3Data($statuses['goal'],$held_time);
        //debug($goal3_data);
        
        if(!$is_set){
            /*登録処理*/
            var_dump("登録処理");
            foreach ($goal3_data as $status){
                $data[] = array(
                    'held_time' => $held_time,
                    'held_date' => $this->formatDate($status['held_date']),
                    'match_time' => $status['match_time'],
                    'no' => $status['no'],
                    'team' => $status['team'],
                    'position' => $status['position'],
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
             foreach($goal3_data as $status){
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
                    'team' => "'". $status['team']."'",
                    'position' => "'".$status['position']."'",
                    'stadium' => "'".$status['stadium']."'",
                    'modified' => "'".$today."'",
                );
                //debug($data);                
                $result = $this->updateAll($data,$conditions);
            }
        }
        return $result;
    }
    
    
    /*goal3(2)のデータ整形
     * mini-A or mini-B のデータ配列を受け取り
     * DB登録用に整形して返す
     *      */
    public function formatGoal3Data($statuses, $held_time){
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
            //"class",
        );
        
        $goal3_data = array();
        
        foreach($statuses as $var){
            $temp = array();
            
            for($i = 0;  $i < count($var); $i++){
                $temp[$data[$i]] = $var[$i];
            }
            //$temp = $home + $away;
            $goal3_data[] = $temp;
        }
        
        /*1試合をHomeとawayに分割*/
        
        $temp_data = array();
        
        /* いらないデータの削除 */
        foreach($goal3_data as $var){
            unset($var['VS']);
            unset($var['データ']);
            $temp_data[] = $var;
        }
        
        $goal3_data = $temp_data;
        
        $data_goal = array(
            "no",
            "held_date",
            "match_time",
            "stadium",
            "team",
            "vs_team"
        );
        
        $result = array();  //返却用
        
        $goal3_data = array_values($goal3_data);

        /* 整形 */
        $count = 0;
        foreach ($goal3_data as $var){
            $temp = array();
            $tmp = array();
            $home = array();    //Home
            $away = array();    //Away
            $no_h = $var['no'] + $count;
            $no_a = $no_h + 1;
            foreach ($var as $v){
                $tmp[] = $v;
            }
            //debug($tmp);
            for($i = 0; $i < count($tmp); $i++){
                if($i === 0){
                    $home["held_time"] = $held_time;
                    $away['held_time'] = $held_time;
                    $away[$data_goal[$i]] = $no_a;
                    $home[$data_goal[$i]] = $no_h;
                    $away[$data_goal[$i]] = $no_a;
                }
                else if($i === 4){
                    $home['team'] = $tmp[$i];
                    $home['position'] = "Home";
                }else if($i === 5){
                    $away['team'] = $tmp[$i];
                    $away['position'] = "Away";
                }else{
                    $home[$data_goal[$i]] = $tmp[$i];
                    $away[$data_goal[$i]] = $tmp[$i];
                }
            }
            //debug($away);
            $result[] = $home;
            $result[] = $away;
            $count++;
        }
        
        
        return $result;
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
