<?php
// app/Controller/SampleController.php
class SampleController extends AppController{
	//Demoコンポーネントを設定
	public $components = array('Demo');
	
	//Demoヘルパーを設定
	public $helpers = array('Demo');
	
	public function index(){
		//DemoコンポーネントのHelloメソッドを利用
		$massage = $this->Demo->hello();
		$this->Session->setFlash($message);
		$this->redirect(array(
			'controller' => 'posts',
			'action' => 'index'
		));
	}
	
	public function test(){
	
	}

}