<?php
App::uses('Folder','Utility');
App::uses('File', 'Utility');

use Goutte\Client;
App::uses('Component', 'Controller');

class MatchController extends AppController{
    var $name = 'Match';                    //コントローラー名の指定
    public $uses = array('Post','Match');    //使用するモデルを宣言
    public $components = array('Match');     //コンポーネントの指定
    public $helpers = array("Form");         //ヘルパーの指定
    
    public $scaffold;
    
    /* index */
    public function index(){
        
        /*チームのリスト取得*/
        $team_list = $this->getTeamList();
        
        /*チームリストをビューへ渡す*/
        $this->set('team_list',$team_list);
        
        /**/
        if(!empty($form_data = $this->show())){
            /*チームの試合結果を指定した分だけ最新件数より取得する*/
            $team = $form_data['team'];
            $item = $form_data['count'];
            $match_result = $this->getMatchByTeam($team, $item);
            //var_dump($match_result);
            $this->set('match',$match_result);
        }else{
            /*POSTで指定されてこなかった場合の処理*/
            $team ="C大阪";
            $item = "3";
            $match_result = $this->getMatchByTeam($team, $item);
            //var_dump($match_result);
            $this->set('match',$match_result);
        }
        

        //Jリーグの試合結果を取得
        $j_class = "j2";
        $year = "2014";
        $month = "12";
        $param = $year."/".$j_class. "/fixtures_results/".$month.".html";
        $data_item = array("section","date_s","match_date","home_team","score",
                             "home_score","away_score","away_team",
                             "start_time","stadium","match_year","match_month","league");
        //$match_result_j = $this->Match->getMatchInfoJleague(GAME_MATCH_RESULT,$param);
        //debug($match_result_j);
        //$this->setMatchesInfoJLeague($match_result_j1[1], $j_class, $data_item);
        
        /* 月のデータを節ごとに保存処理
        foreach($match_result_j1 as $result){
            $this->setMatchesInfoJLeague($result, $j_class, $data_item);
        }
        */
         
        //ナビスコカップの情報を取得
        /*実装中*/
        //$result_y = $this->Match->getYamazakiCupInfo();
        
        //debug($this->request->data('Post.team'));
        
        /*画面表示テスト*/
        //if($this->show()){
            //Formからのデータ受け取り
            //var_dump("POSTデータ受け取り");
            ////$data = $this->show();
            //debug($data);
        //}
       // else{
            
        //}
        
        /*ヘルパーに初期値(前回入力値）をセットする
         * 参照 CakePHP実践入門 p.139
         * 
         *          */    
        //$id = $this->request->pass[0];  //
        if($this->request->is('post')){
            $data = array(
                'team'  => $this->request->data['match']['team'],
                'count' => $this->request->data['match']['count'],
            );
            if($this->Post->save($data)){
                //debug("保存しました");
                $this->Session->setFlash("保存しました");
                $this->redirect('/match/index');
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


    /*チーム一覧の取得*/
    public function getTeamList(){
         App::uses('Match','Model');     //モデルクラスにMatchを指定
        
        //モデルクラスのインスタンスを生成
        $match = new Match('Match','matches');
        $team_list = $match->getTeamListByDb();
        
        return $team_list;
    }

    /*試合結果を登録（Jリーグ）*/
    public function setMatchesInfoJLeague($match_info , $j_class,$data_item){
        App::uses('Match','Model');     //モデルクラスにMatchを指定
        
        //モデルクラスのインスタンスを生成
        $match = new Match('Match','matches');
        $match->setMatchesDb($match_info, $j_class,$data_item);
    }
    
    /*チームの直近試合結果を件数指定して取得
     * $team チーム
     * $item 取得件数
     *      */
    public function getMatchByTeam($team,$item){
         App::uses('Match','Model');     //モデルクラスにTeamTrendを指定
        
        //モデルクラスのインスタンスを生成
        $match = new Match('Match','matches');
        $result =  $match->getMatchDataByTeam($team, $item);
        return $result;
    }
    
    /*Formデータの受け取り、返却*/
    public function show(){
        //POSTが送信されたかどうか
        if($this->request->is('POST')){
            $team = $this->request->data['match']['team'];;
            $count = $this->request->data["match"]['count'];
            $data['team'] = $team;
            $data['count'] = $count;
        }
        else{
            return false;
        }
        return $data;
    }
}

?>