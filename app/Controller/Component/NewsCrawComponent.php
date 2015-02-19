<?php

/*
 * 各ニュースサイトをスクレイピング  
 */

require_once 'C:\xampp\htdocs\cake\app\Vendor/autoload.php';


App::uses('Component', 'Controller');
class NewsCrawComponent extends Component{

    public $uses = array('POST','Live','Totovote','Teamtrendgoal');    //使用するモデルを宣言
    public $components = array('News'); 
    
    
    /*サッカーキングより取得
     *  Jリーグの情報を取得（http://www.soccer-king.jp/news/japan/jl)
     *  
     * Symfony\Component\DomCrawler\Crawler について参考
     * http://docs.symfony.gr.jp/symfony2/components/dom_crawler.html
     */
    public function getNewsInfoSoccerKing($param=""){
        $url = "http://www.soccer-king.jp/news/japan/jl".$param;
        
        $title_list = array();  //記事タイトルの保持
        
        //Goutteオブジェクト生成
        $client = new Goutte\Client();

        //totoマッチング、投票率HTMLを取得
        $crawler = $client->request('GET', $url);
        
        //DomCrawlerオブジェクト生成
        $craw = new Symfony\Component\DomCrawler\Crawler();
        
        //debug($crawler_trend); 
       
        //ニュースのタイトル一覧を取得
        $crawler->filter('.h3_title')->each(function( $node )use(&$title_list){
            //debug($node->text());
            $title_list[] = trim($node->text());
        });
        //debug($title_list);
        
        $link = $client->click($crawler->selectLink($title_list[2])->link());
        debug($link);
        //$uri = $link->getUri();
        //debug($uri);
    }
    
    
    /*フットボールチャンネルより取得
     *
     *      */
    public function getNewsInfoFootBallChannel($param){
        $url = "http://www.footballchannel.jp/hotnews/hotnews-japan/";

        //Goutteオブジェクト生成
        $crawer = new Goutte\Client();
        
    }
}
?>