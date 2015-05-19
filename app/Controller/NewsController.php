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
            $result_sk = $this->NewsCraw->getNewsInfoSoccerKing();   //サッカーキング
            $result_f = $this->NewsCraw->getNewsInfoFootBallChannel();  //フットボールチャンネル
            $this->NewsCraw->getNewsInfoSoccerDigest();
            
            //debug($result_sk);
            $this->setNews($result_f);
            
            //mecab テストコード
            $text = $result_sk['title'];
            $exe_path = 'C:/"Program Files (x86)"/MeCab/bin/mecab.exe';
            $descriptorspec = array(
                  0 => array("pipe", "r")
                , 1 => array("pipe", "w")
            );
            $process = proc_open($exe_path, $descriptorspec, $pipes);
            if (is_resource($process)) {
                fwrite($pipes[0], $text);
                fclose($pipes[0]);
                $result = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                proc_close($process);
            }
            echo "<pre>";
            //print_r($result);
            echo "</pre>";
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