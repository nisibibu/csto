<?php

/*
 * 各ニュースサイトをスクレイピング  
 */

require_once 'C:\xampp\htdocs\cake\app\Vendor/autoload.php';


App::uses('Component', 'Controller');
class NewsCrawComponent extends Component{

    public $uses = array('POST','Live','Totovote','Teamtrendgoal');    //使用するモデルを宣言
    
    /*サッカーキングより取得
     *
     *      */
    public function getNewsInfoSoccerKing($param){
        //Goutteオブジェクト生成
        $crawer_trend = new Goutte\Client();
        //$crawer_trend_2 = new Goutte\Client();
        //$client_trend = new Client();
        $team_trend_goal = array();      //チームの時間帯別得点を保持
        $team_trend_temp = array();     //データ一時保管用変数
        $team_name = array();               //チーム名を保管
        $team_trend_url = TEAM_TREND.$param;
        debug($team_trend_url);

        //totoマッチング、投票率HTMLを取得
        $crawler_trend = $crawer_trend->request('GET', $team_trend_url);
        //debug($crawler_trend); 
       
        //チーム名の取得
       $crawler_trend->filter('.time_team')->each(function( $node )use(&$team_name){
            //debug($node->text());
            $team_name[] = trim($node->text());
        });
        //debug($team_name);

        //J１時間帯別得点を取得
        //時間帯別の取得
        $crawler_trend->filter('.time_sel')->each(function( $node )use(&$team_trend_temp){
            //debug($node->text());
            $team_trend_temp[] = trim($node->text());
        });
       //debug($team_trend_temp);
        
        /*連想配列の名前に使用する文字を取得*/
        $all_goal = $team_trend_temp[0];
        $time1 = $team_trend_temp[1];
        $time2 = $team_trend_temp[2];
        $time3 = $team_trend_temp[3];
        $time4 = $team_trend_temp[4];
        $time5 = $team_trend_temp[5];
        $time6 = $team_trend_temp[6];
        
        /*取得したデータから得点のみ抽出*/
        $temp = array_slice($team_trend_temp, 7);
        //debug($temp);
        
        /*  データの保管形式
         * 　array(
         *        'team' => 
         *        'alll_goal' =>
         *         '1-15' =>
         *         '16-30' =>
         *          '31- 前終' =>
         *          '46-60' =>
         *          '61-75' =>
         *          '76-終了' =>
         *   */
        
        //ゴールの一時変数
        $temp_goal  = array_chunk($temp, 7);
        //debug($temp_goal);
        //debug(count($team_name) -1);
        
        //データの加工処理
        for($i = 0; $i < count($team_name) ;  $i++){
           //チーム名の追加（各チーム配列の先頭）
           array_unshift($temp_goal[$i], $team_name[$i]);
           //年度の追加
           array_push($temp_goal[$i],"2014");   //現在の設定は2014年
            $team_trend_goal[] = $temp_goal[$i];   //チームの得点傾向を格納
        }
        //var_dump($team_trend_goal);
        
        /*データの整形*/
        /*
         foreach($temp as $var){
                            $temp_array = array();
                            $temp_array['team'] = $name;
                            $temp_array[$all_goal][] = $var[0];
                            $temp_array[$time1][] = $var[1];
                            $temp_array[$time2][] = $var[2];
                            $temp_array[$time3][] = $var[3];
                            $temp_array[$time4][] = $var[4];
                            $temp_array[$time5][] = $var[5];
                            $temp_array[$time6][] = $var[6];
                            $team_trend_goal[] = $temp_array;                      
          }
          */
        return $team_trend_goal;
    }
}
?>