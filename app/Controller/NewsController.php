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
            //$this->NewsCraw->getNewsInfoSoccerKing();   //サッカーキング
            $result_f = $this->NewsCraw->getNewsInfoFootBallChannel();  //フットボールチャンネル
            //debug($result_f);
            //$this->setNews($result_f);
        }
        
        /*RSSからデータを取得*/
        public function getRss(){
            /*RSSコンポーネントテスト*/
            $list = $this->Rss->getFromtoFileData();
            $feed = "http://web.gekisaka.jp/feed?category=domestic"; //ゲキサカ（Jリーグ&国内）;
            $output[] = $this->Rss->read($feed,3);
            //debug($output);
            $title = $this->Rss->getFeedTitle($output);
            //debug($title);
        }
        
        /*ニュース登録用*/
        public function setNews($page_info){
             App::uses('News','Model');     //モデルクラスにMatchを指定
        
            //モデルクラスのインスタンスを生成
            $news = new News('News','matches');
            $result = $news->setOneNewsDb($page_info);
            
        }


}


?>