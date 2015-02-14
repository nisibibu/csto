<?php

/* 
 * ニュースコントローラー 
 * 
 */

App::uses('Xml','Utility');

class NessController extends Controller{
    
        /*コンポーネントの指定*/
        public $components = array('Rss');
        
        public function index(){
            /*RSSコンポーネントテスト*/
            $feed = "http://web.gekisaka.jp/feed";
            $output = $this->Rss->read($feed);
            debug($output);
        }


}


?>