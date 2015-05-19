<?php

/*
 * 各ニュースサイトをスクレイピング  
 */

//require_once 'C:\xampp\htdocs\cake\app\Vendor/autoload.php';
require_once($_SERVER['DOCUMENT_ROOT']."cake/app/Vendor/autoload.php");

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
        
        $article = array();     //記事を保持
        
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
        $article['title_list'] = $title_list;
        //debug($title_list);
        
        
        /*遷移ページのＵＲＬを取得*/
        /*Crawler(symfony2)のselectLinkを使用しての場合*/
        /*使用する時は、遷移先のURLの取得に使用*/
        $linkCrawer = $crawler->selectLink($title_list[0]);
        $link_c = $linkCrawer->link();
        $uri = $link_c->getUri();
        //debug($uri);
        
        /*Goutteライブらリを使用して次ページの遷移の場合*/
        $link_craw = $client->click($crawler->selectLink($title_list[0])->link());
        //debug($link_craw);
        
        
        /*遷移先のHTML情報を取得
         * 記事（個別）ページで情報を取得
         *          */
        
        $title; //記事のタイトル
        /*記事のタイトル取得*/
        $link_craw->filter('h1')->each(function( $node )use(&$title){
            //debug($node->text());
            $title = trim($node->text());
        });
        $article['title'] = $title;
        //debug($title);
        
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
        $article['body'] = $article_body;
        //debug($article_body);
        
        /*画像情報の取得*/
        $photo_href = array();
        $photo_src = array();
        $link_craw->filter('#ns_photo a,#ns_photo img')->each(function( $node )use(&$photo_href,&$photo_src){
            $href_flag = $node->attr('href');
            if(isset($href_flag)){
                $photo_href[] = $node->attr('href');
            }
            $src_flag = $node->attr('src');
            if(isset($src_flag)){
                $photo_src[] = $node->attr('src');
            }
            
        });
        $article['photo_href'] = $photo_href;
        $article['photo_src'] = $photo_src;
        //debug($photo_href);
        //debug($photo_src);
        
        
        $photo_info;    //画像情報の保持
        /*画像の説明取得*/
        $link_craw->filter('#ns_photo .ns_caption_bg_mid')->each(function( $node )use(&$photo_info){
            //debug($node->text());
            $photo_info = trim($node->text());
        });
        $article['photo_info'] = $photo_info;
        //debug($photo_info);
        
        /*動画情報の取得*/
        
        
        /*動画の説明取得*/
        
        
        /*関連キーワードの取得*/
        $relation_keyword = array(); //関連キーワードの保持
        $link_craw->filter('.ns_kanren')->each(function( $node )use(&$relation_keyword){
            $search = "関連キーワード：";
            $text = str_replace($search, "", $node->text());
            /*記事本文の整形処理*/
            $array = explode(",", $text); // とりあえず行に分割
            $array = array_map('trim', $array); // 各要素をtrim()にかける
            $array = array_filter($array, 'strlen'); // 文字数が0のやつを取り除く
            $array = array_values($array); 
            $relation_keyword[] = $array;
        });
        $article['relation_keyword'] = $relation_keyword;
        //debug($relation_keyword);
        
        /*関連記事の取得*/
        $relation_news = array();   //関連記事の保持
        $link_craw->filter('#ns_txt a')->each(function( $node )use(&$relation_news){
            //var_dump($node->text());
            /*関連記事の取得記述
              配列：ＵＲＬ,
             *      関連記事タイトル
             *              */
        });
        return $article;
    }
    
    
    /*フットボールチャンネルより取得
     *
     *      */
    public function getNewsInfoFootBallChannel($param = ""){
        $url = "http://www.footballchannel.jp/category/jleague/";
        
        $url = $url.$param;

        //Goutteオブジェクト生成
        //$crawer = new Goutte\Client();
        
        $news_info = array();   //返却用変数
        $temp_info = array();   //一時格納用
        //Goutteオブジェクト生成
        $client = new Goutte\Client();
        //var_dump($client);
        //totoマッチング、投票率HTMLを取得
        $crawer = $client->request('GET', $url);
        //debug($crawer);
        
        //DomCrawlerオブジェクト生成
        $craw = new Symfony\Component\DomCrawler\Crawler();
        
        $selecta_title = 'h2.contents_title > a';    //記事タイトルのCSSセレクタ
        /*記事タイトルのリスト取得*/
        $title_list = $this->getNewsTitleList($crawer, $selecta_title);
        //debug($title_list);
        
        
        /***  1ページ目  ***/
        /*次ページのCrawerオブジェクトを取得*/
        
        
        /*取得できたら情報取得*/
         //$link_craw =  $this->getNextCraw($craw, $title_list[0]);
         //var_dump($link_craw);
         
         
        /*情報取得*/
         $title = $title_list[0];
         $page = 1;
         $site = "フットボールチャンネル";
         $page_info[] = $this->getOnePageInfoFootBallCh($crawer, $title, $page, $site);
         
         /*Goutteライブらリを使用して次ページの遷移の場合*/
         $link_craw = $client->click($crawer->selectLink($title_list[0])->link());
         $next_page_str = $this->getNextTitle($link_craw, "");
         
         
         /*次ページの判定*/
         if(!isset($next_page_str)){
             //最終ページなら返却
             return $page_info;
         }
         
         /*次ページ取得*/
         //$craw = $this->getNextCraw($link_craw, $next_page_str);
         //debug($craw);
         
         $page = 2;
         $page_info[] = $this->getOnePageInfoFootBallCh($link_craw, $next_page_str, $page, $site);
         //debug($page_info);
 
        /*最終処理*/
        $news_title = $page_info[0]['news_title'];
        $result;
        foreach ($page_info as $var){
            $var['news_title'] = $news_title;
            $result[] = $var;
        }
        
        
        return $result;
    }
    
    /*1ページ分の情報を取得
     * フットボールチャンネルのみ対応
     * 
     *      */
    private function getOnePageInfoFootBallCh($crawer,$title,$page,$site){
        $uri = $this->getNextUri($crawer, $title);
        //debug($uri);
        $temp_info['uri'] = $uri;
        $temp_info['news_title'] = $title;
        
        //Goutteオブジェクト生成
        $client = new Goutte\Client();
        
        /*Goutteライブらリを使用して次ページの遷移の場合*/
        $link_craw = $client->click($crawer->selectLink($title)->link());
        
        
        /* 他サイトと同様にページの情報を取得
         *          *
         */
        
        //ページタイトルボックスの取得
        $selecta_t_box = '#title_box';
        $title_box = $this->getNewsTitlebox($link_craw,$selecta_t_box);
        $temp_info['title_box'] = $title_box;
        
        /*記事の本文の取得*/
        $article_body;  //記事の本文の取得
        $selecta_body = ".entry_body";
        
        $article_body = $this->getNewsBody($link_craw, $selecta_body);
        //debug($article_body);
        $temp_info['page_title'] = $article_body[0];
        $temp_info['body'] = $article_body;        
        
        /*タグ（関連キーワードの取得*/
        $selecta_tag = ".entry a";
        $tag_list = array();
        
        $tag_list = $this->getTag($link_craw, $selecta_tag);
        //debug($tag_list);
        
        $temp_info['tag'] = $tag_list;
        
        /*画像情報の取得*/
        $selecta_photo = ".entry_body a, .entry_body img";
                
        $photo_info = $this->getPhotoInfo($link_craw,$selecta_photo);
        //debug($photo_info);
        $temp_info['photo'] = $photo_info;
        
        /*ページを格納*/
        $temp_info['page'] = $page;
        /*サイト名を格納*/
        $temp_info['site_name'] = $site;
        
        return $temp_info;
    }
    
    /*次ページのCrawオブジェクトを取得*/
    protected function getNextCraw($craw,$title){
         //Goutteオブジェクト生成
        $client = new Goutte\Client();
        
        /*Goutteライブらリを使用して次ページの遷移の場合*/
        $link_craw = $client->click($craw->selectLink($title)->link());
        
        return $link_craw;
    }
    
    /*ゲキサカからニュースを取得*/
    public function getNewsInfoGekiSaka($param = "news/category?category=domestic"){
        $url = "http://web.gekisaka.jp/";
        $url = $url.$param;
    }
    
    /*サッカーダイジェストＷｅｂからニュースを取得
     * カテゴリー：jリーグ
     *      */
    public function getNewsInfoSoccerDigest($param = "tag_list/tag_search=1&tag_id=50"){
        $url = "http://www.soccerdigestweb.com/";
        $url = $url.$param;
        
        $param = array();   //スクレイピング取得用
        $param['title_list'] = ".entry";
        $param['link'] =".entry a";
        
        //Goutteオブジェクト生成
        $client = new Goutte\Client();
        //var_dump($client);
        //totoマッチング、投票率HTMLを取得
        $crawler = $client->request('GET', $url);
        
        $title_list = $this->getItemByCraw($crawler, $param['title_list'],'text');
        $temp_list = $this->getItemByCraw($crawler, $param['link'],'link');
        $temp_list = array_unique($temp_list);
        $link_list = array_values($temp_list);
        
        debug($link_list);
    }
    
    
    /*セレクタを指定して項目を取得して返す*/
    public function getItemByCraw($crawler,$selecta,$type){
        $item = array();
        
        if($type === 'text'){            
            $crawler->filter($selecta)->each(function( $node )use(&$item){
            //debug($node->text());
            $text = trim($node->text());
            /*記事本文の整形処理*/
            $array = explode("\n", $text); // とりあえず行に分割
            $array = array_map('trim', $array); // 各要素をtrim()にかける
            $array = array_filter($array, 'strlen'); // 文字数が0のやつを取り除く
            $array = array_values($array); //キーを連番に振りなおし
            //$array = $this->fixDataBlank($array);
            $item[] = $array;
            });
        }else if($type === 'link'){
            $crawler->filter($selecta)->each(function( $node )use(&$item){
            //debug($node->text());
            $link = $node->attr('href');
            $item[] = $link;
            });
        }
        
        //debug($title_box); 
         
        return $item;
    }
    
    /*記事タイトルの一覧を取得して返す*/
    public function getNewsTitleList($crawer,$selecta_title){
        $title_list = array();        //記事タイトルリストの保持
        //debug($crawler);
        
        $crawer->filter($selecta_title)->each(function( $node )use(&$title_list){
            //debug($node->text());
            $title_list[] = trim($node->text());
        });
         
         
        //debug($title_list);
        return $title_list; 
    }
    
    /*タグ（関連キーワードの取得*/
    public function getTag($crawer,$selecta_tag){
        $tag_list = array();
        $crawer->filter($selecta_tag)->each(function( $node )use(&$tag_list){
            $tag_list[] = $node->text();
        });
        //debug(strlen($tag_list[5]));
        $tag_list = array_filter($tag_list,"strlen");
        $tag_list = array_values($tag_list);
        
        return $tag_list;
    }


    /*記事のタイトルボックス（タイトルと付随文）を取得*/
    public function getNewsTitlebox($crawler,$selecta){
        //次のページのタイトルを取得
        //debug($crawler);
        $title_box = array();
        $crawler->filter($selecta)->each(function( $node )use(&$title_box){
            //debug($node->text());
            $text = trim($node->text());
            /*記事本文の整形処理*/
            $array = explode("\n", $text); // とりあえず行に分割
            $array = array_map('trim', $array); // 各要素をtrim()にかける
            $array = array_filter($array, 'strlen'); // 文字数が0のやつを取り除く
            $array = array_values($array); // これはキーを連番に振りなおし
            //$array = $this->fixDataBlank($array);
            $title_box[] = $array;
        });
        //debug($title_box); 
         
        return $title_box;
    }
    
    /*次ページのタイトルを取得*/
    public function getNextTitle($crawer,$selecta_next){
        $title;        //次ページのタイトル取得
        //debug($crawler);
        $selecta_next = ".nextpage a";
        
        $crawer->filter($selecta_next)->each(function( $node )use(&$title){
            //debug($node->text());
            $title = trim($node->text());
        });
         
         
        //debug($title);
        return $title; 
    }
    
    /*空白のデータを削除して配列を整列しなおす
     *  修正する
     *      */
    public function fixDataBlank($result){
         
        /*0のデータを消さないようにする*/
         function even($var){
                  return ($var<> '');
         }
         
         //改行無しのスペース(＆ｎｂｓｐ；)を半角スペースに置換して削除
         //参考URL
         //http://nanoappli.com/blog/archives/5429
         for ($i = 0 ; $i < count($result); $i++){
                  $result[$i] = trim( $result[$i], chr(0xC2).chr(0xA0) );
         }
         
         
         //取得したデータから空の部分を詰めて配列添え直し
          $result =  array_filter($result,'even');
          $result = array_values($result);
          

          return $result;
    }
    
    /*ページ本文の取得*/
    public function getNewsBody($craw,$selecta_body){
        $article_body;  //記事の本文の取得

        $craw->filter($selecta_body)->each(function( $node )use(&$article_body){
            //debug($node->text());
            $text = trim($node->text());
            /*記事本文の整形処理*/
            $array = explode("\n", $text); // とりあえず行に分割
            $array = array_map('trim', $array); // 各要素をtrim()にかける
            $array = array_filter($array, 'strlen'); // 文字数が0のやつを取り除く
            $array = array_values($array); // これはキーを連番に振りなおし
            $article_body = $array;
        });
        
        return $article_body;
    }
    
    
    /*次のページのＵＲＩを取得
     * $crawer Craerオブジェクト
     * $title  タイトルの文字列
     *      */
    public function getNextUri($crawer,$title){
        $linkCrawer = $crawer->selectLink($title);
        $link_c = $linkCrawer->link();
        $uri = $link_c->getUri();
        
        return $uri;
    }
    
    /*画像情報の取得*/
    public function getPhotoInfo($craw,$selecta_phpto){
        $photo_href = array();
        $photo_src = array();
        
        //$selecta_photo = ".entry_body a, .entry_body img";
        
        $craw->filter($selecta_phpto)->each(function( $node )use(&$photo_href,&$photo_src){
            $href_flag = $node->attr('href');
            if(isset($href_flag)){
                $photo_href['href'][] = $node->attr('href');
            }
            $src_flag = $node->attr('src');
            if(isset($src_flag)){
                $photo_src['src'][] = $node->attr('src');
            }           
        });
        $photo_info = array_merge($photo_href, $photo_src);
        
        return $photo_info;
    }
    
    /*最終ページか判定する
     * 最終ページの場合はTRUEを返す
     *      */
    public function judgmentLastPage($next_page){
        $flag = FALSE;
        if(!isset($next_page)){
            //最終ぺージ
            $last = TRUE;
        }
        return $flag;
    }
}
?>