<?php

/* 
 *  試合情報のモデル
 * * /
 */
App::uses('AppModel','Model');

class News extends AppModel{
    
    
    /*ニュース（記事）の1記事の登録処理*/
    public function setOneNewsDb($statuses){
         foreach ($statuses as $status){
            $title_box;
            $bddy;
            $updatetime;
            
            //debug($status);
            
            /*登録前の処理*/
            foreach ($status['title_box'][0] as $var){
                /*配信日時の取得*/
                if(preg_match('#\d+年\d.+月\d.+日.+#', $var)){
                    $updatetime = $var;
                }
                //$title_box = $title_box.$var."<br />"; 
            }
            var_dump($updatetime);
            
            /*登録前に同じものがないか確認*/
            
            
            /*登録用データの整形処理*/
//            $data[] = array(
//                'news_title' => $status['held_time'],
//                'page' =>(int)$status['page'],
//                'page_title' => $status['page_title'],
//                'title_box' => $title_box,
//                'body' => $body,
//                'site_name' => $status['site_name'],
//                'uri' => $status['uri'],
//                'update_time' => $update_time,
//            );
            
             
        }
//        $result = $this->saveAll($data);
//        debug($result);
        
    }
    
    /** *********  DBからの取得処理 *************
     *
     * 
     * 
     * *************************************** */
    
   
    
    
    /*チームリストの取得(テスト)*/
    public function  getNewsDb(){
        $data = array(
          'fields' => array('home_team','home_team'),
        );
        
        $result = $this->find('list',$data);
        //debug($result);
        
        return $result;
    }
}
 ?>