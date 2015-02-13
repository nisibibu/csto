<?php

use Goutte\Client;
require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';

define('GAME_MATCH_RESULT', 'http://www.sponichi.co.jp/soccer/games/2014/j1/fixtures_results/03.html'); //試合日程＆結果
define('SCORE_QUICK_J1',"http://www.nikkansports.com/soccer/jleague/j1/score/j1-score.html");   //J1の速報
/*リーグ情報の取得、格納*/
class MatchComponent extends Component{
    
    /*J1の情報をスクレイピングで取得*/
    public function  getMatchInfoJleague($url = GAME_MATCH_RESULT){
        //Goutteオブジェクト生成
        $crawer = new Goutte\Client();
        //var_dump($url);
        //順位を取得
        $crawler = $crawer->request('GET',$url);
        
        $craw_result = array(); //取得結果を保持
        $item_name = array();   
        $match_info = array();  //試合結果の保持
        $year;                  //年度
        $month;                 //月
        $date;                  //日
        $data_time;             //データの年月日
        
        
        //更新日の取得（年度に使用）
         $crawler->filter('#midashi01' )->each(function( $node )use(&$data_time){
            //var_dump($node->text());
            $data_time = trim($node->text());
        });
        //var_dump($data_time);
        
        //年度の取得
        preg_match("/^\d{4}/", $data_time,$year);
        $year = $year[0];
        //月の取得
        $temp_m;
        preg_match("/[０-９]+月/", $data_time,$temp_m);
        //$month = preg_replace("/月/", "", $temp_m[0]);
        $month = str_replace("月","", $temp_m);
        $month = mb_convert_kana($month[0],'n','UTF-8');
        var_dump($year);
        var_dump($month);
        
        
        $item_name = array("ホーム","スコア","アウェー","試合開始","競技場");
        $data_item = array("開催日","ホーム","スコア", "アウェイ",
                            "開始時刻","スタジアム");
        
        //var_dump($item_name);
        
        //情報を取得
        $crawler->filter('.table-block01 table td' )->each(function( $node )use(&$craw_result){
            $craw_result[] = $this->stripspaces($node->text());
            //var_dump($craw_result);
        });
        
        //不要な項目データを削除（添え字も詰める）
        array_splice($craw_result, 0, count($item_name));
        $craw_result = array_filter($craw_result,"strlen");    //空要素の削除
        $craw_result = array_values($craw_result);      //添え字を直す

        //debug($craw_result);
        
        $setu = array();
         for($i = 0; $i < count($craw_result); $i++){
            if(preg_match("/^▽.+/",$craw_result[$i])){
                $setu[$i] = $craw_result[$i];
            }
        }
        
        //debug($setu);
        
        //空白行を取り除いて整列
        $result = $this->fixDataBlank($craw_result);
        //var_dump($result);
        
        $result_s = array();    //節毎にデータ保持
        
        /*節毎にデータを分けて取得*/
        foreach($setu as $var){
            $pattern_begin = "/".$var."/";
            $pattern_end = "/第.+節$/";
            $result_s[] = $this->getIndividual($pattern_begin, $pattern_end, $result);
        }

        //var_dump($result_s[1]);
        $match_date = array();    //試合開催日をdate型に変換
        $match_date_s = array();  //曜日も含む文字列で試合開催日を保持
        $match_date_i = array();
        $temp_result = array();
        $date_count = 0;
        
        /************* １節のデータ整形 *************/
        /*日付が見つかるまで探索*/
        for($i = 0; $i < count($result_s[1]);$i++){
            /*日付のデータ探索*/
            if(preg_match("/.+月.+日（.+）/", $result_s[0][$i])){
                $match_date_i[] = $i;
                $match_date_s[] = $result_s[0][$i];
            }
            
        }
        var_dump($match_date_i);
        if(count($match_date_i) !== 1){
            foreach ($match_date_i as $var){
                /*1節複数日開催の場合、日付別に分割*/
                if($var <> 1){
                    $temp_result = array_chunk($result_s[0], $var);
                    //$date_count = $var;
                }
            }
        }else{
            $temp_result = $result_s[0];
        }
        /*日付変換（文字列の日付→date型）*/
        //mb_regex_encoding("UTF-8");
        //preg_match("/[０-９]+/", "３月２日",$t);
        //debug($t);
        
        /**日にちのみ取得（全角数字取り出し、半角数値へ）**/
        /*日付の部分
            ○○月○○日（○）
         *          */
        $d_1 = mb_substr($match_date_s[0], count($match_date_s[0]) - 6, 1);
        //debug($d_1);
        $d_1 = mb_convert_kana($d_1, "n");
        
        $d_2 = mb_substr($match_date_s[0], count($match_date_s[0]) - 7, 1);
        //debug($d_2);
        $d_2 = mb_convert_kana($d_2, "n");
        
        //日にち部分生成
        if(preg_match("/[\d]/",$d_2)){
            $d_1 = $d_1.$d_2;
        }
        
        
        $date_m = $year."-". $month. "-".$d_1;
        debug($date_m);
        $match_date = date("Y-m-d",strtotime($date_m));
        
        
        debug($match_date);
        debug($temp_result);
        
        /*節の取り出し*/
        
        
        /********************************************/
        
        /*配列の最後*/
        
        
        /*同一日付のデータに分ける*/
        $result_d = array();    //日付毎にデータを保持
        foreach($result_s as $var){
            $pattern_begin = "/.+月.+日（.+）/";
            $pattern_end = "/.+月.+日（.+）/";
            $result_d[] = $this->getIndividual($pattern_begin, $pattern_end, $var);
        }
        //var_dump($result_d);
        
        //各試合ごとに分けて取り出し
         /*$league_info
         * array(
         *      節
         *      開催日
         *      ホームチーム
         *     スコア
         *      ホームチームスコア
         *      アウェイチームスコア
         *      アウェイチーム
         *      開始時刻
         *      スタジアム
         *      年度
         *      月
         *          */
        //$match_info = array_chunk($result, count($data_item));
        
        //debug($match_info);
        //return $league_info;
    }
    
    /*  function getIndividual()
     * 
     *      正規表現を用いてデータの取り出し（個別）
         *   $pattern_begin 正規表現のパターン （データ取得初めの判定）
     　　*   $pattern_end   正規表現のパターン（データ取得終了判定）
         *   $result    全体のデータ(）
         */
    public function getIndividual($pattern_begin,$pattern_end,$result){
                $flag = TRUE;         //whileループを抜ける判定に使用
                $data  = array();    //結果を格納
                   /*totoの結果を個別に取得*/
                   for($i  = 0 ; $i < count($result); $i++){
                        if(!$flag){
                            //var_dump("Falseになったので抜ける");
                            break;
                        }
                        //探す初めのデータかチェック         
                        if(preg_match($pattern_begin, $result[$i])){
                            $j = $i;    //くじ結果の配列添え字番号を記録
                            //次の行の手前までデータを取得する
                            while(preg_match($pattern_end, $result[$j+1] ) == 0) {
                                    $data[] = $result[$j];
                                    $j++;
                                    //最終行なら抜ける
                                    if(!isset($result[$j +1])){
                                        //debug('最終行なので抜けます');
                                        break;
                                    }
                           }
                           $data[]  = $result[$j];
                           $flag = FALSE;
                           //$this->getIndividual($pattern_begin, $pattern_end, $data);
                 }
             }
             return $data;
     }
    
    
    /*速報の取得*/
    public function getScoreQuickReport($url = SCORE_QUICK_J1){
        //Goutteオブジェクト生成
           $crawer = new Goutte\Client();
           //var_dump($url);

           //HTMLを取得
           $crawler = $crawer->request('GET',$url);

           $craw_result = array(); //取得結果を保持
           $update_time;  //更新日時を取得
           $year;                   //年度を保持
           $item_name = array();
           $section;
           $goal_ranking_info = array();
           $links = array();
           
           //更新日の取得（年度に使用）
           $crawler->filter('.upDate p' )->each(function( $node )use(&$update_time){
                var_dump($node->text());
                $update_time = trim($node->text());
            });
           //var_dump($update_time);
            /*年度の取得*/
           preg_match('/^[0-9]{4}/', $update_time,$year);
           $year = $year[0];
           //var_dump($year);
           
           /*節の取得*/
           $crawler->filter('.upDate p' )->each(function( $node )use(&$section){
                var_dump($node->text());
                $section = trim($node->text());
            });
            
          //項目の取得
          $crawler->filter('#personal_record table th' )->each(function( $node )use(&$item_name){
               //debug($node->text());
               $item_name[] = trim($node->text());
           });
           //var_dump($item_name);
           
           
           //リンクの取得
           $crawler->filter('.pagelist ul li a' )->each(function( $node )use(&$links){
               //var_dump($node->attr('href'));
               $links[] = $node->attr('href');
           });
           $links = array_unique($links);//重複するリンクを削除
           
           /*リンクを利用して処理するよう記述
            *
            *
            */
           
           
            //情報の取得
          $crawler->filter('#personal_record table td' )->each(function( $node )use(&$craw_result){
               //debug($node->text());
               $craw_result[] = trim($node->text());
           });
           //var_dump($craw_result);
           
           //データの整形
           $result = $this->fixDataBlank($craw_result);
           //var_dump($result);
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
        $goal_result = array_chunk($result, count($item_name));
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