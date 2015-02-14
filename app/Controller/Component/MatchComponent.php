<?php

use Goutte\Client;
require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';

define('GAME_MATCH_RESULT', 'http://www.sponichi.co.jp/soccer/games/');                                 //Jリーグ試合日程＆結果
define("YAMAZKI_MATCH_RESULT","http://soccer.yahoo.co.jp/jleague/schedule/jleaguecup");                 //ヤマザキカップ試合日程＆結果
define("ACL_MATCH_RESULT","http://sportsnavi.yahoo.co.jp/sports/soccer/jleague/2015/schedule/112/");    //ACL試合日程＆結果
define('SCORE_QUICK_J1',"http://www.nikkansports.com/soccer/jleague/j1/score/j1-score.html");           //J1の速報
define("ALL_MATCH_THIS_MONTH","http://www.jleague.jp/match/");      //Jリーグ公式サイト（当月）の試合
/*リーグ情報の取得、格納*/
class MatchComponent extends Component{
    
    /*J1の情報をスクレイピングで取得*/
    public function  getMatchInfoJleague($url = GAME_MATCH_RESULT,$param = "2014/j1/fixtures_results/03.html"){
        $url = $url.$param;
        //debug($url);
        //Goutteオブジェクト生成
        $crawer = new Goutte\Client();
        
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
        preg_match("/[0-9]+月/", $data_time,$temp_m);
        //$month = preg_replace("/月/", "", $temp_m[0]);
        //debug($temp_m);
        $month = str_replace("月","", $temp_m);
        $month = mb_convert_kana($month[0],'n','UTF-8');
        //var_dump($year);
        //var_dump($month);
        
        
        $item_name = array("ホーム","スコア","アウェー","試合開始","競技場");
        $data_item = array("section","date_s","date","home","score",
                             "home_score","away_score","away","start_time","stadium","yaer","month");
        
        //debug($data_item);
        
        //情報を取得
        $crawler->filter('.table-block01 table td' )->each(function( $node )use(&$craw_result,$year){
            $temp = $this->stripspaces($node->text());
            $str = "―";
            $str_2 = "▽";
            $temp = str_replace($str,"-", $temp);   //全角ダッシュ変換
            $temp = str_replace($str_2,"", $temp);
            $temp = mb_convert_kana($temp, "rna");
            $craw_result[] = $temp;
            //スコアなら得点を分けて追加して格納
            if(preg_match("/(?P<home>\d+)-(?P<away>\d+)/", $temp,$var)){
                $craw_result[] = $var['home'];
                $craw_result[] = $var['away'];
            }
            //中止回だった場合、スコアに-99を入れる
            if($temp === "-"){
                $craw_result[] = -99;
                $craw_result[] = -99;
            }
            //開催日ならDATE型に変換したものを追加
            if(preg_match("/.+月.+日(.+)/", $temp)){
                $craw_result[] = $this->changeDateType($year,$temp);
            }
            
            //var_dump($craw_result);
        });
        
        //不要な項目データを削除（添え字も詰める）
        array_splice($craw_result, 0, count($item_name));
        $craw_result = array_filter($craw_result,"strlen");    //空要素の削除
        $craw_result = array_values($craw_result);      //添え字を直す

        //debug($craw_result);
        
        $setu = array();
         for($i = 0; $i < count($craw_result); $i++){
            if(preg_match("/第\d+節/",$craw_result[$i])){
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

        //debug($result_s);
        $match_date = array();      //試合開催日をdate型に変換
        $match_date_s = array();    //曜日も含む文字列で試合開催日を保持
        $match_date_i = array();    //日付のデータの添え字番号を保持
        $temp_result = array();     //整理前のデータ（一時保管）
        //$date_count = 0;
        
        
        /*日にち毎に分割して取得*/
        foreach ($result_s as $var){
            $temp_result[] = $this->getDataByDate($var);
        }

        
        /*DB登録用にデータを整形*/
        foreach ($temp_result as $var){
            $match_info[] = $this->formatResult($var, $year, $month);
        } 
        //debug($match_info);
        return $match_info;

    }
    
    /*日程毎の配列をDB登録用に整形して返す*/
    public function formatResult($temp_result, $year, $month){
        /* データの個別変換 */
        $arr = $this->arrayDepth($temp_result);
        $temp = array();
        if($arr === 1){
               $i = 0;  //単日の場合
               $section[] = $temp_result[$i];      //節
               array_shift($temp_result);
               $date_str[] = $temp_result[$i];      //試合開催日
               array_shift($temp_result);
               $date_d[] = $temp_result[$i];
               array_shift($temp_result);
               $temp[] = array_chunk($temp_result, 7);
 
        }else{
            //複日の場合
            foreach($temp_result as $var){
               $i = 0;
               $section[] = $var[$i];      //節
               array_shift($var);
               $date_str[] = $var[$i];      //試合開催日
               array_shift($var);
               $date_d[] = $var[$i];
               array_shift($var);
               $temp[] = array_chunk($var, 7);
            }
        }

        /*各配列（試合)をDB登録用の配列に整形 */
         /*$league_info
         * array(
         *      節
         *      開催日（曜日）
         *      開催日
         *      ホームチーム
         *      スコア
         *      ホームチームスコア
         *      アウェイチームスコア
         *      アウェイチーム
         *      開始時刻
         *      スタジアム
         *      年度
         *      月
         *          */
        $result = array();
        
        for($i = 0; $i < count($temp);$i++){
           //1つの配列に処理を行う
            foreach($temp[$i] as $var){
                array_unshift($var, $section[$i],$date_str[$i],$date_d[$i]);   //節、開催日（曜日）、開催日追加
                array_push($var, $year,$month);  //年、月を追加
                $result[] = $var;
            }  
        }
        
        return $result;
    }
    
    
    /*開催日排列を毎のデータに分割して返す*/
    public function getDataByDate($result_s){
        $section = $result_s[0];
        for($i = 0; $i < count($result_s);$i++){
                /*日付のデータ探索*/
                if(preg_match("/.+月.+日(.+)/", $result_s[$i])){
                    $match_date_i[] = $i;
                    //debug($match_date_i);
                    $match_date_s[] = $result_s[$i];
                }

        }
        /*1節複数日開催の場合、日付別に分割（２日のみ対応）*/
        if(count($match_date_i) !== 1){
                foreach ($match_date_i as $var){
                    if($var <> 1){
                        $temp_result[] = array_slice($result_s,0,$var);
                        $temp_result[] = array_slice($result_s,$var, count($result_s));
                        //debug($temp_result);
                        $result_t = array();
                        //各日付データの先頭に節を挿入
                        foreach($temp_result as $var){
                            if(!preg_match("/第\d+節/", $var[0])){
                                //排列先頭に節がない場合、節を挿入
                                array_unshift($var, $section);
                                $result_t[] = $var;
                            }
                            else{
                                $result_t[] = $var;
                            }
                        }
                        $result = $result_t;
                    }
                }
         }else{
                $result = $result_s;
         }
         
         return $result;
    }

    /*配列の階層を返す*/
    public function arrayDepth($arr, $blank=false, $depth=0){
        if( !is_array($arr)){
            return $depth;  //配列空の場合、0
        } else {
            $depth++;
            $tmp = ($blank) ? array($depth) : array(0);
            foreach($arr as $value){
                $tmp[] = $this->arrayDepth($value, $blank, $depth);
            }
            return max($tmp);
        }
    }
    
    //○月×日を日をDATE型に変換
    public function changeDateType($year,$str){
        preg_match("/(?P<month>\d+)月(?P<date>\d+)日/", $str, $temp_date);
        //debug($temp_date);
        
        $month_temp = $temp_date['month'];
        $date_temp =$temp_date['date'];
        
        $date_s = $year."-".$month_temp."-".$date_temp;
        //var_dump($date_s);
        //日付の生成
        $date = date("Y-m-d", strtotime($date_s));
        
        return $date;
    }
    
    
    //受け取ったデータを同一日付別の排列にして返す
    public function getDateDifferent($result_s){
        $result_d = array();    //日付毎にデータを保持
        foreach($result_s as $var){
            $pattern_begin = "/.+月.+日(.+)/";
            $pattern_end = "/.+月.+日(.+)/";
            $result_d[] = $this->getIndividual($pattern_begin, $pattern_end, $var);
        }
        var_dump($result_d);
        return $result_d;
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

     /*ヤマザキナビスコカップの情報を取得*/
     public function getYamazakiCupInfo($url = YAMAZKI_MATCH_RESULT,$param = ""){
        $url = $url.$param;
        //debug($url);
        //Goutteオブジェクト生成
        $crawer = new Goutte\Client();
        
        //順位を取得
        $crawler = $crawer->request('GET',$url);
        
        $craw_result = array(); //取得結果を保持
        $item_name = array();   
        $match_info = array();  //試合結果の保持
        $year;                  //年度
        $month;                 //月
        $date;                  //日
        $now_date;
        $tournament;            //トーナメントの状況（予選、準決勝、決勝etc...）
        $data_time;             //データの年月日
        
        
        //更新日の取得（年度に使用）
        $now_date = date('Y-m-d');  //年月に使用
        
        $item_name = array("キックオフ","ホーム","試合状況","アウェイ","試合会場");
        $data_item = array("section","date_s","date","home","score",
                             "home_score","away_score","away","start_time","stadium","yaer","month");
        
        //トーナメント（状況）を取得
        $crawler->filter('.h02_c h3' )->each(function( $node )use(&$tournament){
            $tournament[] = $node->text();
        });
        debug($tournament);
        
        //情報を取得
//        $crawler->filter('.table-block01 table td' )->each(function( $node )use(&$craw_result,$year){
//            $temp = $this->stripspaces($node->text());
//            $str = "―";
//            $str_2 = "▽";
//            $temp = str_replace($str,"-", $temp);   //全角ダッシュ変換
//            $temp = str_replace($str_2,"", $temp);
//            $temp = mb_convert_kana($temp, "rna");
//            $craw_result[] = $temp;
//            //スコアなら得点を分けて追加して格納
//            if(preg_match("/(?P<home>\d+)-(?P<away>\d+)/", $temp,$var)){
//                $craw_result[] = $var['home'];
//                $craw_result[] = $var['away'];
//            }
//            //中止回だった場合、スコアに-99を入れる
//            if($temp === "-"){
//                $craw_result[] = -99;
//                $craw_result[] = -99;
//            }
//            //開催日ならDATE型に変換したものを追加
//            if(preg_match("/.+月.+日(.+)/", $temp)){
//                $craw_result[] = $this->changeDateType($year,$temp);
//            }
//            
//            //var_dump($craw_result);
//        });
        
        //不要な項目データを削除（添え字も詰める）
//        array_splice($craw_result, 0, count($item_name));
//        $craw_result = array_filter($craw_result,"strlen");    //空要素の削除
//        $craw_result = array_values($craw_result);      //添え字を直す
//
//        //debug($craw_result);
//        
//        $setu = array();
//         for($i = 0; $i < count($craw_result); $i++){
//            if(preg_match("/第\d+節/",$craw_result[$i])){
//                $setu[$i] = $craw_result[$i];
//            }
//        }
//        
//        //debug($setu);
//        
//        //空白行を取り除いて整列
//        $result = $this->fixDataBlank($craw_result);
//        //var_dump($result);
//        
//        $result_s = array();    //節毎にデータ保持
//        
//        /*節毎にデータを分けて取得*/
//        
//        foreach($setu as $var){
//            $pattern_begin = "/".$var."/";
//            $pattern_end = "/第.+節$/";
//            $result_s[] = $this->getIndividual($pattern_begin, $pattern_end, $result);
//        }
//
//        //debug($result_s);
//        $match_date = array();      //試合開催日をdate型に変換
//        $match_date_s = array();    //曜日も含む文字列で試合開催日を保持
//        $match_date_i = array();    //日付のデータの添え字番号を保持
//        $temp_result = array();     //整理前のデータ（一時保管）
//        //$date_count = 0;
//        
//        
//        /*日にち毎に分割して取得*/
//        foreach ($result_s as $var){
//            $temp_result[] = $this->getDataByDate($var);
//        }
//
//        
//        /*DB登録用にデータを整形*/
//        foreach ($temp_result as $var){
//            $match_info[] = $this->formatResult($var, $year, $month);
//        } 
//        //debug($match_info);
//        return $match_info;
//         
//         
     }

     /*天皇杯の情報を取得*/
     public function getEmperorCupInfo(){
         
     }
     
     /*ACLの情報を取得*/
     public function getAclInfo(){
         
     }





     /*速報の取得
     *
     * 
     *  後で実装する
     *      
     *
     *      */
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