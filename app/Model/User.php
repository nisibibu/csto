<?php
//App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'AppModel', 'Model');
App::uses('AppModel', 'Model');
class User extends AppModel{

	public function beforeSave($options = array()){
		if(!$this->id){
			$passwordHasher = new SimplePasswordHasher();
			$this->data['User']['password'] = $passwordHaser->hash($this->data['User']['password']);
		}
		return true;
	}
        //Twitterのホームタイムライン取得
        function tweetHomeTimeLine($data) {
            debug($data);
            
            $tmhOAuth = new tmhOAuth(
 		  			array(
 		  				"consumer_key" => "pkOywXf82gAyn1hnpqgzShmHX",
 		  				"consumer_secret" => "ajN7sqnlvJEmqgQyaXRZ7m2bkSGWGIcsWF6ft9Alm0jmpuGlgE",
 		  				"token" => $data['auth']['credentials']['token'],
 		  				"secret" =>$data['auth']['credentials']['secret'],
 		  			)
 		  );
 		  
 		  /**
 		  * requestメソッドでTwitterAPIにリクエストを送る
 		  * ここではユーザーのホームラインを取得
 		  */
 		  $status = $tmhOAuth->request(
                          "GET", // リクエストメソッド
                           $tmhOAuth->url("1.1/statuses/home_timeline.json"), // エンドポイントを指定
                           array( "count" => 8 ) // パラメータ
                 );
 		  		//'method'=> 'GET',
 		  		//'url' => $twitter->url('1.1/statuses/user_timeline.json'),	//エンドポイントを指定
 		  		//'params' => array("count" => 8)	//パラメータ
 		  //));
 		  
                   //debug($status);
                  
                   //debug($tmhOAuth->response);
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
 		  	$data_j = json_decode($response['response']);
 		  	//debug($data_j);
                        
                        return $data_j;
                        
                        //リダイレクト（テストページ)
                        //$this->redirect(array('controller' => 'Live', 'action' => 'index'));
 		  }
 		  else{
                      //リクエスト情報が取得できなかった場合
                      return false;
                  }
        }
        
}
?>
