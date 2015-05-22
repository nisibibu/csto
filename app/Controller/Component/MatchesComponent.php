<?php

use Goutte\Client;
if(env('DOCUMENT_ROOT')){
    require_once($_SERVER['DOCUMENT_ROOT']."cake/app/Vendor/goutte/goutte.phar");
}else{
    //debug(env('DOCUMENT_ROOT'));
    require_once 'C:\xampp\htdocs\cake/app/Vendor/goutte/goutte.phar';
}


define('GAME_MATCH_RESULT', 'http://www.sponichi.co.jp/soccer/games/');                                 //Jリーグ試合日程＆結果
define('YAMAZAKI_MATCH_RESULT','http://www.sponichi.co.jp/soccer/games/');
define("YAMAZKI_MATCH_RESULT_YAHOO","http://soccer.yahoo.co.jp/jleague/schedule/jleaguecup");                 //ヤマザキカップ試合日程＆結果
define("ACL_MATCH_RESULT","http://sportsnavi.yahoo.co.jp/sports/soccer/jleague/2015/schedule/112/");    //ACL試合日程＆結果
define("ACL_MATCH","http://www.nikkansports.com/soccer/jleague/acl/result/");   //ACLの情報取得先URL
define('SCORE_QUICK_J1',"http://www.nikkansports.com/socncer/jleague/j1/score/j1-score.html");           //J1の速報
define("ALL_MATCH_THIS_MONTH","http://www.jleague.jp/match/");      //Jリーグ公式サイト（当月）の試合
define("NIKKAN_JLEAGUE","http://www.nikkansports.com/");   //スタッツ取得用

/*リーグ情報の取得・格納
 * 
 * 
 * 
 *
 *  */
class MatchesComponent extends Component{
    /*Jリーグ速報から試合詳細へ→スタッツ情報の取得（直近の試合のみ）
     * 
     *      */
    public function getStatuRecentMatch($league){
        $url = NIKKAN_JLEAGUE ."soccer/jleague/". $league . "/score";
        //debug($url);
        
        //Goutteオブジェクト生成
        $crawer = new Goutte\Client();
        
        //順位を取得
        $crawler = $crawer->request('GET',$url);
        
        $detail_links = array(); //試合詳細を見るリンクURLを保持
        
        //リンクの取得
        $crawler->filter('p.showDetail a' )->each(function( $node )use(&$detail_links){
               //var_dump($node->attr('href'));
               $detail_links[] = $node->attr('href');
        });
        $detail_links = array_unique($detail_links);//重複するリンクを削除
        //debug($detail_links);
        
        /*更新日時の取得*/
        $update;
        $crawler->filter('p.upDate' )->each(function( $node )use(&$update){
               //var_dump($node->attr('href'));
               $update = trim($node->text());
        });
        debug($update);
        
        /**** 直近の開催試合分のスタッツ取得(１試合分） ****/
        $stats_url = NIKKAN_JLEAGUE . $detail_links[0];
        $stats_clawer = $crawer->request('GET', $stats_url);
        
        //対戦カードの取得
        $match_cards = array();
        $stats_clawer->filter('.kickStats th')->each(function( $node )use(&$match_cards){
            $match_cards[] = trim($node->text());
        });
        debug($match_cards);
        
        //スタッツ情報の取得
        $stats_array = array(); 
        $stats_clawer->filter('.kickStats td')->each(function( $node )use(&$stats_array){
            $stats_array[] = trim($node->text());
        });
        debug($stats_array);
        
        //スタッツ情報の整形
        $temp_array = array();  //整形用一時変数
        if(count($match_cards) !== 3){
            return;  //対戦カードの取得失敗
        }
        if(count($stats_array) === 21){
            $tmp_stat;
            for($i = 0; $i < count($stats_array); $i++){
                if($i % 3 === 2){
                    $temp_array[$tmp_name][] = $stats_array[$i];
                }else if($i % 3 === 1){
                    $temp_array[$stats_array[$i]][] = $tmp_stat;
                    $tmp_name = $stats_array[$i];
                }else{
                    $tmp_stat = $stats_array[$i];
                }
            }
        }else{
            return;  //スタッツ情報の取得失敗
        }
        debug($temp_array);
        
        $result;    //整形済みデータ
        $item_array = array_keys($temp_array);
        debug($item_array);
        
        for($i = 0; $i < count($item_array); $i++){
            $result[$match_cards[0]][$item_array[$i]] = $temp_array[$item_array[$i]][0];
        }
        for($i = 0; $i < count($item_array); $i++){
            $result[$match_cards[2]][$item_array[$i]] = $temp_array[$item_array[$i]][1];
        }
        debug($result);
    }
    
    
    /*Jリーグ試合結果の情報をスクレイピングで取得
     * Ｊ１昇格、プレーオフ未対応なので対応させる
     * 
     * @param string $url
     * @param string $j_class
     * @param string $year
     * @param string $month 
     * 
     */
    public function  getMatchInfoJleague($url = GAME_MATCH_RESULT,$j_class ="j1", $year ="", $month="" ){
        
        /*年度指定がない場合、今年の年度を設定*/
        if(!$year){
            $now_year = date("Y", time());
            //debug($year);        
            $year = $now_year;
        }
        /*月の指定がない場合、今月の月を設定*/
        if(!$month){
            $now_month = date("m", time());
            //debug($month);
            $month = $now_month;
        }
        
        if($j_class === "j1" || $j_class === "j2"){  //Jリーグ
            /*URLに付与するパラメーターを設定*/
            $param = $year."/".$j_class. "/fixtures_results/".$month.".html";
            $url = $url.$param;
        }else if($j_class === "ヤマザキナビスコ杯" ){ //ヤマザキナビスコ杯
            $param = $year."/nabisco/fixtures_results/index.html";
            $url = $url.$param;     
        }

        
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
        
        //var_dump($year)
        
        /*Jリーグの場合、年、月を取得*/
        if($j_class === "j1" || $j_class === "j2"){
             //年度の取得
            preg_match("/^\d{4}/", $data_time,$year);
            $year = $year[0];
            //月の取得
            $temp_m;
            preg_match("/[０-９]+月/", $data_time,$temp_m);
            //$month = preg_replace("/月/", "", $temp_m[0]);
            //debug($temp_m);

            $month = str_replace("月","", $temp_m);
            //debug($month);
            $month = mb_convert_kana($month[0],'n','UTF-8');
        }
        
       
        //var_dump($year);
        //var_dump($month);
        
        
        $item_name = array("ホーム","スコア","アウェー","試合開始","競技場");
        $data_item = array("section","date_s","date","home","score",
                             "home_score","away_score","away","start_time","stadium","yaer","month","week");
        
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
            //開催日（DATE変換化）なら週を追加
//            if(preg_match("/\d{4}-\d{2}-\d{2}/", $temp)){
//                //$craw_result[] = $this->getWeek($temp);
//                var_dump($this->getWeek($temp));
//            }
//            
            //var_dump($craw_result);
        });
        
        //debug($craw_result);
        
        //不要な項目データを削除（添え字も詰める）
        array_splice($craw_result, 0, count($item_name));
        $craw_result = array_filter($craw_result,"strlen");    //空要素の削除
        $craw_result = array_values($craw_result);      //添え字を直す

        //debug($craw_result);
        //debug($j_class);
        
        /*Jリーグのデータ取得の時の処理*/
        if($j_class === "j1" || $j_class === "j2"){
            //var_dump("Jリーグの処理");
            $setu = array();
             for($i = 0; $i < count($craw_result); $i++){
                if(preg_match("/第\d+節/",$craw_result[$i])){
                    $setu[$i] = $craw_result[$i];
                }
            }

            //debug($setu);

            //空白行を取り除いて整列
            $result = $this->fixDataBlank($craw_result);
            //svar_dump($result);

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
            //debug($temp_result);

            /*DB登録用にデータを整形*/
            foreach ($temp_result as $var){
                $match_info[] = $this->formatResult($var, $year, $month);
            } 
        }else if($j_class === "ヤマザキナビスコ杯"){
            /*ヤマザキナビスコ杯の時の処理*/
            //var_dump("ヤマザキナビスコ杯の時の処理");
            $group = array();
            
            /*日にちの取得*/
            for($i = 0; $i < count($craw_result); $i++){
                if(preg_match("#^\d+月\d+日#",$craw_result[$i],$m)){
                    $group[$i] = $m[0];
                }
            }

            //debug($group);

            //空白行を取り除いて整列
            $result = $this->fixDataBlank($craw_result);
            //debug($result);

            $result_s = array();    //節毎にデータ保持


            /*日毎にデータを分けて取得*/
            foreach($group as $var){
                $pattern_begin = "/".$var."/";
                $pattern_end = "#.+月.+日.+#";
                $result_s[] = $this->getIndividual($pattern_begin, $pattern_end, $result);
            }          
            //debug($result_s);
            
            $match_date = array();      //試合開催日をdate型に変換
            $match_date_s = array();    //曜日も含む文字列で試合開催日を保持
            $match_date_i = array();    //日付のデータの添え字番号を保持
            $temp_result = array();     //整理前のデータ（一時保管）
            $date_count = 0;
            
            /*リーグステージ毎に分割して取得*/            
            foreach($result_s as $var){
                $result_g[] = $this->getDataByGroupStage($var);
            }
            //$result = $this->getDataByGroupStage($result_s[0]);
            //debug($result_g);
            //debug($temp_result);

//          /*DB登録用にデータを整形*/
            foreach ($result_g as $var){
                //$match_info[] = $this->formatResultToNabisuko($result);
                $match_info[] = $this->formatResultToNabisuko($var);
            }
        }
        
        //debug($match_info);
        
//        $test_array = array(
//             "あ",
//             "い",
//             "う",
//             "え",
//             "お",
//        );
//        debug($test_array);
//        $test = $this->sanitize($test_array);
//        debug($test);       
        
        /*リーグの付与*/
        $match_info['class'] = $j_class;
        //debug($match_info);
        
        return $match_info;

    }
    
    /*日にちから月の何週目かを取得して返す
     *
     * @param  stirng  $date 
     * @return int  $count_week
     *      */
    public function getWeek($date){
        $now = strtotime($date);
        $saturday = 6;
        $week_day = 7;
        //debug(date('w',$now));
        $w = intval(date('w',$now));
        //debug($w);
        $d = intval(date('d',$now));
        //debug($d);
        if ($w!=$saturday) {
        $w = ($saturday - $w) + $d;
        } else { // 土曜日の場合を修正
        $w = $d;
        }
        $count_week = ceil($w/$week_day);
        return $count_week;
    }
    
    
    /*日程毎の配列をDB登録用に整形して返す(ナビスコ杯使用)*/
    public function formatResultToNabisuko($match_info){
        foreach($match_info['match_info'] as $var){
            if(preg_match("#\d{4}-\d{2}-\d{2}#", $var)){
                $held_date = $var;  //開催日の取得
                $week = $this->getWeek($var);
            }
        }
        
        //$held_date = $match_info['match_info'][1];  //開催日時の取得
        
        /*YamazakiNabisukoCup(suponichi)のデータ形式*/
        $data_item = array(
            "stage",
            'home_team',
            'score',
            'home_score',
            'away_score',
            'away_team',
            'match_time',
            'stadium',
        );
        
        /**/
        foreach($match_info as $var){
            //一番目の要素取り出し判定（ステージ情報かどうか）
            //$i = 0;
            $temp;
            if(preg_match("#(予選リーグ|準々決勝|準決勝|決勝)#",$var[0])){
                $stege = array_shift($var); //ステージ情報を取り出し
                $temp = array_chunk($var, count($data_item) -1);
                /*年月日の取り出し*/
                preg_match("/(?P<year>\d{4}+)-(?P<month>\d{2}+)-(?P<day>\d{2}+)/", $held_date,$m);
                $year = $m['year'];
                $month = $m['month'];
                
                /*データ整形*/
                foreach($temp as $v){
                    array_unshift($v,$stege,$held_date);
                    array_push($v,$year,$month,$week);
                    $result[] = $v;
                    //$i++;
                }
            }
        }
        //debug($result);
        
        return $result;
    }
    
    
    
    /*日程毎の配列をDB登録用に整形して返す(Jリーグ使用)*/
    public function formatResult($temp_result, $year, $month){
        /* データの個別変換 */
        $arr = $this->arrayDepth($temp_result);
        $temp = array();
        if($arr === 1){
               $i = 0;  //単日の場合
               $section[] = $temp_result[$i];      //節取得
               array_shift($temp_result);
               $date_str[] = $temp_result[$i];      //試合開催日取得
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
         *      週目
         *          */
        $result = array();
        
        for($i = 0; $i < count($temp);$i++){
           //1つの配列に処理を行う
            foreach($temp[$i] as $var){
                $week = $this->getWeek($date_d[$i]);
                array_unshift($var, $section[$i],$date_str[$i],$date_d[$i]);   //節、開催日（曜日）、開催日追加
                array_push($var, $year,$month,$week);  //年、月を追加
                $result[] = $var;
            }  
        }
        
        return $result;
    }
    
    /*再帰*/
    function sanitize($value){
        if(is_array($value)){
            $sanitize_array = array();
            foreach($value as $k => $v){
                $sanitize_array[$k] = $this->sanitize($v);
            }
            return $sanitize_array;
        }else{
            return htmlspecialchars($value);
        }
    }

    /*再帰での処理
     * 実装途中。。。・
     * 
     * 実装する。
     * 
     *      */
    function getByGroup($match_info,$begin=0){
        $pattern = "#(予選リーグ|準々決勝|準決勝|決勝)#";
        if(is_array($match_info)){
            for($i = $begin; $i < count($match_info); $i++){
                if(preg_match($pattern, $match_info[$i])){
                    $return_info[] = $this->getByGroup($match_info,$i);
                }
            }
            return $return_info;
        }else{
            return array_slice($match_info, $begin);
        }
    }



    /* ヤマザキナビスコ杯用
     * グループステージの１節毎に試合結果を返す
     * 予選のみ対応（suponiti)
     * 
     * @param array $match_info
     * @param int $begin
     * @return array $result
     * 
     *      */
    public function getDataByGroupStage($match_info,$begin=0){
        $match_temp = array();
        $tmp_count;     //分割に使用
        /*リーグ別に分割*/
        $tmp = array(); //一時保管用
        $pattern = "#(予選リーグ|準々決勝|準決勝|決勝)#";
        $flag = FALSE;
        
        
        /*ステージの取得*/
        for($i = 0; $i < count($match_info); $i++){
                if(preg_match("#(予選リーグ|準々決勝|準決勝|決勝)#",$match_info[$i],$m)){
                    $stage[$m[0]][] = $i;
                }
        }
        //debug($stage);
        
         /*リーグ毎の処理の分割
          * リーグ毎に処理を分ける場合
          * array_key_exitesで判定を行い、処理を分ける
          * 
          *           */
             $slice_count = array();
             foreach($stage as $var){
                 for($i = 0; $i < count($var);$i++){
                     $slice_count[] = $var[$i];
                 }
             }
             //debug($slice_count);
             
             /*返却用配列の作成*/
             $result['match_info'] = array_slice($match_info,0,$slice_count[0]);
             $match_count = count($result['match_info']);
             for($i = 0; $i < count($slice_count); $i++){
                 $next_i = $i + 1;
                 if(!array_key_exists($next_i, $slice_count)){
                     //最後のデータ
                     //debug($slice_count[$i]);
                     $result[] = array_slice($match_info,$slice_count[$i]);
                 }else{
                     $result[] = array_slice($match_info,$slice_count[$i],$slice_count[$i+1] - $match_count);
                 }
             }
       
        //debug($result);
        
        return $result;
    }
    
   
    /*開催日排列を毎のデータに分割し,先頭に節を挿入して返す
     *
     * @param  array $result_s
     * @return array $result
     * 
     *      */
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
        preg_match("#(?P<month>\d+)(/|月)(?P<date>\d+)#", $str, $temp_date);
        //debug($temp_date);
        
        $month_temp = $temp_date['month'];
        $date_temp =$temp_date['date'];
        
        $date_s = $year."-".$month_temp."-".$date_temp;
        //var_dump($date_s);
        //日付の生成
        $date = date("Y-m-d", strtotime($date_s));
        
        return $date;
    }
    
     //日付からを月を取得（日を返すように変更も可）
    public function getMonthString($str){
        preg_match("#(?P<month>\d+).+(?P<date>\d+)#", $str, $temp_date);
        
        $month = $temp_date['month'];
        $date =$temp_date['date'];

        return $month;
    }
    
    //文字列から時刻（○○:○○)を取得
    public function getTimesOfDayString($str){
        preg_match("#\d{1,2}:+\d{1,2}#", $str, $temp_date);
        
        $times_of_day = $temp_date[0];

        return $times_of_day;
    }
    
    //受け取ったデータを同一日付別の排列にして返す
    public function getDateDifferent($result_s){
        $result_d = array();    //日付毎にデータを保持
        foreach($result_s as $var){
            $pattern_begin = "#(.+月.+日(.+))|(\d+/\d+)#";
            $pattern_end = "#(.+月.+日(.+))|(\d+/\d+)#";
            $result_d[] = $this->getIndividual($pattern_begin, $pattern_end, $var);
        }
        //var_dump($result_d);
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
                //debug($pattern_begin);
                //debug($pattern_end);
                $flag = TRUE;         //whileループを抜ける判定に使用
                $data  = array();    //結果を格納
                //var_dump($result);
                   /*個別に取得*/
                   for($i  = 0 ; $i < count($result); $i++){
                        if(!$flag){
                            //var_dump("Falseになったので抜ける");
                            break;
                        }
                        //var_dump($result[$i]);
                        //探す初めのデータかチェック         
                        if(preg_match($pattern_begin, $result[$i])){
                            //var_dump("データを発見");
                            $j = $i;    //くじ結果の配列添え字番号を記録
                            //次の行の手前までデータを取得する
                            while(preg_match($pattern_end, $result[$j+1] ) == 0) {
                                    $data[] = $result[$j];
                                    $j++;
                                    //最終行なら抜ける
                                    if(!isset($result[$j +1])){
                                        //var_dump('最終行なので抜けます');
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

     /*ヤマザキナビスコカップの情報を取得
      * Yahoo 
      *       */
     public function getYamazakiCupInfoByY($url = YAMAZKI_MATCH_RESULT_YAHOO,$param = ""){
        $url = $url.$param;
        //debug($url);
        //Goutteオブジェクト生成
        $crawer = new Goutte\Client();
        
        //順位を取得
        $crawler = $crawer->request('GET',$url);
        
        $craw_result = array();     //取得結果を保持
        $item_name = array();   
        $match_info = array();      //試合結果の保持
        $year;                      //年度
        $month;                      //月
        $date;                      //日
        $now_date;
        $tournament;                //トーナメントの状況（予選、準決勝、決勝etc...）
        $data_time;                 //データの年月日
        $nabisuco_result = array(); //ナビスコカップの結果を保持
        
        //更新日の取得（年度に使用）
        $now_date = date('Y-m-d');  //年月に使用
        
        $item_name = array("キックオフ","ホーム","試合状況","ホーム得点","アウェイ得点","アウェイ","試合会場");
        $data_item = array("section","date_s","date","home","score",
                             "home_score","away_score","away","start_time","stadium","yaer","month");
        
        debug($url);
        
        //トーナメント（状況）を取得
        $crawler->filter('.h02_c h3' )->each(function( $node )use(&$tournament){
            $tournament[] = $node->text();
        });
        //debug($tournament);
        
        $year = "2014";
        //情報を取得
        $crawler->filter('.h02_c h3, .paragraph_tbl table td' )->each(function( $node )use(&$craw_result,$year){
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
        
        //debug($craw_result);
        
        //空白行を取り除いて整列
        $result = $this->fixDataBlank($craw_result);
        //debug($result);
        
        $preliminary_all = array(); //予選の全情報を保持
        $quarter_finals = array();  //順々決勝の情報を保持
        $semi_finals = array();     //準決勝の情報を保持
        $finals = array();          //決勝の情報を保持
        
        $pattern_begin = "/.?準々決勝.?/";
        $pattern_end = "/.?(準々決勝|準決勝|決勝|予選).?/";
        /*トーナメントの段階毎にデータを整形*/
        //準々決勝の情報を取得
        $quarter_finals =  $this->getIndividual($pattern_begin, $pattern_end, $result);
        //debug($quarter_finals);
        //準決勝の情報を取得
        $pattern_begin = "/.?準決勝.?/";
        $semi_finals = $this->getIndividual($pattern_begin, $pattern_end, $result);
        //debug($semi_finals);
        //決勝の情報を取得
        $pattern_begin = "/^決勝$/";
        $finals = $this->getIndividual($pattern_begin, $pattern_end, $result);
        //debug($finals);
        
        /*試合毎に分割*/
        //準々決勝を取得
        $tornament_section = $quarter_finals[0];
        array_shift($quarter_finals);
        $temp_result[$tornament_section] = array_chunk($quarter_finals, count($item_name));
        //$temp_result[] = $this->formatDataByDate($quarter_finals);
        //debug($temp_result);
       
        //準決勝を取得
        $tornament_section = $semi_finals[0];
        array_shift($semi_finals);
        $temp_result[$tornament_section] = array_chunk($semi_finals, count($item_name));
        //debug($temp_result);
        
        //決勝を取得
        $tornament_section = $finals[0];
        array_shift($finals);
        $temp_result[$tornament_section] = array_chunk($finals, count($item_name));
        //debug($temp_result);
        
        
        /*DB登録用にデータを整形*/
        
        //予選
        
        
        
        //準々決勝
        foreach($temp_result["準々決勝"] as $temp){
            $d = $temp[0];
            $start_time = $this->getTimesOfDayString($d);
            $date = $this->changeDateType($year, $d);               //日付を取得
            $month = $this->getMonthString($d);                     //月を取得
            //$this->array_insert(&$temp, $date, 1);                //日付（DATE型)の挿入
            //$arra_splice($var,$count($var)-2,0,$start_time);        //開始時自国の挿入
            array_unshift($temp, "準々決勝");
            array_push($temp, $year,$month,"ヤマザキナビスコカップ");
            $nabisuco_result[] = $temp;
        }
        
        debug($nabisuco_result);
        
        //準決勝
        
        //決勝

        
//        /*DB登録用にデータを整形*/
//        foreach ($temp_result as $var){
//            $match_info[] = $this->formatResult($var, $year, $month);
//        } 
//        //debug($match_info);
//        return $match_info;
//         
//         
     }

        /* [関数名] array_insert
        * [機能] 配列の任意の位置へ要素を挿入し、挿入後の配列を返す
        * [引数]
        * @param array  &$array 挿入される配列（参照渡し）
        * @param string $insert 挿入する値
        * @param string $pos    挿入位置（先頭は0）
        * [返り値]
        * @return bool  $flag   成功した場合にTRUE、そうでないにFALSEを返す
        * 
        * 参考
        * http://phpjavascriptroom.com/exp.php?f=include/php/tips_array/ainsert.inc&ttl=%E9%85%8D%E5%88%97%E3%81%AE%E4%BB%BB%E6%84%8F%E3%81%AE%E4%BD%8D%E7%BD%AE%E3%81%AB%E8%A6%81%E7%B4%A0%E3%82%92%E6%8C%BF%E5%85%A5
        */
    function array_insert ( &$array, $insert, $pos ) {
           //引数$arrayが配列でない場合はFALSEを返す
           if (!is_array($array)) return false;
           //挿入する位置～末尾まで
           $last = array_splice($array, $pos);
           //先頭～挿入前位置までの配列に、挿入する値を追加
           array_push($array, $insert);
           //配列を結合
           $array = array_merge($array, $last);
           return true;
    }
     
     
     /*天皇杯の情報を取得*/
     public function getEmperorCupInfo(){
         
     }
     
     /*ACLの情報を取得*/
     public function getAclInfo(){
         
     }

    /*データ（配列）を日付で分割
     * 現在未使用
     * 使用する前に、動作確認を行う
     *      */
    public function formatDataByDate($result_s){
        for($i = 0; $i < count($result_s);$i++){
                /*日付のデータ探索*/
                if(preg_match("#(.+月.+日(.+))|(\d+/\d+)#", $result_s[$i])){
                    $match_date_i[] = $i;
                    //debug($match_date_i);
                    $match_date_s[] = $result_s[$i];
                }

        }
        debug($match_date_s);
        /*1節複数日開催の場合、日付別に分割（２日のみ対応）*/
        if(count($match_date_i) !== 1){
                foreach ($match_date_i as $var){
                    if($var <> 1){
                        $temp_result[] = array_slice($result_s,0,$var);
                        $temp_result[] = array_slice($result_s,$var, count($result_s));
                        //debug($temp_result);
                        $result_t = array();
                        foreach($temp_result as $var){
                                $result_t[] = $var; 
                        }
                        $result = $result_t;
                    }
                }
         }else{
                $result = $result_s;
         }
         
         return $result;
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
         *      年度
         *      
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
         
        /* コールバック関数（2回目の処理で再定義のエラー出る為未使用）
        function even($var){
            return ($var<> '');
        }
         */
        
        
         //改行無しのスペース(＆ｎｂｓｐ；)を半角スペースに置換して削除
         //参考URL
         //http://nanoappli.com/blog/archives/5429
         for ($i = 0 ; $i < count($result); $i++){
                  $result[$i] = trim( $result[$i], chr(0xC2).chr(0xA0) );
         }
         
         
        //取得したデータから空の部分を詰めて配列添え直し
        //$result =  array_filter($result,'even');  //コールバック関数未使用
        $result =  array_filter($result,'MyClass::even');   //コールバックメソッド使用
//          $result = array_filter($result, function($k) {
//              return $k <> '';
//          },
//          ARRAY_FILTER_USE_BOTH);
         
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

/*コールバックメソッド*/
class Myclass{
    static function even($var){
         return ($var<> '');
    }
}