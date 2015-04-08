<?php

/* 
 *  リーグ情報のモデル
 * * /
 */
App::uses('AppModel','Model');
App::import('Component','Common');

class League extends AppModel{
    
    /*リーグ情報の登録*/
    public function setLeagueDb($statuses,$j_class){
        /* 付与情報の取り出し
         * 年
         * 月
         * 週
         */
        $year = $statuses['year'];
        unset($statuses['year']);
        $month = $statuses['month'];
        unset($statuses['month']);
        $week = $statuses['week'];
        unset($statuses['week']);
        
        
        $is_set = $this->is_setYear($j_class, $year,$month,$week);
        //debug($is_set);
        if($is_set === 0){
            //登録処理
             foreach ($statuses as $status){
                $data[] = array(
                    'team' => $status["チーム名"],
                    'point' => $status["勝点"],
                    'match_count' => $status['試合数'],
                    'v_count' => $status["勝数"],
                    'd_count' => $status["引分数"],
                    'l_count' => $status["敗数"],
                    'goal_point' => $status["得点"],
                    'lose_point' => $status["失点"],
                    'goal_difference' => $status["得失点差"],
                    'year' => $year,
                    'month' => $month,
                    'week' => $week,
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
                    'month' => $month,
                    'week' => $week,
                    'league' => $j_class,
                );

                $today = date("Y-m-d H:i:s");
                //debug($today);
                $data = array(
                    'team' => "'".$status["チーム名"]."'",
                    'point' => $status["勝点"],
                    'match_count' => $status['試合数'],
                    'v_count' =>$status["勝数"],
                    'd_count' => $status["引分数"],
                    'l_count' => $status["敗数"],
                    'goal_point' => $status["得点"],
                    'lose_point' => $status["失点"],
                    'goal_difference' => $status["得失点差"],
                    'year' => $year,
                    'month' => $month,
                    'week' => $week,
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
        
        /* 付与情報の取り出し
         * 年
         * 月
         * 週
         */
        $year = $statuses['year'];
        unset($statuses['year']);
        $month = $statuses['month'];
        unset($statuses['month']);
        $week = $statuses['week'];
        unset($statuses['week']);

        /* Common コンポ―ネントインスタンス化 */
        $collection = new ComponentCollection();
        $common = new CommonComponent($collection);
        //debug($common->formatTeamName("G大阪"));
        
        $is_set = $this->is_setYear($j_class, $year,$month,$week);
        //debug($is_set);
        
        if($is_set === 0){
            //登録処理
             foreach ($statuses as $status){
                $data[] = array(
                    'rank' => $status["順位"],
                    'name' => $status["選手名"],
                    'team' => $common->formatTeamName($status['チーム名']),
                    'position' => $status['Pos'],
                    'goal' => $status["PK"],
                    'pk' => $status["得点"],
                    'shoot' => $status["シュート"],
                    'shoot_per_goal' => $status["シュート決定率"],
                    'shoot_per_90' => $status["90分平均得点"],
                    'match_count' => $status["試合数"],
                    'play_time' => $status['出場時間（分）'],
                    'warning_count' => $status['警告'],
                    'exit_count' => $status['退場'],
                    'year' => $year,
                    'month' => $month,
                    'week' => $week,
                    'league' => $j_class,
                );
            }
         
            //debug($data);
            $result = $this->saveAll($data);
        }else{
            //更新処理
            foreach($statuses as $status){
                $conditions = array(
                    'team' => $common->formatTeamName($status['チーム名']),
                    'year' => $year,
                    'month' => $month,
                    'week' => $week,
                    'league' => $j_class,
                );

                $today = date("Y-m-d H:i:s");
                //debug($today);
                $data = array(
                    'rank' => "'".$status["順位"]."'",
                    'name' => "'".$status["選手名"]."'",
                    'team' => "'".$common->formatTeamName($status['チーム名'])."'",
                    'position' => "'".$status["Pos"]."'",
                    'goal' => $status["得点"],
                    'pk' => $status["PK"],
                    'shoot' => $status["シュート"],
                    'shoot_per_goal' => $status["シュート決定率"],
                    'shoot_per_90' => $status["90分平均得点"],
                    'match_count' => $status['試合数'],
                    'play_time' => $status['出場時間（分）'],
                    'warning_count' => $status['警告'],
                    'exit_count' => $status['退場'],
                    'year' => $year,
                    'month' => $month,
                    'week' => $week,
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
    
    
    /*設定個数を返却（年度)*/
    public function is_setYear($league,$year,$month,$week){
             
            $data = array(
            "conditions" => array(
                "AND" => array(
                   'year' => $year,
                   'month' => $month,
                   'week' => $week,
                   "league" => $league,
                ),
            ),
            'fields' => array('team AS team')
            );

        
        $result = $this->find('count',$data);

        return $result;
    }
    
    /*********************DBから情報取り出し処理*******************************/
   
    
    
    
}

