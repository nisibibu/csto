<?php
/*Toto情報関連コンポーネント
 *
 *  */
//use Goutte\Client;
//require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';
require_once 'C:\xampp\htdocs\cake\app\Vendor/autoload.php';

/*定数*/
//totoOne
define('TEAM_TREND', 'http://www.totoone.jp/blog/datawatch/timezone.php?kind=1');

class TeamTrendComponent extends Component{

    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
    
    public function getTeamTrendGoal($team_trend_url = TEAM_TREND){
        //Goutteオブジェクト生成
        $crawer_trend = new Goutte\Client();
        //$crawer_trend_2 = new Goutte\Client();
        //$client_trend = new Client();
        $team_trend_goal = array();      //チームの時間帯別得点を保持
        $team_trend_temp = array();     //データ一時保管用変数
        $team_name = array();               //チーム名を保管
        //debug($team_trend_url);

        //totoマッチング、投票率HTMLを取得
        $crawler_trend = $crawer_trend->request('GET', $team_trend_url);
        //debug($crawler_trend); 
       
        //チーム名の取得
//        $crawler_trend->filter('.time_team')->each(function( $node )use(&$team_name){
//            //debug($node->text());
//            $team_name[] = trim($node->text());
//        });
        //debug($team_name);

        //J１時間帯別得点を取得
        //時間帯別の取得
        $crawler_trend->filter('.time_sel')->each(function( $node )use(&$team_trend_temp){
            //debug($node->text());
            $team_trend_temp[] = trim($node->text());
        });
       debug($team_trend_temp);
        
        /*連想配列の名前に使用する文字を取得*/
//        $all_goal = $team_trend_temp[0];
//        $time1 = $team_trend_temp[1];
//        $time2 = $team_trend_temp[2];
//        $time3 = $team_trend_temp[3];
//        $time4 = $team_trend_temp[4];
//        $time5 = $team_trend_temp[5];
//        $time6 = $team_trend_temp[6];
        
        /*取得したデータから得点のみ抽出*/
//        $temp = array_slice($team_trend_temp, 7);
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
//        $temp_goal  = array_chunk($temp, 7);
        //debug($temp_goal);
        
 //       for($i = 0; count($team_name); $i++){
           //$temp_array = $team_name['name'][$i];
           //$temp_array[] =  $temp_goal[$i];
           //debug($team_name['name'][$i]);
            //$team_trend_goal[] = $temp_array;
//        }
        //debug($temp_goal);
        
        /*
         foreach($team_name as $name){
                   foreach($temp_goal as $var){
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
            }
            */
          
        //debug($team_trend_goal);
    }
}
?>