<?php

/* 
 * ニュースコントローラー 
 * 
 */

App::uses('Xml','Utility');

class NewsController extends Controller{
    
        /*コンポーネントの指定*/
        public $components = array('Rss','News','NewsCraw');
        
        /**/
        public function index(){
            /*ニュースサイトからの情報取得（テスト）*/
            $this->NewsCraw->getNewsInfoSoccerKing();
           
           
        }
        
        /*RSSからデータを取得*/
        public function getRss(){
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