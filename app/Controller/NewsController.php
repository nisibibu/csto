<?php

/* 
 * ニュースコントローラー 
 * 
 */

App::uses('Xml','Utility');

class NewsController extends Controller{
    
        /*コンポーネントの指定*/
        public $components = array('Rss');
        
        public function index(){
            /*RSSコンポーネントテスト*/
            $list = $this->Rss->getFromtoFileData();
            var_dump($list);
            //$feed = "http://web.gekisaka.jp/feed";
            //$output = $this->Rss->read($feed);
            //debug($output);
        }


}


?>