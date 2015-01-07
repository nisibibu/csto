<?php

// app/Test/Case/Model/TopicTest.php

App::uses('Topic', 'Model');	//テスト対象のクラスを宣言

class TopicTest extends CakeTestCase{
	//フィクスチャを利用する宣言。テストデータを自動的にロードする。
	//テスト対象のモデルだけでなく、関連モデルのテストデータも利用
	public $fixtures = array(
		'app.topic',
		'app.category',
		'app.comment'
	);
	
	//テストケースの前に呼ばれる処理
	public function setUp(){
		//親クラス(CakeTestCaseのsetupメソッド呼び出し）
		parent::setUp();
		$this->Topic = ClassRegistry::init('Topic');
	}
	
	//テストケースの後に呼ばれる処理
	public function tearDown(){
		unset($this->Topic);
		//親クラス(CakeTestCaseのteardownメソッド呼び出し）
		parent::tearDown();
	}


	public function testタイトルは必須入力である(){
	//createメソッドでモデルに値をセットする
	$this->Topic->create(array('Topic' => array('title'=>'')));
	//validatesメソッドでモデルの値をチェックする
	$this->assertFalse($this->Topic->validates());
	//バリデーションエラーになった項目がないかチェック
	//$this->assertArrayHashKey('title',$this->Topic->validationErrors);
	}
	
	public function test最新5件が取得できること(){
		//$latests = $this->Topic->getLatest();
		//$this->assertCount(5,$latests);
		
		$this->assertCount(3, array(100, 200));
		
	
	}
	
}
?>