<?php

/* 
 *  リーグ情報のモデル
 * * /
 */
App::uses('AppModel','Model');

class League extends AppModel{
    
    /*リーグ情報の登録*/
    public function setLeagueDb($statuses,$j_class){
        //年の取り出し
        $year = $statuses['year'];
        unset($statuses['year']);
        
        $is_set = $this->is_setYear($j_class, $year);
        
        if($is_set === 0){
            //登録処理
             foreach ($statuses as $status){
                $data[] = array(
                    'team' => $status["チーム名"],
                    'point' => $status["勝点"],
                    'v_count' => $status["勝数"],
                    'd_count' => $status["引分数"],
                    'l_count' => $status["敗数"],
                    'goal_point' => $status["得点"],
                    'lose_point' => $status["失点"],
                    'goal_difference' => $status["得失点差"],
                    'year' => $year,
                    'ranking' => $status["順位"],
                    'league' => $j_class,
                );
            }
         
            //debug($data);
            $result = $this->saveAll($data);
        }else{
            //更新処理
            foreach($statuses as $status){
                $conditions = array(
                    'team' => $status['チーム名'],
                    'year' => $year,
                    'league' => $j_class,
                );

                $today = date("Y-m-d H:i:s");
                //debug($today);
                $data = array(
                    'team' => "'".$status["チーム名"]."'",
                    'point' => $status["勝点"],
                    'v_count' =>$status["勝数"],
                    'd_count' => $status["引分数"],
                    'l_count' => $status["敗数"],
                    'goal_point' => $status["得点"],
                    'lose_point' => $status["失点"],
                    'goal_difference' => $status["得失点差"],
                    'year' => $year,
                    'ranking' => $status["順位"],
                    'league' => "'".$j_class."'",
                    'modified' => "'".$today."'",
                );
                //debug($data);
                $result = $this->updateAll($data,$conditions);
            }
        }
       
         //debug($result);
        return $result;
    }
    
    /*ゴールランキングの登録*/
    public function setGoalRankingDb($statuses,$j_class){
        
        $is_set;    //登録判定用変数
        
        foreach ($statuses as $status){
                $data[] = array(
                    'rank' => $status[0],                       //順位
                    'name' => $status[1],                       //選手名
                    'team' => $status[3],                       //チーム
                    'position' => $status[4],                   //ポジション
                    'goal' => (int)$status[5],                  //ゴール数
                     'pk' => (int)$status[6],                   //PK数
                    'shoot' => (int)$status[7],                 //シュート数
                    'shoot_per_goal' => (float)$status[8],      //シュート決定率
                    'shoot_per_90' => (float)$status[9],        //90分平均得点
                    'match_count' => (int)$status[10],           //試合数
                    'play_time' => (int)$status[11],            //出場時間
                    'warning_count' => (int)$status[12],        //警告数
                    'exit_count' => (int)$status[13],           //退場数
                    'year' => $status[14],                      //年度
                    'league' => $j_class,                       //リーグ 
            );
        }
         
         //debug($data);
         $result = $this->saveAll($data);
         //debug($result);
    }
    
    
    /*設定個数を返却（年度)*/
    public function is_setYear($league,$year){
             
            $data = array(
            "conditions" => array(
                "AND" => array(
                   'year' => $year,
                   "league" => $league,
                ),
            ),
            'fields' => array('team AS team')
            );

        
        $result = $this->find('count',$data);

        return $result;
    }
}

