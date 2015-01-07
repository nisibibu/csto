<?php
//app/Controller/TimelineController.php
App::uses('AppConntoller', 'Controller');
App::uses('UserController', 'Controller');

class TimelineController extends AppController{
    public $helpers = array('Time');
    
    public function index(){
        //ログイン中のユーザー情報を取得
        $user = $this->data_j;
        if(empty($user)){
            return;
        }
        else{
            echo "データ受け取りました";
        }
        //Twitterデータソースを取得
        //$ds = $this->Twitter->getTwitterSource();
        //アクセストークンをTwitterデータソースに設定
        //$ds = setToken($user['TwitterUser']);
        //タイムラインを取得
        //$timeline = $ds->statuses_home_timeline();
        //取得した情報をビュー変数に設定
        $this->set(compact('timeline'));
    }
    public function test(){
        //ログイン中のユーザー情報を取得
        debug($this->data_j);
        
        /*$user = $this->data_j;
        if(empty($user)){
            return;
        }
        else{
            echo "データ取得しました";
        }*/
    }
}

?>