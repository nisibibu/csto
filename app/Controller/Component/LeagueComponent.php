<?php

use Goutte\Client;
require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';

define('LEAG_RANKING', 'http://soccer.yahoo.co.jp/jleague/standings/');                 //リーグの情報
define('TEAM_INFO_SPORTS_NAVI','http://soccer.yahoo.co.jp/jleague/teams/detail');       //チームの情報
define('GOAL_RANKING_J1','http://soccer.yahoo.co.jp/jleague/stats/j1/0/');              //得点ランキング（J1)
define('GOAL_RANKING_J2','http://soccer.yahoo.co.jp/jleague/stats/j2/0/');              //得点ランキング(J2)
define('EXPECTATION_RANK',"http://soccer.yahoo.co.jp/jleague/expectranking");           //期待値ランキング

define('ASSIST_RANKING','http://www.football-lab.jp/summary/player_ranking/j1/?year=2014');   //アシストランキング

/*リーグ情報の取得、格納*/
class LeagueComponent extends Component{
    
    /*J1の情報をスクレイピングで取得*/
    public function  getLeagueInfo($status = "j1"){
        //Goutteオブジェクト生成
        $crawer = new Goutte\Client();
        $url = LEAG_RANKING.$status;
        var_dump($url);
        //順位を取得
        $crawler = $crawer->request('GET', LEAG_RANKING.$status);
        
        $craw_result = array(); //取得結果を保持
        $item_name = array();   
        $league_info = array(); //リーグ情報の保持
        $year;             //年度
        $update_time;      //更新日の取得
        
        
        //更新日の取得（年度に使用）
        /*ゴールゴールランキングの部分を適用する*/
         $crawler->filter('.grayBg2 p10p mb10p yjMS txt_c' )->each(function( $node )use(&$update_time){
             var_dump($node->text());
            $update_time = trim($node->text());
        });

        
        //項目名の取得
         $crawler->filter('#team_ranking table th' )->each(function( $node )use(&$item_name){
            //debug($node->text());
            $item_name[] = trim($node->text());
        });
        //var_dump($item_name);
        
        //リーグの情報を取得
       $crawler->filter('#team_ranking table td' )->each(function( $node )use(&$craw_result){
            //debug($node->text());
            $craw_result[] = trim($node->text());
        });
        
        //空白行を取り除いて整列
        $result = $this->fixDataBlank($craw_result);
        //var_dump($result);
        
        //debug($result);
        
        //各チームごとに分けて取り出し
         /*$league_info
         * array(
         *      順位
         *      チーム名
         *       勝点
         *       試合数
         *       勝敗
         *       引き分け数
         *       敗戦数
         *       得点
         *       失点
         *       得失点差
         *          */
        $league_info = array_chunk($result, count($item_name));

        return $league_info;
    }
    
    /*得点ランキングの取得*/
    public function getGoalRanking($url = GOAL_RANKING_J1,$param ="1"){
        //Goutteオブジェクト生成
           $crawer = new Goutte\Client();
           $url = $url.$param;
           //debug($url);

           //HTMLを取得
           $crawler = $crawer->request('GET',$url);

           $craw_result = array(); //取得結果を保持
           $update_time;  //更新日時を取得
           $year;                   //年度を保持
           $item_name = array();
           $goal_ranking_info = array();
           $links = array();
           
           //更新日の取得（年度に使用）
           $crawler->filter('p.updateDate' )->each(function( $node )use(&$update_time){
                //var_dump($node->text());
                $update_time = trim($node->text());
            });
           //var_dump($update_time);
            /*年度の取得*/
           preg_match('/^[0-9]{4}/', $update_time,$year);
           $year = $year[0];
           //debug($year);
           
          //項目の取得
          $crawler->filter('#modSoccerStats table th' )->each(function( $node )use(&$item_name){
               if(trim($node->text()) === "選手名"){
                    $item_name[] = trim($node->text());
                    $item_name[] = "ベストゴール動画";
               }else{
                   $item_name[] = trim($node->text());
               }              
           });
           //debug($item_name);
           
           
           //リンク（ランキングページ）の取得
           $crawler->filter('.pagelist ul li a' )->each(function( $node )use(&$links){
               //var_dump($node->attr('href'));
               $links[] = $node->attr('href');
           });
           $links = array_unique($links);//重複するリンクを削除
           
           /*リンクを利用して処理するよう記述
            * 複数ページ取得
            *
            * 未実装
            */
           
           
            //情報の取得
           $crawler->filter('#modSoccerStats table tbody td' )->each(function( $node )use(&$craw_result){
               //debug($node->text());
               $craw_result[] = trim($node->text());
           });
           //debug($craw_result);
           
           //データの整形
//           $result = $this->fixDataBlank($craw_result);
//           debug($result);
           //var_dump($goal_ranking_info);
           
         //個別に分けて保持
         /*$goal_ranking_info
         * array(
         *      順位
         *      選手名
         *      チーム名
         *      位置
         *      得点
         *      PK
         *      シュート
         *      シュート決定率
         *      90分平均得点
         *      試合数
         *      出場時間（分）
         *      警告
         *      退場
          *     年度
         *          */
        $goal_result = array_chunk($craw_result, count($item_name));
        //var_dump($goal_result);
        //debug($goal_ranking_info);
        
        foreach ($goal_result as $var){
            //var_dump($var);
            //各要素に年度を末尾に追加
            array_push($var,$year);
            $goal_ranking_info[] = $var;
        }
        //$goal_ranking_info = $goal_result;
        
       //var_dump($goal_ranking_info);
        
       return $goal_ranking_info;
           
    }
    
    
    /*ページのリンク取得用*/
    public function getLinks($url,$param){
           //Goutteオブジェクト生成
           $crawer = new Goutte\Client();

           //HTMLを取得
           $crawler = $crawer->request('GET',$url);

           $links = array();
           
           /*共通化の処理を施す*/
    }
    
    
    
    /*空白のデータを削除して配列を整列しなおす*/
    public function  fixDataBlank($result){
         
        /*0のデータを消さないようにする*/
         function even($var){
                  return ($var<> '');
         }
         
         //改行無しのスペース(＆ｎｂｓｐ；)を半角スペースに置換して削除
         //参考URL
         //http://nanoappli.com/blog/archives/5429
         for ($i = 0 ; $i < count($result); $i++){
                  $result[$i] = trim( $result[$i], chr(0xC2).chr(0xA0) );
         }
         
         
         //取得したデータから空の部分を詰めて配列添え直し
          $result =  array_filter($result,'even');
          $result = array_values($result);
          
          debug($result);

          return $result;
    }
    
    
    /*削除用にスペース、半角スペース、タブを空に変換*/
    public function stripspaces($string){
        $all="　";//全角スペース
        $half=" ";//半角スペース
        $tab="\t";//タブ
        $no="";//削除用変数
        $string = str_replace(array($all,$half,$tab),$no,$string);
        return $string;
    }
}

