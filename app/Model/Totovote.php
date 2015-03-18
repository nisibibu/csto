<?php

App::uses('Vote', 'Model');
class Totovote extends Vote{
    /*totovotesテーブルにtoto投票率の登録*/
    public $useTable = 'totovotes';  //モデルがtotovoteテーブルを使用するように指定
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
    
    /*Totoの投票率（のみ）登録
     * yahoo 
     * 
     *      */
     public function setTotoOnlyVoteDb($statuses){
         /*保存する前に同一データがないかチェックする*/
        /* 新規ではない場合、1件ずつチェックして更新する
         *
         *          */
        $held_time;
        $no;
        foreach($statuses as $status){
            $held_time = $status['held_time'];
            $no = $status['no'];
        }
        
        debug($held_time);
        debug($no);
        
        //登録しようしているデータ（今回分）登録済みかチェック
        $update_flag = FALSE;
        
        $options = array(
          'conditions' => array(
              'held_time' => $held_time,
          ),
        );

        
        $c_result = $this->find('count',$options);
        debug($statuses[0]['held_date']);
        
        //return $result;
        
        if($no === (int)$c_result){
            $update_flag = TRUE; 
        }
        
        if($update_flag){
            //更新
            foreach($statuses as $status){
                $conditions = array(
                    'held_time' => $status['held_time'],
                     'no' => $status['no'],
                );

                $today = date("Y-m-d H:i:s");
                debug($today);
                $data = array(
                    'held_time' => $status['held_time'],
                    'held_date' => "'".$status['held_date']."'",
                    'no' => (int)$status['no'],
                    'home_team' => "'". $status['home_team']."'",
                    'away_team' => "'".$status['away_team']."'",
                    '1_vote' => $status['1_vote'],
                    '0_vote' => $status['0_vote'],
                    '2_vote' => $status['2_vote'],
                    'year' => $status['year'],
                    'month' => $status['month'],
                    'modified' => "'".$today."'",
                );
                
                
                $result = $this->updateAll($data,$conditions);
            }
           
        }
        else{
            //debug($statuses);
            foreach ($statuses as $status){
                debug($status);
            
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
                    'month' => $status['month'],
                );
            }
        $result = $this->saveAll($data);
        }
        return $result;
    }
    
    
    /*totoの試合情報の登録
     * toto
     * mini A mini B
     * goal 3
     * 存在するものを登録 
     *      */
//    public function setTotoMatchAllDb($statuses){
//        /*くじの情報の存在チェック*/
//        if(array_key_exists("toto", $statuses)){
//            //totoの試合情報の登録
//        }
//        if(array_key_exists("mini-A", $statuses)){
//            //mini-Aの試合情報の登録
//            var_dump("mini-Aの登録処理");
//        }
//        if(array_key_exists("mini-B", $statuses)){
//            //mini-Bの試合情報の登録
//        }
//        if(array_key_exists("goal", $statuses)){
//            //goal3(2)の試合情報の登録
//            var_dump("goal3の登録処理");
//        }
//        
//        
//        
//    }
    



    /* totoの試合情報を登録
     * 
     *      */
    public function setTotoMatchDb($statuses, $held){
        /*情報を登録用に整形*/
        $match_info = array();  //登録用データ
        $held_time;             //開催回
        
        $held_time = $statuses['held_time'];
        
        if(!is_array($statuses)){
            //配列入ってきていない場合
            return;
        }
        
        /*開催（数字）を取得*/
        //preg_match("#\d+#", $held,$m);
        //$held_time = $m[0];
        
        foreach ($statuses as $status){
            $temp = array();
            for($i = 0; $i < count($status); $i++){
                switch ($i){
                    case 0:
                        $temp['no'] = $status[$i];
                        break;
                    case 1:
                        /*日付に変換*/
                        $now = date("Y--m-d",time());
                        var_dump($now);
                        $held_date;
                        $temp['held_date'] = $status[$i];
                        break;
                    case 2:
                         $temp['match_time'] = $status[$i];
                        break;
                    case 3:
                         $temp['stadium'] = $status[$i];
                        break;
                    case 4:
                         $temp['home_team'] = $status[$i];
                        break;
                    case 5:
                         $temp['vs'] = $status[$i];
                        break;
                    case 6:
                         $temp['away_team'] = $status[$i];
                        break;
                    case 7:
                         $temp['data'] = $status[$i];
                        break;
                }
            }
            /*配列に開催回を追加*/
            $temp['held_time'] = $held_time;
            
            $match_info[] = $temp;  /*登録用配列へ追加*/
        }
        debug($match_info);
        
        /*開催回登録チェック*/
        
        
        /*開催回未登録の場合*/
//        if(count($match_info) !== (int)$c_result){
//            var_dump(count($match_info));
//             debug($statuses);
//            foreach ($statuses as $status){
//            //debug($status);
//            
//            $data[] = array(
//                'held_time' => $status['held_time'],
//                'held_date' => $status['held_date'],
//                'no' => (int)$status['no'],
//                'home_team' => $status['home_team'],
//                'away_team' => $status['away_team'],
//                '1_vote' => $status['1_vote'],
//                '0_vote' => $status['0_vote'],
//                '2_vote' => $status['2_vote'],
//                'class' => $status['class'],
//                'year' => $status['year'],
//                'month' => $status['month'],
//                );
//            
//            }
//            //$result = $this->saveAll($data);
//            debug($result);
//        }else{
//        
//        /*開催回登録済（更新処理）*/
//        
//        foreach($statuses as $status){
//                $conditions = array(
//                    'held_time' => $status['held_time'],
//                     'no' => $status['no'],
//                );
//
//                //$today = date("Y-m-d H:i:s");
//                //debug($today);
//                $data = array(
//                    'held_time' => $status['held_time'],
//                    //'held_date' => "'".$status['held_date']."'",
//                    'match_time' => "'".$status['match_time']."'",
//                    'no' => (int)$status['no'],
//                    'home_team' => "'". $status['home_team']."'",
//                    'away_team' => "'".$status['away_team']."'",
//                    'stadium' => "'".$status['stadium']."'",
//                );
//                                
//                $result = $this->updateAll($data,$conditions);
//            }
//        
//        }
        
    }
    
    //public $useTable = "minivotes";
    
    /* 登録回（最新回）の登録済みか判定 */
    public function isRecentlyset($table,$held_time){
        $useTable = $table; //使用するテーブルの指定 
        
        $update_flag = FALSE;
        
        $options = array(
          'conditions' => array(
              'held_time' => $held_time,
          ),
        );

        
        $c_result = $this->find('count',$options);
        debug($c_result);
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
