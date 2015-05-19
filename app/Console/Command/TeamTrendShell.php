<?php

/*Toto投票率取得シェル*/
App::uses('ComponentCollection','Controller');
App::uses('TeamTrendController', 'Controller'); //TeamTrendController使用
App::uses('TeamTrendComponent','Controller/Component');

/*チームの傾向情報を取得*/
class TeamTrendShell extends AppShell{
    public $uses = array('POST','Teamtrend');    //使用するモデルを宣言
    
    
    /*TotoComponentの呼び出し*/
    //コントローラーの現在のアクションハンドラの前に呼び出し
    public function startup() {
        /*コンポーネントクラスの使用準備*/
        $collection = new ComponentCollection();
        $this->Trend = new TeamTrendComponent($collection); 
        parent::startup();
        $this->TeamTrendController = new TeamTrendController(); //コントローラーの使用準備
    }
    
    /*メイン処理*/
    public function main(){
        $this->out("Hello");
    }
    
    /*時間帯別の得点傾向情報を保存*/
    public function goalTrend(){
        //J1の情報を取得
       $trend_J1 = $this->Trend->getTeamTrendGoal();
       //J2の情報を取得
       $param = "?kind=4";
       $trend_J2 = $this->Trend->getTeamTrendGoal($param,"j2");
       
        /*DBへ保存*/
        //DBへ時間帯別得点の情報を登録
        $team_model = new Teamtrend('Teamtrend','teamtrendgoal');
        $team_model->setTrendGoalDb($trend_J1);
        $team_model->setTrendGoalDb($trend_J2);
    }
    
    /*時間帯別の失点傾向情報を保存*/
    public function losTrend(){
        //J1の情報を取得
        $trend_J1 = $this->Trend->getTeamTrendLos();
        //J2の情報を取得
        $param = "?kind=5";
        $trend_J2 = $this->Trend->getTeamTrendLos($param,"j2");
        /*DBへ保存*/
        $team_model = new Teamtrend('Teamtrend','teamtrendlos');
        $team_model->setTrendLosDb($trend_J1);
        $team_model->setTrendLosDb($trend_J2);
    }
    
    /*状況別勝敗の記録*/
    public function winTrend(){
       //チームの状況別勝敗のDB登録
       $team_trend =  $this->Trend->getTeamTrendWin();
       /*コントローラー使用の場合*/
       //$this->TeamTrendController->setTrendWinning($team_trend); 
       /*モデル使用の場合*/
        $team_model = new Teamtrend('Teamtrend','teamtrendwinning');
        $team_model->setTrendWinDb($team_trend);
    }
}

