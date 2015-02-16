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
            /*
            $feed = "http://www.footballchannel.jp/feed/";  //フットボールチャンネル
            debug($feed);
            $output[] = $this->Rss->read($feed,3);
            debug($output);
             * 
             */
            /*
            $feed ="http://headlines.yahoo.co.jp/rss/soccerk-c_spo.xml";    //SOCCER KING - スポーツ - Yahoo!ニュース
            debug($feed);
            $output[] = $this->Rss->read($feed,3);
            debug($output);
             * 
             */
            /*
            foreach($list as $feed){
                debug($feed);
                $output[] = $this->Rss->read($feed,$item=1);
            }
            debug($list);
            */

           
        }


}


?>