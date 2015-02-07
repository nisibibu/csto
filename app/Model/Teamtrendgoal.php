<?php

/* 
 *  チーム傾向（ゴール）のモデル
 * * /
 */
App::uses('Appmodel', 'Model');

class Teamtrendgoal extends Appmodel{
        public $useTable = 'teamtrendgoal';  //モデルがteamtrendgoalテーブルを使用するように指定
        public $useDbConfig = 'default';    //defaultの接続設定を指定
        
        //登録処理
        public function setTrendGoalDb($statuses){
        /*DBへ保存*/
            foreach ($statuses as $status){
                debug($status);
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
        
        debug($data);
        
        
         $this->saveAll($data);
        }
}
