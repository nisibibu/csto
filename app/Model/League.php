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
    
    
    /*設定個数を返却（該当セクションが登録されているか判定)*/
    public function is_set($league,$year){
//        $recent_secttion = array();
//        $is_set;
             
            $data = array(
            "conditions" => array(
                "AND" => array(
                   'section' => $section,
                   "match_date"  => $match_date,
                   "league" => $league,
                ),
            ),
            'fields' => array('section AS section')
            );

        
        $result = $this->find('count',$data);

        return $result;
    }
}

