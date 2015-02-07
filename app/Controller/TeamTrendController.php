<?php

App::uses('Component', 'Controller');
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
        //$trend_los = $this->TeamTrend->getTeamTrendLOS();
        
        //debug($trend_goal_J2);
        
        //DBへ時間帯別得点の情報を登録
        //$this->setTrendGoal($trend_goal_J1);
        //$this->setTrendGoal($trend_goal_J2);
        
        //DBへ時間帯別失点の情報を登録
        //$this->setTrendLos($trend_los);
    }
    
        //DBへゴール傾向を保存
        public function setTrendGoal($trend_goal){
            //App::uses('Teamtrendgoal','Model'); //モデルクラスにTeamtrendgoalを指定
            App::uses('Teamtrend','Model');     //モデルクラスにTeamTrendを指定
            //debug($trend_goal);
            
            $teamtrend = new Teamtrend('Teamtrend','teamtrendgoal');
            $teamtrend->setTrendGoalDb($trend_goal);
            
            
            //$this->Teamtrendgoal->setTrendGoalDb($trend_goal);
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
    
}


