<?php

/*Toto関連ページのコントローラー
 * *****************************
 *  2015-03-22
 * 
 * TotovotesController
 * MatchController
 * 
 *              を読み込み使用する
 * 
 * ******************************
 * 
 * 
 * 
 * 
 * 
 *  */

App::uses('Folder','Utility');
App::uses('File', 'Utility');
App::uses('AppController','Controller');
App::uses('TotovotesController','Controller');
App::uses('MatchController','Controller');

use Goutte\Client;  //Goutteの読み込み
App::uses('Component', 'Controller');

CakePlugin::load('TwitterBootstrap');
CakePlugin::load('BoostCake');



class TotoController extends AppController{

    var $name = 'Toto';                      //コントローラー名の指定
    public $uses = array('Post','Match','Vote','League','TeamTrend');       //使用するモデルを宣言
    public $components = array(
        'Matches',
        'Toto',
        'TotoVotes',
//        'Auth' => array(
//			'flash' => array(
//				'element' => 'alert',
//				'key' => 'auth',
//				'params' => array(
//					'plugin' => 'BoostCake',
//					'class' => 'alert-error'
//				)
//			)
//	)
        );               //コンポーネントの指定
    //public $helpers = array("Html","Form");            //ヘルパーの指定
    public $helpers = array(
		'Session',
		'Html' => array('className' => 'BoostCake.BoostCakeHtml'),
		'Form' => array('className' => 'BoostCake.BoostCakeForm'),
		'Paginator' => array('className' => 'BoostCake.BoostCakePaginator'),
    );
    
    
    public $scaffold;
    
    /* index */
    public function index(){
        $totovotes_controller = new TotovotesController();
        $match_controller = new MatchController();
        
        /*チームのリスト取得*/
        $team_list = $match_controller->getTeamList();
        //debug($team_list);
        /*チームリストをビューへ渡す*/
        $this->set('team_list',$team_list);
        
        /*今回のtotoの試合情報を取得*/
        $recent_toto_info = $totovotes_controller->getRecentTotoinfo();
        //debug($recent_toto_info);
        //開催くじの取得
        $kuji = array();
        if(isset($recent_toto_info['toto'])){
            $kuji['toto'] = 'toto';
        }
        if(isset($recent_toto_info['mini']['A'])){
            $kuji['mini-A'] = 'mini-A';
        }
        if(isset($recent_toto_info['mini']['B'])){
            $kuji['mini-B'] = 'mini-B';
        }
        //debug(count($recent_toto_info['goal']));
        if(isset($recent_toto_info['goal'])){
            if(count($recent_toto_info['goal']) === 7 ){
                $kuji['goal3'] = 'goal3';
            }
            if(count($recent_toto_info['goal']) === 5 ){
                $kuji['goal2'] = 'goal2';
            }
        }
        //debug($kuji);
        $this->set('kuji',$kuji);
        $this->set('recent_toto_info',$recent_toto_info);        
        
        
        /*POSTデータの判定、データの取り出し→画面へセット*/
        $form_data = $this->show();
        if(!empty($form_data) && array_key_exists('team', $form_data)){
            /*チームの試合結果を指定した分だけ最新件数より取得する*/
            $team = $form_data['team'];
            $item = $form_data['count'];
            $match_result = $match_controller->getMatchByTeam($team, $item);
            $ranking = $match_controller->getTeamRanking($team);
            //debug($ranking);
            $this->set('match',$match_result);
            $this->set('ranking',$ranking);
        }else if(!empty($form_data) && array_key_exists('home_team', $form_data) && array_key_exists('away_team', $form_data)){
            //debug($form_data);
            $home_team_info = $form_data['home_team'];
            $this->set('home_team_info',$home_team_info);
            $away_team_info = $form_data['away_team'];
            $this->set('away_team_info',$away_team_info);
        }else if(!empty($form_data) && array_key_exists('kuji', $form_data)){
            $kuji_kind = $form_data['kuji'];
            $this->set('kuji_selected',$kuji_kind);
        }else{
            /*POSTで指定されてこなかった場合の処理*/
            $team ="C大阪";
            $item = "3";
            $match_result = $match_controller->getMatchByTeam($team, $item);
            //var_dump($match_result);
            $this->set('match',$match_result);
        }
        
        /*画面表示テスト*/
        
        /*ヘルパーに初期値(前回入力値）をセットする
         * 参照 CakePHP実践入門 p.139
         * 
         *          */    
        //$id = $this->request->pass[0];  //
        if($this->request->is('post')){
            //debug($this->request->data['match']['kind']);
            if(array_key_exists('match', $this->request->data)){
                $data = array(
                'team'  => $this->request->data['match']['team'],
                'count' => $this->request->data['match']['count'],
                );
                if($this->Post->save($data)){
                    //debug("保存しました");
                    $this->Session->setFlash("保存しました");
                    $this->redirect('/toto/index');
                } 
            }
        }else{
            //var_dump("POSTされていないので初期値を設定します");
            $options = array(
            'condtions' => array(
                'matchTeam' => "C大阪",
                'count' => "5",
                )
            );
            $this->request->data = $this->Post->find(
                    'first',
                    $options
            );
        }   
        //$this->request->data = $this->Post->find('team',$options);
        //$this->request->data = array('team',$options);
        
    }
    
    /*Formデータの受け取り、返却*/
    public function show(){
        //POSTが送信されたかどうか
        if($this->request->is('POST')){
            if(array_key_exists('kuji', $this->request->data)){
                $kuji = $this->request->data['kuji']['kind'];
                $data['kuji'] = $kuji;
            }
            if(array_key_exists('match', $this->request->data)){
                $team = $this->request->data['match']['team'];
                $count = $this->request->data["match"]['count'];
                $data['team'] = $team;
                $data['count'] = $count;
            }
            if(array_key_exists('card', $this->request->data)){
                $data['home_team'] = $this->request->data['card']['home_team'];
                $data['away_team'] = $this->request->data["card"]['away_team'];
            }
        }
        else{
            return false;
        }
        return $data;
    }
    
}
