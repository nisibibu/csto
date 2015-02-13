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
        //J1の試合結果を取得
        $match_result_j1 = $this->Match->getMatchInfoJleague();
        
         
    }
    
    
    /*試合結果を登録（Jリーグ）*/
    public function setMatchInfoJLeague($league_info , $j_class){
        App::uses('Match','Model');     //モデルクラスにTeamTrendを指定
        
        //モデルクラスのインスタンスを生成
        $league = new League('League','matche');
        $league->setLeagueDb($league_info, $j_class);
    }
    
}

?>