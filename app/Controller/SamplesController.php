<?php

// app/Controller/SamplesController.php
App::uses( 'CakeEmail', 'Network/Email');

class SamplesController extends AppController {
	
	public function index(){
		//CakeEmailクラスのインスタンス生成
		$email = new CakeEmail('default');
		
		//デバッグ機能を利用するよう設定
		$email->transport('Mail');
		
		$email->from('trialanderror685172@hotmail.com');
		$email->to('trialanderror685172@hotmail.com');
		$email->subject('これはテストメールです');
		$messages = $email->send('これはテストメールの本文です');
		
		$this->set('messages', $messages);
	}

}