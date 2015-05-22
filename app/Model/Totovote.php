<?php

App::uses('Vote', 'Model');
class Totovote extends Vote{
    /*totovotesテーブルにtoto投票率の登録*/
    public $useTable = 'totovotes';  //モデルがtotovoteテーブルを使用するように指定
    public $useDbConfig = 'default';    //defaultの接続設定を指定
    
    /*バリデーションの設定*/
    
    public $validate = array(
            'held_time' => array(
                'required' => true,
                'allowEmpty' => false,
            ),
            'held_date' => array(
                'required' => true,
                'allowEmpty' => false,
            ),
            'no' => array(
                'required' => true,
                'allowEmpty' => false,
            ),
            'hometeam' => array(
                'required' => true,
                'allowEmpty' => false,
            ),
            'away_team' => array(
                'required' => true,
                'allowEmpty' => false,
            ),
        );
    
    /*アソシエーションの設定*/
//    public $belongsTo = array(
//        'Minivotes' => array(
//            'className' => 'Minivote',
//            'foregnKey' => 'held_time',
//        ),
//    );
    
    
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
    public function setTotoMatchDb($statuses){
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
        
        $match_info = $this->formatTotoData($statuses);
        //debug($toto_data);
        
        
        /*開催回登録チェック*/
        $is_set = $this->isRecentlyset($held_time);
        
         
        /*開催回未登録の場合*/
        if(!$is_set){
            foreach ($match_info as $status){
            //debug($status);
            
            $data[] = array(
                'held_time' => $held_time,
                'held_date' => $this->formatDate($status['held_date']),
                'match_time' => $status['match_time'],
                'no' => (int)$status['no'],
                'home_team' => $status['home_team'],
                'away_team' => $status['away_team'],
                'stadium' => $status['stadium'],
                );
            }
            $result = $this->saveAll($data);
            //debug($data);
        }else{        
        /*開催回登録済（更新処理）*/       
            foreach($match_info as $status){
                    $conditions = array(
                        'held_time' => $held_time,
                         'no' => $status['no'],
                    );

                    //$today = date("Y-m-d H:i:s");
                    //debug($today);
                    $data = array(
                        'held_time' => $status['held_time'],
                        'held_date' => "'".  $this->formatDate($status['held_date'])."'",
                        'match_time' => "'".$status['match_time']."'",
                        'no' => (int)$status['no'],
                        'home_team' => "'". $status['home_team']."'",
                        'away_team' => "'".$status['away_team']."'",
                        'stadium' => "'".$status['stadium']."'",
                    );
                    //debug($data);
                    $result = $this->updateAll($data,$conditions);
                }

            }
    }
    
    /*miniのデータ整形
     * mini-A or mini-B のデータ配列を受け取り
     * DB登録用に整形して返す
     *      */
    public function formatTotoData($statuses){
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
        );
        
        $held_time = $statuses['held_time'];
        $toto_status = $statuses['toto'];
        //debug($statuses);
        
        $toto_data = array();
        foreach($toto_status as $var){
            $temp = array();
            for($i = 0;  $i < count($var); $i++){
                $temp[$data[$i]] = $var[$i];
            }
            $temp['held_time'] = $held_time;
            $toto_data[] = $temp;
        }
        return $toto_data;
    }
    
    
    //public $useTable = "minivotes";
    
    /* 登録回（最新回）の登録済みか判定 */
    public function isRecentlyset($held_time){
        $is_set = FALSE;    //登録回（最新回）登録済みか
        $recent_held = $this->getRecentTime();
        
        //debug($held_time);
        if((int)$held_time === (int)$recent_held){
            $is_set = TRUE;
        }
        //debug($recent_held);
        return $is_set;
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
        //debug($result);
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
    
    /*今回（直近）の開催回を取得して返却
     * 
     * 
     *      */
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
     * @param string type
     * 
     * @return array result
     * 
     *************************************************/
    public function getVoteTotoRecent($held_time,$type=""){
        $result = array();
        if($type){
           $data = array(
           "conditions" => array(
                "AND" => array(
                   "held_time" => $held_time,
                   "class" => $type
                ),
           ),
           'order' => array("no ASC"),  
           );
        }else{
           $data = array(
           "conditions" => array(
                "AND" => array(
                   "held_time" => $held_time
                ),
           ),
           'order' => array("no ASC"),  
           );
        }
        
        //debug($data);
        $result = $this->find("all",$data);
        //debug($result);
        
        return $result;
    }
    
    /*今回の試合情報のみ取り出し（toto)
     * (画面表示用に整形）
     * 
     * array(
     *      no
     *      held_date
     *      match_time
     *      home_team
     *      away_team
     *      stadium
     * )
     * 'held_time' =>
     * .
     * .
     * 
     * 
     **********************************/
    public function getTotoMatchInfoOnly($match_info){
        //debug($match_info);
        $result = array();
        $held_time = $match_info[0]['Totovote']['held_time'];
        
        foreach($match_info as $match){
            $temp = array();
            $var = $match['Totovote'];
            foreach ($var as $key => $value) {
                if($key === 'held_date'){
                    $temp['開催日'] = $value;
                }else if($key === 'match_time'){
                    $temp['開始時刻'] = $value;
                }else if($key === 'no'){
                    $temp[$key] = $value;
                }else if($key === 'home_team'){
                    $temp['ホーム'] = $value;
                }else if($key === 'away_team'){
                    $temp['アウェイ'] = $value;
                }else if($key === 'stadium'){
                    $temp['スタジアム'] = $value;
                }else if($key === '1_vote'){
                    $temp['1(%)'] = $value;
                }else if($key === '0_vote'){
                    $temp['0(%)'] = $value;
                }else if($key === '2_vote'){
                    $temp['2(%)'] = $value;
                }else if($key === 'class'){
                    $temp[$key] = $value;
                }else if($key === 'team'){
                    $temp['チーム'] = $value;
                }else if($key === 'position'){
                    $temp['H/A'] = $value;
                }
            }
            $result[] = $temp;
        }
        
        $result['held_time'] = $held_time;//開催回の追加
        //debug($result);
        return $result;
    }
    
    
}
