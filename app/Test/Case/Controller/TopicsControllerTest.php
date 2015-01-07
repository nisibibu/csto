<?php
App::uses('TopicsController', 'Controller');

/**
 * TopicsController Test Case
 *
 */
class TopicsControllerTest extends ControllerTestCase {
	
/**
 * @expectedException NotFoundExcepton
 * @expectedExceptionMessage Invalid topic
 */
	
	public function testNotFoundError()
	{
		$this->testAction('/topics/view/999');
	}
	
	public function test削除が成功したらindexにリダイレクトする(){
		$this->testAction('/topics/delete/1', array('method' => 'post'));
		$this->assertRegExp('/topics$', $this->headers['Location']);
	}
	
	public function test新しいトピックを実装する(){
		$data = array('Topic' => array('title' =>'新しいトピックタイトル'));
		$this->testAction('/topics/add',
					array('data' => $data, 'method' => 'post'));
		$this->assertContains('The topic has been saved',
			$this->controller->Session->read('Message.flash'));
	}

}
