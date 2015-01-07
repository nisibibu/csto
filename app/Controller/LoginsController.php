<?php

// Controller/ExampleController.php
App::import('Vendor', 'OAuth/OAuthClient');

class LoginController extends AppController {
    public function index() {
        $client = $this->createClient();
        $requestToken = $client->getRequestToken('https://api.twitter.com/oauth/request_token', 'http://192.168.1.100/cake/index.php/login/callback');

        if ($requestToken) {
            $this->Session->write('twitter_request_token', $requestToken);
            $this->redirect('https://api.twitter.com/oauth/authorize?oauth_token=' . $requestToken->key);
        } else {
        // an error occured when obtaining a request token
        }
    }

    public function callback() {
        $requestToken = $this->Session->read('twitter_request_token');
        $client = $this->createClient();
        $accessToken = $client->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken);

        if ($accessToken) {
            $client->post($accessToken->key, $accessToken->secret, 'https://api.twitter.com/1/statuses/update.json', array('status' => 'hello world!'));
        }

        if ($accessToken) {
        $twitter = $client->get(
            $accessToken->key,
            $accessToken->secret,
            'https://api.twitter.com/1.1/statuses/user_timeline.json',
            array()
        );// twitter上におけるユーザの情報を取得（optional）
        $twitter = json_decode($twitter, true);

        $this->set('test',$twitter[0]);
        $this->render('index');

        }
    }
    private function createClient() {
        return new OAuthClient('pkOywXf82gAyn1hnpqgzShmHX','ajN7sqnlvJEmqgQyaXRZ7m2bkSGWGIcsWF6ft9Alm0jmpuGlgE');
    }

}
