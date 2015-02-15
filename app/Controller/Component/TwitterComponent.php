<?php

/* 
  Twitter関連のコンポーネント
 *  */

require_once '../Vendor/twitteroauth/twitterOAuth.php';

class TwitterComponent extends Component{
    //tmhOAthインスタンス生成して返す
    public function createTmhOAuth($data){
        $tmhOAuth = new tmhOAuth(
 		array(
 		  	"consumer_key" => "pkOywXf82gAyn1hnpqgzShmHX",
                        "consumer_secret" => "ajN7sqnlvJEmqgQyaXRZ7m2bkSGWGIcsWF6ft9Alm0jmpuGlgE",
 		  	"token" => $data['auth']['credentials']['token'],
                        "secret" =>$data['auth']['credentials']['secret'],
 		  )
 	);
        return $tmhOAuth;
    }
    
    public function createTwitterOauth($data){
        $twitterOAuth = new TwitterOAuth("pkOywXf82gAyn1hnpqgzShmHX",
                                        "pkOywXf82gAyn1hnpqgzShmHX",
                                        $data['auth']['credentials']['token'],
                                        $data['auth']['credentials']['secret']);
        
        return $twitterOAuth;
    }
    
    //特定ユーザーのタイムラインを取得
    public function searchUserTimeline($tmhOAuth, $screenname,$count){
       
         $status = $tmhOAuth->request(
                "GET", // リクエストメソッド
                 $tmhOAuth->url("1.1/statuses/user_timeline.json"), // エンドポイントを指定
                 array('screen_name' => urldecode($screenname),
                     'count' => $count,
                      ));

        /**
        * requestの返り値はHTTPのステータスコード
        */

        if($status == 200){
              /**
              * データはメンバのresponseの中に、
              * さらに生のデータはその中の"response"の中にJSON形式で格納されている
              */

              $response = $tmhOAuth->response;
              //debug($response['response']);
              $data_j = json_decode($response['response'],true);
              //debug($data_j);
              return $data_j;
        }
        else{
            echo 'リクエストを取得できませんでした';
        }
    }
    
    //エンティティ情報付きのツイート検索用メソッド
    public function searchWithEntities($tmhOAuth, $hashtag,$count){
       $hashtag_1; //検索に使用するハッシュタグ
        
        if(count($hashtag) > 1){
            //指定したハッシュタグが複数ある場合
            //1つ目のハッシュタグを指定
            $hashtag_temp = $hashtag[0];
        }else if(count($hashtag) === 0 ){
            //ハッシュタグを取得できなかった場合
            return false;
        }else{
            //ハッシュタグを取得できた場合
            $hashtag_1 = $hashtag[0];
        }
         $status = $tmhOAuth->request(
                "GET", // リクエストメソッド
                 $tmhOAuth->url("1.1/search/tweets.json"), // エンドポイントを指定
                 array('q' => urlencode($hashtag_1),
                     'count' => $count,
                     'include_entities' => '1'));

        /**
        * requestの返り値はHTTPのステータスコード
        */

        if($status == 200){
              /**
              * データはメンバのresponseの中に、
              * さらに生のデータはその中の"response"の中にJSON形式で格納されている
              */

              $response = $tmhOAuth->response;
              //debug($response['response']);
              $data_j = json_decode($response['response'],true);
              //debug($data_j);
              return $data_j;
        }
    }
    
    /*Twitter 検索結果取得用メソッド
     * $tmhOAuth tmhOAuthインスタンス
     * $hashtag 検索キーワード
     * $count 取得件数
     *  */
    public function searchHashtag($tmhOAuth, $hashtag, $count){
        /**
         * requestメソッドでTwitterAPIにリクエストを送る
         * Twitterの検索結果を取得
 	 */
        
        $hashtag_1; //検索に使用するハッシュタグ
        
        if(count($hashtag) > 1){
            //指定したハッシュタグが複数ある場合
            //1つ目のハッシュタグを指定
            $hashtag_temp = $hashtag[0];
        }else if(count($hashtag) === 0 ){
            //ハッシュタグを取得できなかった場合
            return false;
        }else{
            //ハッシュタグを取得できた場合
            $hashtag_1 = $hashtag[0];
        }
        
        /*ハッシュタグ（ワード）に先頭#が含まれているか
         * 先頭の#が含まれている場合、#を取り除く
         */
        /*  使用しないのでコメントアウト   */
        //if(preg_match('/^#.*/', $hashtag_1)){
        //    先頭の#を取り除く
        //    $temp = preg_replace('/^\w.*/', $hashtag_1,$hashtag_1);
        //    $hashtag_1 = str_replace('#', '', $hashtag_1);
        //
        //}
        
        //debug($hashtag_1);
        
        $status = $tmhOAuth->request(
                "GET", // リクエストメソッド
                 $tmhOAuth->url("1.1/search/tweets.json"), // エンドポイントを指定
                 array('q' => urlencode($hashtag_1), 'count' => $count));

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
              $data_j = json_decode($response['response'],true);
              //debug($data_j);
              return $data_j;
        }
        else{
            echo "リクエストの値が取得できませんでした";
        }

    }
    
    /*Twitter 検索結果取得用メソッド
     * $tmhOAuth tmhOAuthインスタンス
     * $keyword 検索キーワード
     * $count 取得件数
     *  */
    public function searchTwtter($tmhOAuth, $keyword, $count){
        /**
         * requestメソッドでTwitterAPIにリクエストを送る
         * Twitterの検索結果を取得
 	 */
                  
        $status = $tmhOAuth->request(
                "GET", // リクエストメソッド
                 $tmhOAuth->url("1.1/search/tweets.json"), // エンドポイントを指定
                 array('q' => urlencode($keyword), 'count' => $count));

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
              $data_j = json_decode($response['response'],true);
              //debug($data_j);
              return $data_j;
        }
        else{
            echo "リクエストの値が取得できませんでした";
        }

    }
    
    /*ファイルからチームハッシュタグの取り出し
　　 * ファイルから取得からDBから取得するよう変更する
     * teamテーブル作成し、データを取得してくるようにする
     *      */
    public function getTeamHashtag(){
    $handle = fopen(APP.'/Text/team_hashtag.txt','r');
       $hashtag_temp = array();
       $hashtag = array();
       
       $count = count(file(APP.'/Text/team_hashtag.txt'));
       //debug($count);
       
       for($i = 0; $i < $count; $i++){
           $hashtag_temp[] = fgets($handle);
       }
       fclose($handle); //ファイルを閉じる
       
       //ファイルから取得した情報でハッシュタグを取得
       for($i = 0; $i < count($hashtag_temp); $i++){
           $temp = split(',', $hashtag_temp[$i]);
           $hashtag[$temp[0]] = $temp;
           //連想配列の中身からチーム名を除く（先頭)
           //「キー」→「ハッシュタグ」の配列にする
           unset($hashtag[$temp[0]][0]);
           $hashtag[$temp[0]] = array_merge($hashtag[$temp[0]]);    //添え字を詰める
           
       }
       return $hashtag;
    }
    
    //リクエストからのデータを整形して配列で返す
    public function getRequestFormatting($result){
        $result_format = array();
        foreach($result as $var){
           $result_format['text'][] = $var['text'];
           $result_format['name'][] = $var['user']['name'];
           $result_format['screen_name'][] = $var['user']['screen_name'];
           $result_format['created_at'][] = $var['created_at'];
       }
       return $result_format;
    }
}
