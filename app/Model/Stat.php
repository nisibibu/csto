<?php

/* 
 *  試合情報のモデル
 * * /
 */
App::uses('AppModel','Model');
App::import('Component','Common');

class Stat extends AppModel{
    
    /* スタッツ情報の登録・更新（付随データ)
     * 
     *
     * 
     *
     */
    public function setStatsConcomitant($stats,$league){
        //日付のデータ
        $date_list = array_keys($stats);
        
        //setStats呼び出し(日付毎)
        foreach($stats as $data_by_day){
            $result[] = $this->setStats($data_by_day, $league);
        }
        
        
    }
    
    /* スタッツ情報を登録・更新処理
     * 
     * 
     *      
     */
    public function setStats($stats,$league){
        //年月日 節を取得(節毎に一括で設定するので先頭を取得)
        //debug($stats);
        $team_list = array_keys($stats);
        $first_team_name = $team_list[0];
        $team_count = count($team_list);
        
        if(array_key_exists('year',$stats[$first_team_name])){
            $year = $stats[$first_team_name]['year'];
        }
        if(array_key_exists('month',$stats[$first_team_name])){
            $month = $stats[$first_team_name]['month'];
        }
        if(array_key_exists('day',$stats[$first_team_name])){
            $day = $stats[$first_team_name]['day'];
        }
        if(array_key_exists('section',$stats[$first_team_name])){
            $section = $stats[$first_team_name]['section'];
        }
        
                
        //section 年 league で件数取得
        $is_set = NULL;
        $is_set = $this->isSetSection($section, $year, $league,$team_count);
        //debug($is_set);
        
        if($is_set === 0){
            //登録処理
            $data = $this->formatSetData($stats,$league);
            $result = $this->saveAll($data);
            //debug($data);
        }else if($is_set === TRUE){
            //更新処理
            foreach ($stats as $one_stats){
                $conditions = $this->formatUpdateField($one_stats,$league);
                $data = $this->formatUpdateData($one_stats, $league);
                //debug($data);
                $result[] = $this->updateAll($data,$conditions);                
            }
            return $result;
        }else{
            //エラー処理
            
        }
        
    }
    
    /*登録するデータの整形
     * @param array data
     * @param string league
     * 
     * @return array return_data
     *      */
    private function formatSetData($data,$league){
        $return_data;
        
        App::import('Model', 'Match');
        $match = new Match();
        
        foreach($data as $var){
            $var['league'] = $league;
            $var['team'] = $match->formatTeamName($var['team']);
            $return_data[] = $var;
        }
        return $return_data;
    }
    
    /* 更新するデータの整形
     * １チームのデータを整形する
     * item の型が文字列型の場合,前後に"'"で括る
     * @param array data
     * @param string league
     * 
     *      */
    private function formatUpdateData($data,$league){
        App::import('Model','Match');
        $match = new Match();
        
        $return_data;
        
        $temp = array();
        $item_list = $this->getColumnTypes();   //tableからカラムリスト取得
        
        
        $concomitant_data_count = 5;    //付随データ個数
        if(count($data) === $concomitant_data_count){
            //付随データ登録用
            foreach($item_list as $key=>$value){
                if($key === 'section' || $key === 'year'){
                    $temp[$key] = $data[$key];
                }
                else if($key === 'team'){
                    $temp[$key] = "'".$match->formatTeamName($data[$key])."'";
                }else if($key === 'weather'){
                    $temp[$key] = "'".$data[$key]."'";
                }
                else if($key === 'league'){
                    $temp['league'] = "'".$league."'";
                }
                else if($key === 'modified') {
                    $today = date("Y-m-d H:i:s");
                    $temp['modified'] =  "'".$today."'";
                }
            }
            
        }else{
            //Stats詳細データ登録用
            foreach($item_list as $key=>$value){
            if($key === 'created' || $key  === 'weather'){
                continue;
            }elseif ($key === 'keep_percent' && $league === 'j2'){
                continue;
            }else if($key === 'team'){
                    $temp[$key] = "'".$match->formatTeamName($data[$key])."'";
            }else if($key === 'league'){
                $temp['league'] = "'".$league."'";
            }else if($key === 'modified') {
                $today = date("Y-m-d H:i:s");
                $temp['modified'] =  "'".$today."'";
            }else if($value === 'string'){
                $temp[$key] = "'".$data[$key]."'";
            }else 
                $temp[$key] = $data[$key];
            }
        }
        
        $return_data = $temp;
        
        
        return $return_data;
    }
    
    /*更新処理の項目を作成
     *@param
     * 
     *@return array field
     *      
     */
    private function formatUpdateField($data,$league){
        App::import('Model','Match');
        $match = new Match();
        
        $conditions = array(
            'section' => $data['section'],
            'team' => $match->formatTeamName($data['team']),
            'year' => $data['year'],
            'league' => $league,
        );
        return $conditions;
    }
    
    /*項目が設定されているか判定
     * @param string section
     * @param int    year
     * @param string league
     * 
     *      */
    private function isSetSection($section,$year,$league,$team_count){
        $data = array(
          'conditions' => array(
              'section' => $section,
              'year' => $year,
              'league' => $league,
          ),
        );
        //debug($data);
        $count = $this->find('count',$data);
        //debug($count);
        if($count > 0 && $count <= $team_count){
            //var_dump("更新処理");
            return TRUE;
        }else if($count === 0){
            return $count;
        }
            
        return FALSE;
        
    }
    
    
}
?>
