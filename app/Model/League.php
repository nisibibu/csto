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
         debug($result);
    }
        
}

