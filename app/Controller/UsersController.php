<?php
	// app/Controller/UserController.php
	//APP::import('Vender', 'tmhUtilities');

        //APP::import('Vender', 'tmhoauth');
        //APP::uses('tmhOAuth', 'Vender');

class UsersController extends AppController{
            public $uses = array('User','Live');   //Userモデルを指定
            
		/*
		public function beforeFilter() {
    		Security::setHash('sha256');
   			$this->Auth->allow('login');
    		$this->Auth->allow('logout');
    		// $this->Auth->allow('add'); //ユーザ登録を制限しない場合はコメントをはずす
  		}
		*/
            
            
		public function index(){
			//debug($this->data);
		}
		
                //Controller Class 
                var $name = "Api";
 
		
		public function login(){
			if($this->request->is('post')){
				if($this->Auth->login()){
					//2.4以降はredirectUrl()を使用する
					return $this->redirect($this->Auth->redirectUrl());
				}else{
					$this->Session->setFlash(
						'Username or password is incorrect'
					);
				}
			}
		}
		public function logout(){
			$this->Auth->logout();
			return $this->redirect('/');
		}
	
                public function search(){

                    //$this->autoLayout = false;
                    $options = array('q'=>'soccer','count'=>'3','lang'=>'ja');

                    echo $this->TwitterOAuth->OAuthRequest(
                        'https://api.twitter.com/1.1/search/tweets.json',
                        'GET',
                        $options
                    );
                    exit;

    }
            
        //test
        function complete()
        {
            debug($this->request->data);
            //$this->autoRender = false;
        }
    
        /*Twitter認証後リダイレクトされてくる*/
	public function opauthComplete() {
 		  debug($this->data);
 		  
 		  //$tmhUtil = new tmhUtilities();
		  //$here = $tmhUtil->php_self();
 		  
                  //debug($this->data);
                  //$data_twitter_token = $this->data;
                  //$this->Live->setTweeetToken($data_twitter_token);
                  //$t = $this->Live->getTweet();
                  
                  //$debug($t);
                  
                  //$tweitter_data_info = $this->data['auth']['info'];
                  
                  //$this->Live->setTest($tweitter_data_info);
                  //$t_test = $this->Live->getTest();
                  //debug($t_test);
                  ////$data_j[] = $this->User->twitter_timeline($data);
                  //$this->redirect(array('controller' => 'lives', 'action' => 'index'));
                  //debug($data_j);
                  
                  /**
 		  * tmhOAuthを初期化
 		  * コンストラクタの引数はトークンをまとめた連想配列
 		  */
                  
                  //SessionへTwitterトークン情報を登録
                  $this->Session->write('twitter_token',$this->data);
            
                  /*
 		  $tmhOAuth = new tmhOAuth(
 		  			array(
 		  				"consumer_key" => "pkOywXf82gAyn1hnpqgzShmHX",
 		  				"consumer_secret" => "ajN7sqnlvJEmqgQyaXRZ7m2bkSGWGIcsWF6ft9Alm0jmpuGlgE",
 		  				"token" => $this->data['auth']['credentials']['token'],
 		  				"secret" =>$this->data['auth']['credentials']['secret'],
 		  			)
 		  );
 		  */
                  $tmhOAuth = new tmhOAuth(
 		  			array(
 		  				"consumer_key" => "pkOywXf82gAyn1hnpqgzShmHX",
 		  				"consumer_secret" => "ajN7sqnlvJEmqgQyaXRZ7m2bkSGWGIcsWF6ft9Alm0jmpuGlgE",
 		  				"token" =>'2537332975-vf1gxhneO73mBA5gfk7zx0XvemitEvF46aGQqdk',
 		  				"secret" =>'uM7isfBEUZmDRQ9IdD6pEgiKCNhYRdd2e1eYT8Pmdyk0r',
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
 		  	$data_j = $this->twitter_timelne = json_decode($response['response'],TRUE);
 		  	//debug($data_j['user']);
                        $this->Session->write('hometime_line', $data_j);
                        //リダイレクト
                       $this->redirect(array('controller' => 'lives', 'action' => 'index'));
 		  }
 		  else{
                      echo "リクエストの値が取得できませんでした";
                  }
                  
	}
}
       
/*   
App::uses('AppController', 'Controller');
class UsersController extends AppController{
    public function beforeFilter() {
        parent::beforeFilter();
        $this-&gt;Auth-&gt;allow('twitter_login', 'login', 'oauth_callback');
    }
    public function twitter_login() {
    	Configure::write('debug', 0);
    	$this-&gt;layout = 'ajax';
    	$this-&gt;Twitter-&gt;setTwitterSource('twitter');
    	pr($this-&gt;Twitter-&gt;getAuthenticateUrl(null, true));
    	$this-&gt;redirect($this-&gt;Twitter-&gt;getAuthenticateUrl(null, true));
    }
    public function login() {
    }
	public function logout() {
		$this-&gt;Session-&gt;destroy();
		$this-&gt;Session-&gt;setFlash(__('Signed out'));
		$this-&gt;Session-&gt;delete($this-&gt;Auth-&gt;sessionKey);
		$this-&gt;redirect($this-&gt;Auth-&gt;logoutRedirect);
	}
	public function index() {
		$this-&gt;User-&gt;recursive = 0;
		$this-&gt;set('users', $this-&gt;paginate());
	}

	function oauth_callback() {
		if (!$this-&gt;Twitter-&gt;isRequested()) {
			$this-&gt;flash(__('invalid access.'), '/', 5);
			return;
		}
		$this-&gt;Twitter-&gt;setTwitterSource('twitter');
		$token = $this-&gt;Twitter-&gt;getAccessToken();
		if (is_string($token)) {
			$this-&gt;flash(__('fail get access token.') . $token, '/', 5);
			return;
		}
		$data['User'] = array(
	        'id' =&gt; $token['user_id'],
			'username' =&gt; $token['screen_name'],
	        'password' =&gt; Security::hash($token['oauth_token']),
	        'oauth_token' =&gt; $token['oauth_token'],
	        'oauth_token_secret' =&gt; $token['oauth_token_secret'],
		);
		if (!$this-&gt;User-&gt;save($data)) {
			$this-&gt;flash(__('user not saved.'), 'login', 5);
			return;
		}
		$this-&gt;Auth-&gt;login($data);
		$this-&gt;redirect($this-&gt;Auth-&gt;loginRedirect);
	}
}
*/
?>