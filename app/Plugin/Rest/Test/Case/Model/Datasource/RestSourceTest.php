<?php

App::uses('AppModel', 'Model');

class RestTestModel extends AppModel {

	public $name = 'RestTestModel';

	public $useDbConfig = 'test_rest';

	public $useTable = false;

	public $request = array();

}

ConnectionManager::create('test_rest', array(
	'datasource' => 'Rest.RestSource',
	'database' => false,
));

/**
 * @property RestTestModel $Model
 */
class RestSourceTestCase extends CakeTestCase {

	public function setUp() {
		parent::setUp();
		$this->Model = ClassRegistry::init('RestTestModel');
		$this->DataSource = ConnectionManager::getDataSource('test_rest');
	}

	public function tearDown() {
		unset($this->Model);
		parent::tearDown();
		ob_flush();
	}

/**
 * read json data
 */
	public function testReadJsonData() {
		$this->Model->request = array(
			'uri' => array(
				'host' => 'ip.jsontest.com',
				'path' => 'example/text',
				'query' => array('service' => 'echo'),
			),
		);

		$results = $this->Model->find('all');

		$this->assertArrayHasKey('example', $results);
	}

/**
 * read xml data
 */
	public function testReadXmlData() {
		$this->Model->request = array(
			'uri' => array(
				'host' => 'bakery.cakephp.org',
				'path' => 'articles.rss',
			),
		);

		$results = $this->Model->find('all');

		$this->assertArrayHasKey('rss', $results);
	}

/**
 * testLog method
 *
 * @outputBuffering enabled
 * @return void
 */
	public function testLog() {
		// clear log
		$this->Model->getDataSource()->getLog();

		$this->Model->request = array(
			'uri' => array(
				'host' => 'search.twitter.com',
				'path' => 'search.json',
				'query' => array('q' => 'twitter'),
			),
		);
		$this->Model->find('all');
		$this->Model->request = array(
			'uri' => array(
				'host' => 'bakery.cakephp.org',
				'path' => 'articles.rss',
			),
		);
		$this->Model->find('all');

		$log = $this->Model->getDataSource()->getLog(false, false);
		$this->assertEquals('http://search.twitter.com/search.json?q=twitter', $log['log'][0]['request_uri']);
		$this->assertEquals('http://bakery.cakephp.org/articles.rss', $log['log'][1]['request_uri']);
		$this->assertGreaterThan(0, $log['log'][0]['took']);
		$this->assertGreaterThan(0, $log['log'][1]['took']);

		$oldDebug = Configure::read('debug');
		Configure::write('debug', 2);
		ob_start();
		$this->Model->getDataSource()->showLog();
		$contents = ob_get_clean();
		$this->assertRegExp('/' . preg_quote('http://search.twitter.com/search.json?q=twitter', '/') . '/s', $contents);
		$this->assertRegExp('/' . preg_quote('http://bakery.cakephp.org/articles.rss', '/') . '/s', $contents);

		ob_start();
		$this->Model->getDataSource()->showLog(true);
		$contents = ob_get_clean();

		$this->assertRegExp('/' . preg_quote('http://search.twitter.com/search.json?q=twitter', '/') . '/s', $contents);
		$this->assertRegExp('/' . preg_quote('http://bakery.cakephp.org/articles.rss', '/') . '/s', $contents);

		Configure::write('debug', $oldDebug);
	}

/**
 * testLog method
 *
 * @return void
 */
	public function testLogOptionIsFalse() {
		$this->Model->getDataSource()->getLog(false, true);
		$this->Model->request = array(
			'uri' => array(
				'host' => 'search.twitter.com',
				'path' => 'search.json',
				'query' => array('q' => 'twitter'),
			),
			'log' => false,
		);
		$this->Model->find('all');

		$log = $this->Model->getDataSource()->getLog(false, false);
		$this->assertEmpty($log['log']);
	}

}
