<?php

/* 
 *  試合情報のモデル
 * * /
 */
App::uses('AppModel','Model');

class News extends AppModel{
    
    
    /*ニュース（記事）の登録*/
    public function setOneNewsDb($statuses,$j_class){
        
        
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