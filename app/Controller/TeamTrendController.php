<?php

App::uses('Component', 'Controller');
App::uses('AppController','Controller');    //Shellから呼び出せるようにAppControllerを明示的に読み込み
/*チーム傾向のコントローラー*/
class TeamTrendController extends AppController{
     public $uses = array('POST','Teamtrendgoal','Teamtrend');    //使用するモデルを宣言
     /*コンポーネントの指定*/
     public $components = array('TeamTrend');
    
    public function  index(){
        /*チームの時間帯別得点の情報を取得(J1)*/
        //$trend_goal_J1 =  $this->TeamTrend->getTeamTrendGoal();    //J!チームの時間帯別得点を取得
        
        /*チームの時間帯別得点の情報を取得(J2)*/
        //$param = "?kind=4";
        //$trend_goal_J2 = $this->TeamTrend->getTeamTrendGoal($param);  //J2チームの時間帯別得点を取得
        
        /*チームの時間帯別失点の情報を取得(J1)*/
        //$trend_los_J1 = $this->TeamTrend->getTeamTrendLos();
        
        /*チームの時間帯別失点の情報を取得(J2)*/
        //$param = "?kind=5";
        //$trend_los_J2 = $this->TeamTrend->getTeamTrendLos($param);
        
        ///debug($trend_los_J2);
        
        //DBへ時間帯別得点の情報を登録
        //$this->setTrendGoal($trend_goal_J1);
        //$this->setTrendGoal($trend_goal_J2);
        
        //DBへ時間帯別失点の情報を登録
        //$this->setTrendLos($trend_los_J1);
        //$this->setTrendLos($trend_los_J2);
        
        /*状況別勝敗の情報を取得*/
        //$trend_win_J1 = $this->TeamTrend->getTeamTrendWin();    //J1の状況別勝敗を取得
        //$this->setTrendWinning($trend_win_J1);
        
        //J2の状況別勝敗の情報を取得
        $param = "?kind=6";
        $trend_win_J2 = $trend_win_J2 = $this->TeamTrend->getTeamTrendWin($param);    //J2の状況別勝敗を取得
        //var_dump($trend_win_J2);
        $this->setTrendWinning($trend_win_J2);
    }
    
        //DBへゴール傾向を保存
        public function setTrendGoal($trend_goal){
            //App::uses('Teamtrendgoal','Model'); //モデルクラスにTeamtrendgoalを指定
            App::uses('Teamtrend','Model');     //モデルクラスにTeamTrendを指定
            //debug($trend_goal);
            
            $teamtrend = new Teamtrend('Teamtrend','teamtrendgoal');
            $teamtrend->setTrendGoalDb($trend_goal);
 
        }
    
        //DBへ時間帯別失点情報を登録
        public function  setTrendLos($trend_los){
            App::uses('Teamtrend','Model');     //モデルクラスにTeamTrendを指定
            
            /*データベース接続先情報*/
            /*
            $config['hostname'] = "localhost";
            $config['username'] = "root";
            $config['password'] = "795382";
            $config['database'] = "soccer";
            $config['dbdriver'] = "mysql";
            $config['dbprefix'] = "";
            $config['pconnect'] = FALSE;
            $config['db_debug'] = TRUE;
            */
            
            /*テーブルを指定してモデルの登録クラスを呼び出す
              第１引数　モデル名
              第2引数　テーブル名
              第3引数　データベース接続先情報
             * 
             */
            $teamtrend = new Teamtrend('Teamtrend','teamtrendlos');
            $teamtrend->setTrendLosDb($trend_los);
        }
    
        public function setTrendWinning($trend_win){
            //App::uses('Teamtrendgoal','Model'); //モデルクラスにTeamtrendgoalを指定
            App::uses('Teamtrend','Model');     //モデルクラスにTeamTrendを指定
            //debug($trend_goal);
            
            $teamtrend = new Teamtrend('Teamtrend','teamtrendwinning');
            $teamtrend->setTrendWinDb($trend_win);
            
        }
}


