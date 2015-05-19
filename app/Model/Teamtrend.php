<?php

/* 
 *  チーム傾向のモデル
 * * /
 */
App::uses('AppModel', 'Model');

class Teamtrend extends AppModel{
      // public $useTable = 'teamtrendlos';
        
        //登録処理（時間帯別得点）
        public function setTrendGoalDb($statuses){
            //登録・更新処理判定
            $year = $statuses[0][8];
            $month = $statuses[0][9];
            $date = $statuses[0][10];
            
            //登録しようとしているデータの登録件数取得
            $find_count = $this->findByDate($year,$month,$date);
            
            if($find_count === 0){
                //登録処理
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
                        'data_month' => $status[9],
                        'data_date' => $status[10],
                    );
                }
                //$result = $this->saveAll($data);
                //debug($result);
            }else{
                 //更新処理
                foreach($statuses as $status){
                $conditions = array(
                    'team' => $status[0],
                    'data_year' => $year,
                    'data_month' => $month,
                    'data_date' => $date,
                );

                $today = date("Y-m-d H:i:s");
                //debug($today);
                $data = array(
                    'team' => "'".$status[0]."'",
                    'allgoal' => $status[1],
                    'time1_15' => $status[2],
                    'time15_30' =>$status[3],
                    'time30_45' => "'".$status[4]."'",
                    'time46_60' => $status[5],
                    'time61_75' => $status[6],
                    'time76_90' => "'".$status[7]."'",
                    'data_year' => $year,
                    'data_month' => $month,
                    'data_date' => $date,
                    'modified' => "'".$today."'",
                );
                //debug($data);
                $result = $this->updateAll($data,$conditions);
                
                }
            }
            
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
        
        /*登録処理（状況別勝敗）*/
        public function setTrendWinDb($statuses){
            foreach ($statuses as $status){
                //debug($status);
                $data[] = array(
                    'team' => $status[0],
                    'win_pre' => $status[1],
                    'draw_pre' => $status[2],
                    'lose_pre' => $status[3],
                    'winning_pre' => $status[4],
                    'win_lead' => $status[5],
                    'draw_lead' => $status[6],
                    'lose_lead' => $status[7],
                    'winning_lead' => $status[8],
                    'data_year' => $status[9],
            );
        }

         $result =  $this->saveAll($data);
         debug($result);
        }
        
        //日時で検索してヒットした件数を返す
        public function findByDate($year,$month,$date){
             $data = array(
                "conditions" => array(
                             "data_year" => $year,
                             "data_month" => $month,
                             "data_date" => $date,
                ),
                //'order' => array("ranking ASC"),    
            );
        //debug($data);
        
            $result_count = $this->find("count",$data);
            //debug($result_count);
            return $result_count;
        }
}

