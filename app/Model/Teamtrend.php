<?php

/* 
 *  チーム傾向のモデル
 * * /
 */
App::uses('Appmodel', 'Model');

class Teamtrend extends Appmodel{
      // public $useTable = 'teamtrendlos';
        
        //登録処理（時間帯別得点）
        public function setTrendGoalDb($statuses){
            foreach ($statuses as $status){
                //debug($status);
                $data[] = array(
                    'team' => $status[0],
                    'allgoal' => $status[1],
                    'time1_15' => $status[2],
                    'time15_30' => $status[3],
                    'time30_45' => $status[4],
                    'time46_60' => $status[5],
                    'time61_75' => $status[6],
                    'time76_90' => $status[7],
                    'data_year' => $status[8],
            );
        }

         $result = $this->saveAll($data);
         debug($result);
        }
         

        /*登録処理（時間帯別得点）*/
        public function setTrendLosDb($statuses){
            foreach ($statuses as $status){
                //debug($status);
                $data[] = array(
                    'team' => $status[0],
                    'all_los' => $status[1],
                    'time1_15' => $status[2],
                    'time15_30' => $status[3],
                    'time30_45' => $status[4],
                    'time46_60' => $status[5],
                    'time61_75' => $status[6],
                    'time76_90' => $status[7],
                    'data_year' => $status[8],
            );
        }

         $result =  $this->saveAll($data);
         debug($result);
        }
}

