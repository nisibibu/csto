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
            $feed = "http://web.gekisaka.jp/feed?category=domestic"; //ゲキサカ（Jリーグ&国内）;
            $output[] = $this->Rss->read($feed,3);
            //debug($output);
            $title = $this->Rss->getFeedTitle($output);
            debug($title);
           
           
        }


}


?>