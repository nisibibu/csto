<?php
class TwLoginsController extends AppController{
	public $name = 'TwLogins';
	public $layout = 'default';
	public $uses = array('NewUser');
	/*認証*/
	public $components = array(
		'DebugKit.Toolbar',
		'TwitterKit.Twitter',
		'Auth' => array(	//ログイン機能を利用する
				'authenticate' => array(
					'Form' => array(
							'userModel' => 'NewUser'	//作成したnew_usersテーブルで認証する
						)
					),
					//ログイン後の移動先
					'loginRedirect' => array('controller' => 'tw_logins', 'action' => 'index/'),
					//ログアウト後の移動先
					'logoutRedirect' => array('controller' => 'tw_logins', 'action' => 'login'),
					//ログインページのパス
					'loginAction' => array('controller' => 'tw_logins', 'action' => 'login'),
					//未ログイン時のメッセージ
					'authError' => 'あなたのお名前とパスワードを入力して下さい。',
		)
	);
	
	public function beforFilter(){ //login処理の設定
		$this->Auth->allow('twitter_login', 'login', 'oauth_callback');
		$this->set('user',$this->Auth->user()); //ctpで$userが使えるようにセット
	}

	//Twitterログイン時に使用
	//twitterのOAuth用ログインURLにリダイレクト
	public function twitter_login(){
		$this->redirect($this->Twitter->getAuthenticateUrl(null, true));
	}
	
	//ログインアクション
	public function login(){
		//$this->layout = "";
	}
	
	public function logout(){
		$this->Auth->logout();
		$this->Auth->destroy(); //セッションを完全削除
		$this->Session->setFlash(__('ログアウトしました'));
		$this->redirect(array('action' => 'login'));
	}
	
	//Twitterログイン時に使用
	//トークン取得後認証処理を行う
	function oauth_callback(){
		if(!$this->Twitter->isReqested()){	//認証が実施されずにリダイレクト先から遷移してきた場合の処理
			$this->Flash(__('invalid access'), '/', 5);
			return;
		}
		$this->Twitter->setTwitterSource('twitter'); //アクセストークンの取得
		$token = $this->Twitter->getAccessToken();
		$data['NewUser'] = $this->NewUser->signin($token);	//ユーザ登録
		$this->Auth->login($data);	//CakePHPのAuthログイン処理
		$this->redirect($this->Auth->loginRedirect);	//ログイン後画面へリダイレクト
	}
	
	public function index(){}

}