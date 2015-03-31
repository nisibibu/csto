<?php

/* 
 *  試合情報のモデル
 * * /
 */
App::uses('AppModel','Model');

class Match extends AppModel{
    
    //public $primaryKey = 'section';
    
    /*Controllerクラスから受け取ったデータを
     * １節毎に分割
     * 現在日時より前の情報
     *      */
    public function formatMatces($statuses,$data_item){
        $now_time;  //現在日時の保持
        $now_time = date("Y-m-d",time());
        //debug($now_time);
        
        //debug($statuses);
        
        $class = $statuses['class'];    //class(j1,j2 etc..)の保持
        unset($statuses['class']);      //class要素の削除
        $result_info = array();         //返却用配列
        
        /*取得データを整形*/
        
        //debug($statuses[0]);
        
        //開催されていない回のデータは捨てる
        if($class === "ヤマザキナビスコ杯"){
            foreach ($statuses as $status){
               //祭儀の試合の日付を確認し、日にちが現在以降ならば保持しない
               $max = count($status) - 1;
               //debug($status[$max][1]);
               if($status[$max][1] < $now_time){   //日付判定
                    //var_dump("過去のデータ発見");
                    $temp['section'] = $status;
                    $temp['date'] = $status[0][1];
                    //$temp['last_date'] = $status[$max][2];
                    $result_info[] = $temp;
               }
            }
        }
        else{
               foreach ($statuses as $status){
               //祭儀の試合の日付を確認し、日にちが現在以降ならば保持しない
               $max = count($status) - 1;
               //debug($status[$max][2]);
               if($status[$max][2] < $now_time){   //日付判定
                    //var_dump("過去のデータ発見");
                    $temp['section'] = $status;
                    $temp['first_date'] = $status[0][2];
                    $temp['last_date'] = $status[$max][2];
                    $result_info[] = $temp;
               }
            }
        }
        return $result_info;
    }
    
    /*試合結果の保存*/
    public function setMatches($statuses,$data_item,$j_class){
        /*１節まとめて登録*/
        //var_dump($statuses[0]);
        
        $info_flag = FALSE; //情報だけでも登録するか(処理の判定に使用予定)
        $past_flag = FALSE; //過去分だけでも登録するか(処理の判定に使用予定)
        $is_set = FALSE;    //登録対象データは登録済みか(処理の判定に使用予定)

        /*節で処理*/
        //$match_info = $statuses[0]['section'];
        foreach ($statuses as $var){
            $match_info = $var['section'];
            $result[] = $this->setMatchesOneSection($match_info, $j_class, $data_item);
        }    
        return $result;
    }
    
    
    /*試合情報の登録（１節）
     * J1 J2 ナビスコカップ使用
     * 
     * $statuses:   １節のデータ(配列)
     * $j_class:    league
     * $data_item:  整形用のデータ項目(配列)
     * 
     *      */
    public function setMatchesOneSection($statuses,$j_class,$data_item){
        $section = $statuses[0][0]; //始めのデータの節を取り出し
        
        if($j_class === "ヤマザキナビスコ杯"){
            $year = $statuses[0][9];    //始めのデータの年を取り出し
            $match_date = $statuses[0][1];  //始めのデータの日付取り出し
        }else{
            $year = $statuses[0][10];   //始めのデータの年を取り出し
        }
        
        
        
        /*今回登録する部分の登録済み判定*/
        if($j_class === "ヤマザキナビスコ杯"){
           $set_count = $this->isSetSecttion($section, $year,$j_class,$match_date);
        }else{
            $set_count = $this->isSetSecttion($section, $year,$j_class);
        }
        
        debug($set_count);
        
            if($set_count === 0){
                /*登録処理*/
                $temp = array();
                $j = 0;  
                foreach ($statuses as $status){
                    $i = 0;
                    foreach($status as $var){
                       //１項目の処理
                       $tmp = array(
                           $data_item[$i] => $var,
                       );
                       $i++;
                       $temp = $temp + $tmp; 
                    }
                    $league = array(
                        "league" => $j_class,
                    );
                    $temp = $temp + $league;
                    $data[$j] = $temp;
                    $temp = array();
                    $j++;
                }
                //debug($data);
            $result = $this->saveAll($data); 
            }else if($set_count > 8){
                /*該当回が登録されていた場合何もしない*/
                
                
            }else{
                /*データ削除してから登録処理*/
                
                
            }
            
        return $result = null;
    }
    
    /*速報の登録*/
    public function setMatchQuickDb($statuses,$j_class){
        
        
    }
    
    /** *********  DBからの取得処理 *************
     *
     * 
     * 
     * *************************************** */
    
    /*試合情報の取得（チーム）
     * $team チーム名
     * $item 過去何回かを取得
     *      */
    public function getMatchDataByTeam($team,$item){
        $data = array(
           "conditions" => array(
                "AND" => array(
                   "OR" => array(
                         array(
                             "home_team" => $team
                         ),
                         array(
                             "away_team" => $team
                         ),
                     ),
                ),
           ),
           'order' => array("match_date DESC"),
           'limit' => (int)$item,      
        );
        
        $result = $this->find("all",$data);
        
        return $result;
    }
    
    
    /*チームリストの取得*/
    public function  getTeamListByDb(){
        $data = array(
          'fields' => array('home_team','home_team'),
        );
        
        $result = $this->find('list',$data);
        //debug($result);
        
        return $result;
    }
    
    /*TeamTrendGoalテーブルから指定した年度のチームの情報を取り出し
     * チームの得点傾向の取得
     *      */
    public function getTeamTrendGoalDb($team,$year){
        $data = array(
           "conditions" => array(
                "AND" => array(
                        'team' => $team,
                        'data_year' => $year,
                     ),
                ),
        );
        
        $result = $this->find("all",$data);
        //debug($result);
        return $result;
    }
    
    /*TeamTrendLosテーブルから指定した年度のチームの情報を取り出し
     * チームの失点傾向の取得
     *      */
    public function getTeamTrendLosDb($team,$year){
        $data = array(
           "conditions" => array(
                "AND" => array(
                        'team' => $team,
                        'data_year' => $year,
                     ),
                ),
        );
        
        $result = $this->find("all",$data);
        //debug($result);
        return $result;
    }
    
    /*TeamTrendWinningテーブルから指定した年度のチームの情報を取り出し
     * チームの勝利傾向の取得
     *      */
    public function getTeamTrendWinDb($team,$year){
        $data = array(
           "conditions" => array(
                "AND" => array(
                        'team' => $team,
                        'data_year' => $year,
                     ),
                ),
        );
        
        $result = $this->find("all",$data);
        //debug($result);
        return $result;
    }
    
    /*チームのゴールランキングの昇順に取得*/
    public function getGoalrankingByTeamDb($team,$count,$year){
        $data = array(
           "conditions" => array(
                             "team" => $team,
                             "year" => $year,
            ),
           'order' => array("goal DESC"),
           'limit' => (int)$count,      
        );
        //debug($data);
        
        $result = $this->find("all",$data);
        //debug($result);
        return $result;
    }
    
    /*ゴールランキングの取得*/
    public function getGoalRanking($year,$count,$league){
        $data = array(
           "conditions" => array(
               "year" => $year,
               "league" => $league,
               "rank  BETWEEN ? AND ?" => array(1,$count),
                ),
           //'order' => array("goal DESC"),
           //'limit' => (int)$count,      
        );

        $result = $this->find("all",$data);
        //debug($result);
        return $result;
    }
    
    /*rリーグ順位の取得
     * 指定した年度の指定したリーグの順位表情報を取得 
     *      */
    public function getLeagueRankingDb($year,$league){
         $data = array(
           "conditions" => array(
                             "year" => $year,
                             "league" => $league,
            ),
           'order' => array("ranking ASC"),    
        );
        //debug($data);
        
        $result = $this->find("all",$data);
        //debug($result);
        return $result;
    }
    
    
    /*セクションの設定個数を返却（該当セクションが登録されているか判定)*/
    public function isSetSecttion($section,$year,$league = "j1",$match_date=""){
//        $recent_secttion = array();
//        $is_set;
        
        
        if($league === "ヤマザキナビスコ杯"){
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
        }else{
            $data = array(
            "conditions" => array(
                "AND" => array(
                   'section' => $section,
                   "league" => $league,
                   "match_year"  => $year,
                ),
            ),
            'fields' => array('section AS section')
            );
        }
        
        $result = $this->find('count',$data);

        return $result;
    }
    
    /*指定した節のデータを消去*/
    public function deleteOneSection($section,$year,$league){        
        /*直接SQL作成*/
        $db = $this->getDataSource();
        $db->fetchAll(
                "DELETE FROM `matches` WHERE section = ? AND match_year = ? AND league = ?",
                array($section, $year,$league)
        );
        
    }
    
}