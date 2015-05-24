<?php

/* 
 *  試合情報のモデル
 * * /
 */
App::uses('AppModel','Model');
App::import('Component','Common');

class Stat extends AppModel{
    /* スタッツ情報を登録・更新処理
     * 
     * 
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
        
        $year = $stats[$first_team_name]['year'];
        $month = $stats[$first_team_name]['month'];
        $day = $stats[$first_team_name]['day'];
        $section = $stats[$first_team_name]['section'];
                
        //section 年 league で件数取得
        $is_set = NULL;
        $is_set = $this->isSetSection($section, $year, $league,$team_count);
        
        
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
        //debug($league);
        foreach($data as $var){
            $var['league'] = $league;
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
        $return_data;
        
        $temp = array();
        $item_list = $this->getColumnTypes();
        //debug($item_list);
        foreach($item_list as $key=>$value){
            if($key === 'created'){
                continue;
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
        $conditions = array(
            'section' => $data['section'],
            'team' => $data['team'],
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
          'conditons' => array(
              'section' => $section,
              'year' => $year,
              'league' => $league,
          ),
        );
        //debug($team_count);
        $count = $this->find('count',$data);

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
