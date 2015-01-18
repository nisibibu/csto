<?php

/*
  Liveコントローラー
 *  Twitter
 *  Instgram
 *  Youtube
 *  RSS
 *  Toto
 *  TV放送
 *  試合経過、結果
 *  */

App::uses('Folder','Utility');
App::uses('File', 'Utility');

//Goutteの読み込み
//require_once '../Vendor/goutte/goutte.phar';
use Goutte\Client;

class LivesController extends AppController{
    
    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
    //var $name = "Api";
    /*コンポーネントの指定*/
    public $components = array('Twitter','Toto','Rss');

    public $timeline = array();    
    
    //use Goutte\Client;
    
    public function index(){
        //ここで情報を処理 
       //処理記述したら共通化してコンポーネントにする
        
        /*PHPCrawer 使用してのクローリング*/
        //$c_result = $this->Crawl->getCrawlResult();
        //debug($c_result);
        
        
        /*Toto投票率の取得*/
       

        //今回のtotoマッチングと投票率の取得
        $toto_vote = $this->Toto->getTotoVote();
        //debug($toto_vote);
        
        //リンクを全て表示（toto投票率取得元）
        $links = $this->Toto->getPastTotoVote();
        debug($links);
        
        //Totoコンポーネントを使用してmodelクラスのsaveメソッド呼び出し
        //$this->Totovote->setTotoDb();
        
        /*RSSコンポーネントテスト*/
        $feed = "http://web.gekisaka.jp/feed";
        $output = $this->Rss->read($feed);
        //debug($output);
        
        /** Userコントローラーから情報を受け取る 
         * 
         **/
        //Sessionからトークン情報を取得
        $twitter_token = $this->Session->read('twitter_token');
        //debug($twitter_token);
        
        //tmhOuthオブジェクトの生成
        $tmhOAuth = $this->Twitter->createTmhOAuth($twitter_token);
        //debug($tmhOauth);
        //twitteroauhtオブジェクトの生成
        $twitteroauth = $this->Twitter->createTwitterOauth($twitter_token);
        
        
       /*Twitterの検索結果を取得*/
       $s_result =$this->Twitter->searchTwtter($tmhOAuth, 'セレッソ大阪', 8);
       //debug($s_result);
       $s_result = $s_result['statuses'];
       //debug($s_result);
       $search_result = array();
       //検索結果から情報の取り出し
       foreach($s_result as $var){
           $search_result['text'][] = $var['text'];
           $search_result['name'][] = $var['user']['name'];
       }
       //debug($search_result['text']);
       //debug($search_result['name']);
       
       /*Twitter ハッシュタグ検索 */
       //ファイルの読み込み
       
       /*ファイルディレクトリ探査サンプル*/
       //$dir = new Folder(APP,'path/');
       //debug($dir->read());
       
       
       //ハッシュタグのファイルを読み込み
       /* File クラス使用の場合 */
       ////$file = new File(APP.'\Text/team_hashtag.txt');
       //$file_info = $file->read();
       //debug($file_info);
       //$file->close();
       
       /* ハッシュタグ取得（ファイル） */
       $hashtag = $this->Twitter->getTeamHashtag();
       
       $team_name = "セレッソ大阪";
       $hashtag_result = array();   //ハッシュタグ結果保持
       //ハッシュタグ検索処理
       $h_result = $this->Twitter->searchHashtag($tmhOAuth, $hashtag[$team_name], 8);
       //ハッシュタグの取得結果から情報取り出し
       //debug($h_result);
       $h_result = $h_result['statuses'];
       
       //検索結果から情報の取り出し
       $hashtag_result = $this->Twitter->getRequestFormatting($h_result);

       //debug($hashtag_result);
       
       /*特定ユーザーのタイムラインを取得*/
       $user_timeline_result = array();
       $screenname = "SoccerKingJP";
       $u_result = $this->Twitter->searchUserTimeline($tmhOAuth, $screenname, 8);
       //debug($u_result);
       
       /*画像付のツイートを検索、取得*/
       $count = 100;
       
       /*メタ情報を含むすべてのツイートを取得*/
       $search_meta_result = array();
       $m_result = $this->Twitter->searchWithEntities($tmhOAuth, $hashtag[$team_name], $count);
       //$m_result = $m_result->statuses;
       
       //$m_result_array = $this->stdclass_to_array($m_result);
       //$m_result_array = $this->obj2arr($m_result);
       //debug($m_result);
       $m_result = $m_result['statuses'];
       
       foreach ($m_result as $var){
           $search_meta_result['text'][] = $var['text'];
           $search_meta_result['name'][] = $var['user']['name'];
           $search_meta_result['entities'][] = $var['entities'];
           
       }
       //debug($search_meta_result);
       
       //debug($search_meta_result);
       /*レスポンスからメタ情報から画像付きのものだけ取得 */
       $search_photo_result = array();   //ハッシュタグ結果保持
       $p_result = $this->Twitter->searchWithEntities($tmhOAuth, $hashtag[$team_name], $count);
       $p_result = $p_result['statuses'];
       
       foreach ($p_result as $var){
            if(array_key_exists('media', $var['entities'])){
              $search_photo_result['text'][] = $var['text'];
              $search_photo_result['name'][] = $var['user']['name'];
              $search_photo_result['entities'][] = $var['entities'];
              $search_photo_result['media'][] = $var['entities']['media'];
            }
       }
       //debug($search_photo_result);
        //  
       
       /* Twitterのホームラインを取得*/
       $timeline = $this->Session->read('hometime_line');
       //debug($timeline);
      
       
       if($timeline){
            //ホームラインの取得結果から情報取り出し
            foreach ($timeline as $val){
                $timeline['text'][] = $val['text'];
                $timeline['name'][] = $val['user']['name'];
            }
            //debug($timeline['text']);
            //debug($timeline['name']);
       }
    }
    
    /*stdClassオブジェクト→配列変換*/
    //参考サイト
    //http://suin.asia/2009/03/09/cast-object-to-array.html
    function obj2arr($obj){
        if(!is_object($obj)){
            //debug($obj);
            return $obj;
        }
        $arr = (array)$obj;
        foreach ($arr as &$a){
            $a = $this->obj2arr($a);
        }
        //debug($arr);
        return $arr;
    }
    
    //参考サイト
    //http://coneta.jp/1857.html
    function stdclass_to_array($objDATA) {
 
	$arrayDATA = (array) $objDATA;
	$arrayKeys = array_keys($arrayDATA);
	$intCount = count($arrayKeys);
 
	for($i = 0; $i < $intCount; $i++){
            $arrayDATA = (array)$arrayDATA;
        }
        return $arrayDATA;
    }

                
    public function opauthComplete() {
 		  
 	 /**
         * requestメソッドでTwitterAPIにリクエストを送る
 		  * ここではユーザーのホームラインを取得
 	 */
                  
 		  $status = $tmhOAuth->request(
                          "GET", // リクエストメソッド
                           $tmhOAuth->url("1.1/statuses/home_timeline.json"), // エンドポイントを指定
                           array( "count" => 8 ) // パラメータ
                 );

                  /**
 		  * requestの返り値はHTTPのステータスコード
 		  *
 		  */
                  
 		  if($status == 200){
 		  	/**
 		  	* データはメンバのresponseの中に、
 		  	* さらに生のデータはその中の"response"の中にJSON形式で格納されている
 		  	*/
                  
 		  	$response = $tmhOAuth->response;
                        //debug($response['response']);
 		  	$this->twitter_timelne = json_decode($response['response']);
 		  	//debug($data_j);
                        
 		  }
 		  else{
                      echo "リクエストの値が取得できませんでした";
                  }
    }    
}