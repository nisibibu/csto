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
     * Goutteについての包括的なサイト
     * http://d.hatena.ne.jp/hnw/20120115
     * 
     * Goutteを使用してリンクをクリックなどについて参考
     * http://qiita.com/77web@github/items/3cd3b56985d5c6845661
     *  
     * Symfony\Component\DomCrawler\Crawler について参考
     * http://docs.symfony.gr.jp/symfony2/components/dom_crawler.html
     */
    public function getNewsInfoSoccerKing($param=""){
        $url = "http://www.soccer-king.jp/news/japan/jl".$param;
        
        $title_list = array();  //記事タイトルの保持
        
        //Goutteオブジェクト生成
        $client = new Goutte\Client();
        //var_dump($client);
        //totoマッチング、投票率HTMLを取得
        $crawler = $client->request('GET', $url);
        //var_dump($crawler);
        
        //DomCrawlerオブジェクト生成
        $craw = new Symfony\Component\DomCrawler\Crawler();
       
        //ニュースのタイトル一覧を取得
        $crawler->filter('.h3_title')->each(function( $node )use(&$title_list){
            //debug($node->text());
            $title_list[] = trim($node->text());
        });
        
        //debug($title_list);
        
        /*Goutteライブらリを使用して次ページの遷移の場合*/
        $link_craw = $client->click($crawler->selectLink($title_list[0])->link());
        //debug($link_craw);
        
        /*Crawler(symfony2)のselectLinkを使用しての場合*/
        /* こちらは未使用（使用する時は、上記ＵＲＬ参照）
        $linkCrawer = $crawler->selectLink($title_list[0]);
        $link_c = $linkCrawer->link();
        $uri = $link_c->getUri();
        var_dump($uri);
         * * 
         */
        
        
        /*遷移先のHTML情報を取得
         * 記事（個別）ページで情報を取得
         *          */
        $title; //記事のタイトル
        /*記事のタイトル取得*/
        $link_craw->filter('h1')->each(function( $node )use(&$title){
            //debug($node->text());
            $title = trim($node->text());
        });
        debug($title);
        
        /*記事の本文の取得*/
        $article_body;  //記事の本文の取得
        $link_craw->filter('#ns_txt')->each(function( $node )use(&$article_body){
            //debug($node->text());
            $text = trim($node->text());
            /*記事本文の整形処理*/
            $array = explode("\n", $text); // とりあえず行に分割
            $array = array_map('trim', $array); // 各要素をtrim()にかける
            $array = array_filter($array, 'strlen'); // 文字数が0のやつを取り除く
            $array = array_values($array); // これはキーを連番に振りなおし
            $article_body = $array;
        });
        debug($article_body);
        
        /*画像情報の取得*/
        $photo_href = array();
        $photo_src = array();
        $link_craw->filter('#ns_photo a,#ns_photo img')->each(function( $node )use(&$photo_href,&$photo_src){
            //debug($node->text());
            $photo_href[] = $node->attr('href');
            $photo_src[] = $node->attr('src');
        });
        debug($photo_href);
        debug($photo_src);
        
        /*画像の説明取得*/
        
        /*動画情報の取得*/
        
        /*動画の説明取得*/
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