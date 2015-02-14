<?php

/* 
 *  リーグ情報のモデル
 * * /
 */
App::uses('AppModel','Model');

class League extends AppModel{
    
    /*リーグ情報の登録*/
    public function setLeagueDb($statuses,$j_class){
        foreach ($statuses as $status){
                $data[] = array(
                    'team' => $status[1],
                    'point' => $status[2],
                    'v_count' => $status[3],
                    'd_count' => $status[4],
                    'l_count' => $status[5],
                    'goal_point' => $status[6],
                    'lose_point' => $status[7],
                    'goal_difference' => $status[8],
                    'year' => '2014',
                    'ranking' => $status[0],
                    'league' => $j_class,
            );
        }
         
         $result = $this->saveAll($data);
         //debug($result);
    }
    
    /*ゴールランキングの登録*/
    public function setGoalRankingDb($statuses,$j_class){
        
        foreach ($statuses as $status){
                $data[] = array(
                    'rank' => $status[0],                       //順位
                    'name' => $status[1],                       //選手名
                    'team' => $status[2],                       //チーム
                    'position' => $status[3],                   //ポジション
                    'goal' => (int)$status[4],                  //ゴール数
                     'pk' => (int)$status[5],                   //PK数
                    'shoot' => (int)$status[6],                 //シュート数
                    'shoot_per_goal' => (float)$status[7],      //シュート決定率
                    'shoot_per_90' => (float)$status[8],        //90分平均得点
                    'match_count' => (int)$status[9],           //試合数
                    'play_time' => (int)$status[10],            //出場時間
                    'warning_count' => (int)$status[11],        //警告数
                    'exit_count' => (int)$status[12],           //退場数
                    'year' => $status[13],                      //年度
                    'league' => $j_class,                       //リーグ 
            );
        }
         
         debug($data);
         $result = $this->saveAll($data);
         //debug($result);
    }
}

