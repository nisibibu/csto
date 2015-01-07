<?php
class TopicFixture extends CakeTestFixture{
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false,
			'default' => null, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => false,
			'default' => null),
		'body' => array('type' => 'string', 'null' => false,
			'default' => null),
		'category_id' => array('type' => 'integer', 'null' => false,
			'default' => null),
		'created' => array('type' => 'datetime', 'null' => false,
			'default' => null),
		'modified' => array('type' => 'datetime', 'null' => false,
			'default' => null),
		'tableParameters' => array('charset' => 'utf8',
			'collate' => 'utf8_unicode_ci', 'engine' => 'MyISAM')
	);
	
	public $records = array(
			array(
					'id' => 1,
					'title' => 'Lorem ipsum dolor sit amet',
					'body' => 'Lorem ipsum dolor sit amet',
					'category_id' => 1,
			),
	);
}


?>