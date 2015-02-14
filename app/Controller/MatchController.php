<?php
App::uses('Folder','Utility');
App::uses('File', 'Utility');

use Goutte\Client;
App::uses('Component', 'Controller');

class MatchController extends AppController{
    public $uses = array('POST','Match');    //使用するモデルを宣言
    public $components = array('Match');     //コンポーネントの指定
    
    /* index */
    public function index(){
        //Jリーグの試合結果を取得
        $j_class = "j2";
        $year = "2014";
        $month = "12";
        $param = $year."/".$j_class. "/fixtures_results/".$month.".html";
        $data_item = array("section","date_s","match_date","home_team","score",
                             "home_score","away_score","away_team",
                             "start_time","stadium","match_year","match_month","league");
        //$match_result_j1 = $this->Match->getMatchInfoJleague(GAME_MATCH_RESULT,$param);
        //sdebug($match_result_j1);
        //$this->setMatchesInfoJLeague($match_result_j1[1], $j_class, $data_item);
        
        /* 月のデータを節ごとに保存処理
        foreach($match_result_j1 as $result){
            $this->setMatchesInfoJLeague($result, $j_class, $data_item);
        }
        */
         
        //ナビスコカップの情報を取得
        $result_y = $this->Match->getYamazakiCupInfo();
        
    }
    
    
    /*試合結果を登録（Jリーグ）*/
    public function setMatchesInfoJLeague($league_info , $j_class,$data_item){
        App::uses('Match','Model');     //モデルクラスにTeamTrendを指定
        
        //モデルクラスのインスタンスを生成
        $league = new Match('Match','matches');
        $league->setMatchesDb($league_info, $j_class,$data_item);
    }
    
}

?>