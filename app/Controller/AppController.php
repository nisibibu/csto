<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

//App::uses('Controller', 'Controller');
App::uses('Controller', 'Controller');
//App::uses('Controller', 'Controller');
//App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'tmhoauth')));
App::build(array('Vendor' => array(APP . 'Vendor')));
App::uses('tmhOAuth', 'Vendor/tmhoauth/');
//App::build(array('Vender' => array(APP . 'Vendor' . DS . 'twitteroauth')));
App::uses('Vender', 'Vender/twitteroauth/');
//App::build(array('Vendor' => array(APP . 'Vendor' . DS . 'goutte')));
App::uses('goutte', 'Vender/goutte/');

//App::import('Vender', '/Vender/goutte/goutte.phar');
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
 
/* $ tail -15 Controller/AppController.php */
class AppController extends Controller {
	//public $components = array();
        public $components = array('DebugKit.Toolbar','RequestHandler','Session');
        
        /*Twitterの情報を格納する為の変数*/
        public $twitter_timeline;
        
 
        /*
        public function beforeFilter() {
            //$this->TwitterOAuth = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
        }
        */
        /*
        public $components = array('DebugKit.Toolbar',
            'Session',
            'Auth'=> array(
                //認証の設定
                'authenticate' => array(
                    'all' => array(
                        'fields'=>array(
                            'user_name' => 'username',
                            'password' => 'password',
                            ),
                    ),
                'TwitterKit.TwitterOauth',
                ),
                //ログインURL
                'LoginAciton' => array(
                    'plugin' => 'twitter_kit',
                    'controller' => 'users',
                    'action' => 'login',
                ),
                //ログイン完了後に遷移するURL
                'LoginRedirect' => array(
                    'plugin' => 'twitter_kit',
                    'controller' => 'Timeline',
                    'action' => 'index',
                ),
            ),
            'TwitterKit.Twitter',
      );
        */
        //文字コードのエンコードが（UTF-8)ではない為、文字化け
	//�A�v���P�[�V�����S�̂�Auth�R���|�[�l���g��K�p
	/*
	public $components = array(
		'Session' => array(),
		'Auth' => array(
			//���O�C�������p�̃A�N�V�����̐ݒ�
			'loginAction' => array(
					'controller' => 'users',
					'action' => 'login'
			),
			'authenticate' => array(
				//Form���g�����F�؂��s���ݒ�
				'Form' => array(
					//User���f�����g���ĔF�؂��s��
					'userModel' => 'User',
					//User�f�[�^���擾����ۂ̍i�荞�ݏ���
					'scope' => array('User.id >' => 0),
					//���[�U���ƃp�X���[�h�Ƃ��Ďg���J�������w��
					'fields' => array(
						'username' => 'username',
						'password' => 'password'
					)
				)
				'Form' => array(
					'passwordHasher' => array(
						'className' => 'Simple',
						'hashType' => 'sha256'
					)
				)
			)
		)
	);
	*/
}
