<?php

/* 
 *  試合情報のモデル
 * * /
 */
App::uses('AppModel','Model');

class News extends AppModel{
    
    
    /*ニュース（記事）の1記事の登録処理*/
    public function setOneNewsDb($statuses){
        
        
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