<?php

/*Toto情報関連コンポーネント
 *
 *  */
use Goutte\Client;
require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';
/*定数*/
//TOTOオフィシャルサイト
define('TOTO_OFFICIAL','http://www.toto-dream.com/toto/');
//totoOne
define('TOTOONEURL', 'http://www.totoone.jp');
//TOTOの投票率の取得用URL
define('TOTO_VOTE_TOTO_ONE', 'http://www.totoone.jp/blog/datawatch/');
define('TOTO_VOTE_YJ',"http://toto.yahoo.co.jp/vote/toto");
//TOTOの投票率の過去取得用URL
define('TOTO_VOTE_TOTO_ONE_BACKNUMBER','http://www.totoone.jp/blog/datawatch/archives.php');
//TOTOONEURL
//define('TOT','http://www.totoone.jp/');

/*shellからモデルを使用する*/
$toto_vote_model = ClassRegistry::init('Totovote');

/*totoの投票率を返す*/
class TotoComponent extends Component{
    
    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
    
    /*toto公式サイトよりマッチ情報を取得*/
    public function getTotoMatchInfo($url = TOTO_OFFICIAL,$param = ""){
        
    }




    /*Yahhoより投票状況を取得
     * TOTO ONE から取得するデータと共存しながら投票率のデータを使用する。
     * 
     *      */
    public function getTotoVoteDetail($url = TOTO_VOTE_YJ,$param = ""){
        $url = $url.$param;
        
        //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //Toto投票率（全体）を格納
        $miniA_vote = array();  //miniAの投票率を格納
        $miniB_vote = array();  //miniBの投票率を格納
        $goal2_vote = array();  //goal2の投票率を格納
        $goal3_vote = array();  //goal3の投票率を格納
        $count_time;            //開催回の保持
        $count_item;            //開催回のTotoくじ種類を保持
        //debug($url);

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', $url);
        
        
        /*開催回（くじ種類）の取得*/
        $crawler_vote->filter('.toto_result_wr_ttl p')->each(function( $node )use(&$count_item){
            ##debug($node->text());
            $count_item[] = trim($node->text());
        });
        //debug($count_item);
        
        if(isset($count_item[0])){
            //var_dump("開催回の取得");
            preg_match("/\d{2,4}/", $count_item[0],$m);
            $count_time = (int)$m[0];  //開催回を保存
        }
        //debug($count_time);
        
        
        //投票率の解析、取得
        /*
        $crawler_vote->filter('#data_check_box2 td')->each(function($node)use(&$toto_vote)
        {
                if($node->text() !== NULL){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_vote[] =(string)$node->text();
                }

        });
        */
        
        /*TOTOの投票率の取得*/
        
        
        /*Mini(A B)の投票率の取得*/
        $this->getMiniVoteByYJ();
        /*Goal3の投票率の取得*/
        $this->getGoal3VoteByYJ();
    }
    
    /* TOTOの投票率を取得する、
     * 
     * 
     * 
     *      */
    public function getTotoVoteByYJ($param = ""){
        $url = TOTO_VOTE_YJ.$param;

        //totoマッチングと投票率を取得
        /* Yahoo Japan toto より取得 */
        $toto_title;               //開催回の取得
        $toto_team =array();       //チーム
        $toto_date =array();       //開催日
        $miniA_date = array();          //miniAの開催日
        $miniB_date = array();          //miniBの開催日
        $toto_vote =array();       //Goal3投票率（加工前　○○% )
        $count_time;
        //今回のtotoマッチングと投票率の取得
        //define('TOTO_GOAL3_VOTE', 'http://toto.yahoo.co.jp/vote/index.html');

        //Goutteオブジェクト生成
        $client_vote = new Client();

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', $url);
        
        //開催回の格納
        $crawler_vote->filter('.toto_result_wr_ttl p:nth-of-type(1)')->each(function($node)use(&$toto_title)
        {
                if(preg_match('/\d{2,4}/', $node->text(),$m)){
                    //echo (string)$node->text() . "<br />";
                    $toto_title = $m[0];
                }

        });
        //debug($toto_title);
        
        //開催日の格納
        $crawler_vote->filter('.toto_result_wr1_tbl01 td')->each(function($node)use(&$toto_date)
        {
                if(preg_match('/^[0-9]+\/[0-9]+/', $node->text())){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_date[] =(string)$node->text();
                }

        });
        //debug($toto_date);
      
        //対戦チームの格納
        $crawler_vote->filter('.bg_ylw td')->each(function($node)use(&$toto_team)
        {
                //$param = '/.?[ぁ-んァ-ヶー一-龠]*.+$/u';  //現在未使用
                if($node->text() !== NULL && preg_match('/[^\x01-\x7E]+/u', $node->text(),$m)){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_team[] =$m[0];
                }

        });
        //debug($toto_team);
        
        //matac対戦カードの取得
        
        $match_card = array();
        for($i = 0; $i < count($toto_date);$i++){
            $j= $i+1;
            $temp_card = array();
            $filter_param = '.bg_ylw td:nth-of-type('.$j.')';
            $crawler_vote->filter($filter_param)->each(function($node)use(&$match_card,&$temp_card)
            {
                //$param = '/.?[ぁ-んァ-ヶー一-龠]*.+$/u';  //現在未使用
                if($node->text() !== NULL){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $temp_card[] =trim($node->text());
                }
            });
            $match_card[] = $temp_card;
        }
        
        //debug($match_card);
        
        
        //投票率の格納
        $crawler_vote->filter('.toto_result_wr1_tbl01 img')->each(function($node)use(&$toto_vote)
        {
                if($node->attr('title') !== NULL){
                    //echo (string)$node->attr('title') . "<br />";
                    //var_dump($node->text());
                    $toto_vote[] =(string)$node->attr('title');
                }

        });

        //debug($toto_vote);
        
        /*投票率を処理して格納
         * $vote          点数ごとの投票率を全て格納
         * $toto_taam     チームを順番に格納  
         * $team_vote     チーム毎に連想配列で得点別点数投票率を格納
         */

        //チーム毎に連想配列で得点別点数投票率を格納
        $team_vote = array();
        $vote = array();

        for($i = 0; $i < count($toto_vote); $i++){
            //投票率から%を取り除く
            preg_match('/^[\d]{1,2}.\d{1,2}/', $toto_vote[$i],$m);
            $vote[] = (float)$m[0];
        }
        
        //debug($vote);
        
        $item_list = array("0","1","2");    //データ切り分けに使用
        
        /*各対戦カード毎にデータを整形*/
        $vote = array_chunk($vote, count($item_list));
        
        //debug($vote);
        
        $h_data_a = array();   //投票率の先頭に追加
        $h_data_b = array();   //投票率の先頭に追加
        
        //各チーム毎に配列（投票率）を作成
        /* 
         * array(
         *      開催回
         *      第何番目？
         *      ホームアウェイ
         *      日付（date型）
         *      チーム名
         *      0の投票率
         *      1の投票率
         *      2の投票率
         *      
         */
        
        $year; //年を指定（後で動的に変更）
        $month = "3";
        
        if((int)$toto_title >= 750 ){
            $year = "2015";
        }else{
            $year = "2014";
        }
        $date = array();
        $vote_result = array();
        $result = array();
        
        
        //データをDB登録形式に変換
        if(count($vote) !== 0){
            //A B 両方の組の処理
            for($i = 0; $i < count($match_card);$i++){
               //A組の処理
               $tmp = $vote[$i];
               foreach($match_card[$i] as $var){
                   //日付ならdate型に変換
                   if(preg_match('#(?P<month>\d+)(/|月)(?P<date>\d+)#', $var)){
                       $var = $this->changeDateType($year, $var);
                   }
                   array_unshift($tmp, $var);
               }
               array_push($tmp,$year,$month);
               array_unshift($tmp, $toto_title,$i);
               $vote_result[] = $tmp;
            }
        }else{
            //データ未取得の処理
           /*記述する*/
        }
        //debug($vote_result);
     
        
        /*配列→連想配列*/
        foreach($vote_result as $var){
            $temp = array();
            for($i = 0; $i < count($var);$i++){
                if($i == 0){
                    $temp['held_time'] = $var[$i];
                }elseif($i == 1){
                    $temp['no'] = $var[$i] +1;
                }elseif($i == 2){
                    $temp['away_team'] = $var[$i];
                }elseif($i == 3){
                    $temp['home_team'] = $var[$i];
                }elseif($i == 4){
                    $temp['held_date'] = $var[$i];
                }elseif($i == 5){
                    $temp['1_vote'] = $var[$i];
                }elseif($i == 6){
                    $temp['0_vote'] = $var[$i];
                }elseif($i == 7){
                    $temp['2_vote'] = $var[$i];
                }elseif($i == 8){
                    $temp['year'] = $var[$i];
                }
                elseif($i == 9){
                    $temp['month'] = $var[$i];
                }
            }
            $result[] = $temp;
        }
        //debug($result);
        return $result;
    }
    
    
    
    
    
    /* Mini A mini B 両方を取得し、
     * 単一の場合はminiAとして返す。
     * 
     * 単一取得する関数作成のこと
     * 
     *      */
    public function getMiniVoteByYJ(){
        $url = "http://toto.yahoo.co.jp/vote/toto?id=0698";

        //totoGOAL3マッチングと投票率を取得
        /* Yahoo Japan toto より取得 */
        $toto_mini_title;               //開催回の取得
        $toto_mini_team =array();       //チーム
        $toto_mini_date =array();       //開催日
        $miniA_date = array();          //miniAの開催日
        $miniB_date = array();          //miniBの開催日
        $toto_mini_vote =array();       //Goal3投票率（加工前　○○% )
        $count_time;
        //今回のtotoマッチングと投票率の取得
        //define('TOTO_GOAL3_VOTE', 'http://toto.yahoo.co.jp/vote/index.html');

        //Goutteオブジェクト生成
        $client_vote = new Client();

        //totoマッチング、投票率HTMLを取得
        $crawler_mini_vote = $client_vote->request('GET', $url);

        /*TotoGoal3のデータ全体の取得*/
        /*
        $all_data = array();  //開催日を保持
        $crawler_mini_vote->filter('')->each(function($node)use(&$all_data){
                    $all_data[] = $node->text();
        });
        */
        
        
        //開催回の格納
        $crawler_mini_vote->filter('.toto_result_wr_ttl p:nth-of-type(1)')->each(function($node)use(&$toto_mini_title)
        {
                if(preg_match('/\d{2,4}/', $node->text(),$m)){
                    //echo (string)$node->text() . "<br />";
                    $toto_mini_title = $m[0];
                }

        });
        
        //開催日の格納
        $crawler_mini_vote->filter('.toto_result_toto_tbl01 td')->each(function($node)use(&$toto_mini_date)
        {
                if(preg_match('/^[0-9]+\/[0-9]+/', $node->text())){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_mini_date[] =(string)$node->text();
                }

        });
        //debug($toto_mini_date);
        
        if(count($toto_mini_date) < 5){
            //mini単体の開催
            /*特になにもしない*/
        }else{
            $tmp_date = array_chunk($toto_mini_date, 5);
            //miniAの日付を取得
            $miniA_date = $tmp_date[0];
            //miniBの日付を取得
            $miniB_date = $tmp_date[1];
        }

        
        //対戦チームの格納
        $crawler_mini_vote->filter('.bg_blue td')->each(function($node)use(&$toto_mini_team)
        {
                //$param = '/.?[ぁ-んァ-ヶー一-龠]*.+$/u';  //現在未使用
                if($node->text() !== NULL && preg_match('/[^\x01-\x7E]+/u', $node->text(),$m)){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_mini_team[] =$m[0];
                }

        });
        //debug($toto_mini_team);
        
        //matac対戦カードの取得(各Ａ、Ｂ組の1試合目～5試合目を取得)
        
        $match_card = array();
        for($i = 0; $i < count($toto_mini_date)/2;$i++){
            $j= $i+1;
            $temp_card = array();
            $filter_param = '.bg_blue td:nth-of-type('.$j.')';
            $crawler_mini_vote->filter($filter_param)->each(function($node)use(&$match_card,&$temp_card)
            {
                //$param = '/.?[ぁ-んァ-ヶー一-龠]*.+$/u';  //現在未使用
                if($node->text() !== NULL){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $temp_card[] =trim($node->text());
                }
            });
            if(count($temp_card) > 3){
                //A B 両方存在する場合分けて格納
                $temp_card = array_chunk($temp_card, 3);
            }
            $match_card[] = $temp_card;
        }
        
        //debug($match_card);
        
        
        //投票率の格納
        $crawler_mini_vote->filter('.toto_result_wr2 img')->each(function($node)use(&$toto_mini_vote)
        {
                if($node->attr('title') !== NULL){
                    //echo (string)$node->attr('title') . "<br />";
                    //var_dump($node->text());
                    $toto_mini_vote[] =(string)$node->attr('title');
                }

        });

        //debug($toto_mini_vote);
        
        /*投票率を処理して格納
         * $mini_vote          点数ごとの投票率を全て格納
         * $toto_mini_taam     チームを順番に格納  
         * $team_mini_vote     チーム毎に連想配列で得点別点数投票率を格納
         */

        //チーム毎に連想配列で得点別点数投票率を格納
        $team_mini_vote = array();
        $mini_vote = array();

        for($i = 0; $i < count($toto_mini_vote); $i++){
            //投票率から%を取り除く
            preg_match('/^[\d]{1,2}.\d{1,2}/', $toto_mini_vote[$i],$m);
            $mini_vote[] = (float)$m[0];
        }
        
        //debug($mini_vote);
        
        $item_list = array("0","1","2");    //データ切り分けに使用
        
        /*各対戦カード毎にデータを整形*/
        $mini_vote = array_chunk($mini_vote, count($item_list));
        
        //debug($mini_vote);
        
        $h_data_a = array();   //投票率の先頭に追加
        $h_data_b = array();   //投票率の先頭に追加
        
        /*A組のヘッダーデータを作成*/
        if(isset($match_card[0][0]) || isset($match_card[0])){
            for($i = 0; $i < count($match_card); $i++){
                $h_data_a[] = $match_card[$i][0];
            }
        }
        //debug($h_data_a);
        /*B組のヘッダーデータを作成*/
        if(isset($match_card[0][1])){
            for($i = 0; $i < count($match_card); $i++){
                $h_data_b[] = $match_card[$i][1];
            } 
        }
        //debug($header_data_b);
        //各チーム毎に配列（投票率）を作成
        /* 
         * array(
         *      開催回
         *      組(A  or  B)
         *      第何番目？
         *      ホーム？アウェイ？
         *      日付
         *      日付（date型）
         *      チーム名
         *      0の投票率
         *      1の投票率
         *      2の投票率
         *      
         */         
        $year = "2014";
        $date = array();
        $vote_result = array();
        $result = array();
        //日付の取得
        //for($i= 0; $i < count($toto_mini_date); $i++){
        //    $date[] = $this->changeDateType($year, $toto_mini_date[$i]);
        //}
        
        $year = "2014"; //年を指定（後で動的に変更）
        
        //データをDB登録形式に変換
        //A組のデータを生成
        if(count($mini_vote) === 10){
            //A B 両方の組の処理
            for($i = 0; $i < count($h_data_a);$i++){
               //A組の処理
               $tmp = $mini_vote[$i];
               foreach($h_data_a[$i] as $var){
                   //日付ならdate型に変換
                   if(preg_match('#(?P<month>\d+)(/|月)(?P<date>\d+)#', $var)){
                       $var = $this->changeDateType($year, $var);
                   }
                   array_unshift($tmp, $var);
               }
               array_push($tmp,"A");
               array_unshift($tmp, $toto_mini_title,$i);
               $vote_result[] = $tmp;
            }
            for($i = 0; $i < count($h_data_b); $i++){
               //A組の処理
               $tmp = $mini_vote[$i];
               foreach($h_data_b[$i] as $var){
                   //日付ならdate型に変換
                   if(preg_match('#(?P<month>\d+)(/|月)(?P<date>\d+)#', $var)){
                       $var = $this->changeDateType($year, $var);
                   }
                   array_unshift($tmp, $var);
               }
               array_push($tmp,"B");
               array_unshift($tmp, $toto_mini_title,$i);
               $vote_result[] = $tmp; 
            }
        }else{
            //単一組（A組）として処理
            /*記述する*/
        }
        //debug($vote_result);
     
        
        /*配列→連想配列*/
        foreach($vote_result as $var){
            $temp = array();
            for($i = 0; $i < count($var);$i++){
                if($i == 0){
                    $temp['held_time'] = $var[$i];
                }elseif($i == 1){
                    $temp['no'] = $var[$i] +1;
                }elseif($i == 2){
                    $temp['away_team'] = $var[$i];
                }elseif($i == 3){
                    $temp['home_team'] = $var[$i];
                }elseif($i == 4){
                    $temp['held_date'] = $var[$i];
                }elseif($i == 5){
                    $temp['1_vote'] = $var[$i];
                }elseif($i == 6){
                    $temp['0_vote'] = $var[$i];
                }elseif($i == 7){
                    $temp['2_vote'] = $var[$i];
                }elseif($i == 8){
                    $temp['class'] = $var[$i];
                }
            }
            $result[] = $temp;
        }
        //debug($result);
        return $result;
    }
    
    /*配列の階層を返す*/
    private function arrayDepth($arr, $blank=false, $depth=0){
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
    
    
    /* Miniデータ整形用関数
     * $data        投票率の配列
     * $m_count     開催回
     * $s_count     何番目か
     * $mini_class  A組 or B組
     * $team        チーム名
     * $date        日付
     * $pos         ホーム　か　アウェイを表す
     * 
     *      
     */
    private function sortMiniData($data, $m_count, $s_count, $team, $pos, $date){
        /*1チームのデータ処理*/
        $temp_result = $data;
        //debug($temp_result);
        //$date = $this->changeDateType($year, $toto_goal3_date[0]);
        array_unshift($temp_result, $m_count,$s_count,$pos,
                        $team,$date);
        $result = $temp_result;
        //debug($vote_result);
        
        return $result;
    }
    
    
    
    /*            goal3の投票率を取得       ************
     * goal2にも対応
     *
     * * **********************************************/
    public function getGoal3VoteByYJ($url = "http://toto.yahoo.co.jp/vote/toto?id=0698" ){
        $url = "http://toto.yahoo.co.jp/vote/toto?id=0698";

        //totoGOAL3マッチングと投票率を取得
        /* Yahoo Japan toto より取得 */
        $toto_goal3_title;               //開催回の取得
        $toto_goal3_team =array();       //チーム
        $toto_goal3_date =array();       //開催日
        $toto_goal3_vote =array();       //Goal3投票率（加工前　○○% )
        $count_time;
        //今回のtotoマッチングと投票率の取得
        //define('TOTO_GOAL3_VOTE', 'http://toto.yahoo.co.jp/vote/index.html');

        //Goutteオブジェクト生成
        $client_vote = new Client();

        //totoマッチング、投票率HTMLを取得
        $crawler_goal3_vote = $client_vote->request('GET', $url);

        /*TotoGoal3のデータ全体の取得*/
        /*
        $all_data = array();  //開催日を保持
        $crawler_goal3_vote->filter('')->each(function($node)use(&$all_data){
                    $all_data[] = $node->text();
        });
        */
        
        
        //開催回の格納
        $crawler_goal3_vote->filter('.toto_result_wr_ttl p:nth-of-type(1)')->each(function($node)use(&$toto_goal3_title)
        {
                if(preg_match('/\d{2,4}/', $node->text(),$m)){
                    //echo (string)$node->text() . "<br />";
                    $toto_goal3_title = $m[0];
                }

        });

        $h_and_a = array("H","A","H","A","H","A");
        
        //開催日の格納
        $crawler_goal3_vote->filter('.bg_grn td')->each(function($node)use(&$toto_goal3_date)
        {
                if(preg_match('/^[0-9]+\/[0-9]+/', $node->text())){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_goal3_date[] =(string)$node->text();
                }

        });
        //debug($toto_goal3_date);

        //対戦チームの格納
        $crawler_goal3_vote->filter('.td_team td')->each(function($node)use(&$toto_goal3_team)
        {
                if($node->text() !== NULL){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_goal3_team[] =(string)$node->text();
                }

        });
        //debug($toto_goal3_team);
        
        //投票率の格納
        $crawler_goal3_vote->filter('.td_vote02 img')->each(function($node)use(&$toto_goal3_vote)
        {
                if($node->attr('title') !== NULL){
                    //echo (string)$node->attr('title') . "<br />";
                    //var_dump($node->text());
                    $toto_goal3_vote[] =(string)$node->attr('title');
                }

        });

        //debug($toto_goal3_vote);
        
        /*投票率を処理して格納
         * $goal3_vote          点数ごとの投票率を全て格納
         * $toto_goal3_taam     チームを順番に格納  
         * $team_goal3_vote     チーム毎に連想配列で得点別点数投票率を格納
         */

        //チーム毎に連想配列で得点別点数投票率を格納
        $team_goal3_vote = array();
        $goal3_vote = array();

        for($i = 0; $i < count($toto_goal3_vote); $i++){
            //投票率から%を取り除く
            preg_match('/^[\d]{1,2}.\d{1,2}/', $toto_goal3_vote[$i],$m);
            $goal3_vote[] = (float)$m[0];
        }
        
        //debug($goal3_vote);
        
        $item_list = array(
            "0","1","2","3more",
        );
        
        /*各日付毎にデータを整形*/
        $goal3_vote = array_chunk($goal3_vote, count($item_list));
        
        //debug($goal3_vote);
        
        //各チーム毎に配列（投票率）を作成
        /* 
         * array(
         *      開催回
         *      第何番目？
         *      ホーム？アウェイ？
         *      日付
         *      日付（date型）
         *      チーム名
         *      0の投票率
         *      1の投票率
         *      2の投票率
         *      3の投票率
         */         
        $year = "2014";
        $date = array();
        $vote_result = array();
        $result = array();
        //日付の取得
        for($i= 0; $i < count($toto_goal3_date); $i++){
            $date[] = $this->changeDateType($year, $toto_goal3_date[$i]);
        }
        
        //データをDB登録形式に変換
        $j = 0;
        for($i = 0; $i < count($goal3_vote); $i++){
            $pos = "Home";
            if($i % 2 === 0 && $i !== 0){
                $pos = "Home";
                $j++;
            }else if($i % 2 === 1){
                $pos = "Away";
            }
            $vote_result[] = $this->sortGoal3Data($goal3_vote[$i], $toto_goal3_title, $i+1, $toto_goal3_team[$i], $pos,$date[$j]);
        }
        //debug($vote_result);
        
        /*配列→連想配列*/
        foreach($vote_result as $var){
            $temp = array();
            for($i = 0; $i < count($var);$i++){
                if($i == 0){
                    $temp['held_time'] = $var[$i];
                }elseif($i == 1){
                    $temp['no'] = $var[$i];
                }elseif($i == 2){
                    $temp['position'] = $var[$i];
                }elseif($i == 3){
                    $temp['team'] = $var[$i];
                }elseif($i == 4){
                    $temp['held_date'] = $var[$i];
                }elseif($i == 5){
                    $temp['0_vote'] = $var[$i];
                }elseif($i == 6){
                    $temp['1_vote'] = $var[$i];
                }elseif($i == 7){
                    $temp['2_vote'] = $var[$i];
                }elseif($i == 8){
                    $temp['3_vote'] = $var[$i];
                }
            }
            $result[] = $temp;
        }
        //debug($result);
        return $result;
    }
    
    
    /* GOAL3データ整形用関数
     * $data        投票率の配列
     * $m_count     開催回
     * $s_count     何番目か
     * $team        チーム名
     * $date        日付
     * $pos         ホーム　か　アウェイを表す
     * 
     *      
     */
    private function sortGoal3Data($data, $m_count, $s_count, $team, $pos, $date){
        /*1チームのデータ処理*/
        $temp_result = $data;
        //debug($temp_result);
        //$date = $this->changeDateType($year, $toto_goal3_date[0]);
        array_unshift($temp_result, $m_count,$s_count,$pos,
                        $team,$date);
        $result = $temp_result;
        //debug($vote_result);
        
        return $result;
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
    
    
    
    /*TOTOONEより投票率の結果を取得*/
    public function getTotoVote($toto_vote_url = TOTO_VOTE_TOTO_ONE){
        //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //Toto投票率を格納
        debug($toto_vote_url);

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', $toto_vote_url);
        
        //開催回の取得
        $crawler_vote->filter('.txt_lead1')->each(function( $node )use(&$toto_vote){
            //debug($node->text());
            $toto_vote[] = $node->text();
        });
        
        //投票率の解析、取得
        $crawler_vote->filter('#data_check_box2 td')->each(function($node)use(&$toto_vote)
        {
                if($node->text() !== NULL){
                    //echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_vote[] =(string)$node->text();
                }

        });
        
        //取得したデータを整理
        $toto_vote_result = array();              //連想配列で投票率を保持
        $toto_vote_result_2 = array();            //連想配列で投票率を保持
        $toto_vote_result_3 = array();            //連想配列で投票率を保持
        
        /*開催回の保持*/
        $held_temp = $toto_vote[0];
        //debug($held_times);
        preg_match('/[0-9]+/', $held_temp,$held_time_array);
        $held_time = $held_time_array[0];   //開催回の保持変数
        //$toto_vote_result['held_time'] = $held_time_array[0];   //投票率結果１の配列に格納
        //$toto_vote_result_2['held_time'] = $held_time_array[0]; //投票率結果２の配列に格納
        
        /*開催日の保持*/
        $held_date = $toto_vote[7];         
        //debug($held_date);
        //文字列からDATE型へ変換
        //date_default_timezone_set('ja');
        $held_date = '2014/'.$held_date;    //一時用2014年の日付に変換
        $held_datatime = strtotime($held_date);
        //var_dump($held_datatime);
        $held_date = date('Y-m-d',$held_datatime);
        
        //$toto_vote_result['held_date'] = $held_date;    //投票率結果１の配列に格納
        //$toto_vote_result_2['held_date'] = $held_date;//投票率結果２の配列に格納
        //var_dump($toto_vote_result['held_date']);
        
        //取得結果から余分なデータの削除
        array_splice($toto_vote, 0, 7);     //始めの余分なデータを削除
        $toto_vote = array_filter($toto_vote,"strlen");    //空要素の削除
        $toto_vote = array_values($toto_vote);      //添え字を直す
        
        //debug($toto_vote);
        
        $date_no;   
        //連想配列の作成（結果データの生成）
        
        for($i = 1; $i < count($toto_vote); $i++){
            if(preg_match('/^[0-9]+.[0-9]+/', $toto_vote[$i])){
                $date_no = $i;
                break;
            }
        }
        
        
        $vote_temp =  array_chunk($toto_vote, $date_no);        //一試合毎に分割
        //debug($vote_temp);
        
        //debug($vote_temp);
        
        /*toto_vote_result araay
        /* array(      
         *         各対戦カードの情報
         *         .................
         * )
         */
        foreach ($vote_temp as $var){
            //始めの不要データを取り除いて格納
            $toto_data = array_slice($var, 1,count($var));    //対戦カード～各々の投票率
            array_unshift($toto_data, $held_time,$held_date);
            $toto_vote_result[] = $toto_data;
        }
        //debug($toto_vote_result);
 
        /* データの別の保管方法*/
        /*toto_vote_result_2 araay
        /* array
         *      'held_time' => '開催回'
         *      'held_date' => '開催日'
         *      'vote' => 'NO'
         *             => 'card'
         *             => 'all_vote'
         *             => '1_vote'
         *             => '0_vote'
         *             => '2_vote'
         */
        /*
        foreach ($vote_temp as $var){
            $toto_vote_result_2['held_time'][] = $held_time;
            $toto_vote_result_2['held_date'][] = $held_date;
            $toto_vote_result_2['vote']['No'][] = $var[1];
            $toto_vote_result_2['vote']['card'][] = $var[2];
            $toto_vote_result_2['vote']['all_vote'][] = $var[3];
            $toto_vote_result_2['vote']['1_vote'][] = $var[4];
            $toto_vote_result_2['vote']['0_vote'][] = $var[5];
            $toto_vote_result_2['vote']['2_vote'][] = $var[6];
        }
        */
        /*
         * データの保管方法
         *  array = array(
         *              'held_time'
         *              'held_date'
         *              .....
         *          )
         *          ......
         *          */
        
        //debug(count($toto_vote_result[0]));
        //toto data only 
        if(count($toto_vote_result[0]) === 9){
                $toto_vote_result_3[0]['held_time'] =0;
                debug($toto_vote_result_3);
        }else{
            foreach ($vote_temp as $var){
                $temp_array = array();
                $temp_array['held_time'] = $held_time;
                $temp_array['held_date'] = $held_date;
                $temp_array['No'] = $var[1];
                $temp_array['card'] = $var[2];
                $temp_array['all_vote'] = trim(str_replace(array("\r\n", "\n", "\r"), '', $var[3]));
                //var_dump($temp_array['all_vote']);
                //debug($temp_array['all_vote']);
                $temp_array['1_vote'] = $var[4];
                $temp_array['0_vote'] = $var[5];
                $temp_array['2_vote'] = $var[6];
                $toto_vote_result_3[] = $temp_array;
            }
        }
        
        //debug($toto_vote_result);
        //debug($toto_vote_result_2);
        
        
        /*取得したデータのDB登録*/
        //$this->setTotoVotesTable($toto_vote_result[3]);
        /*取得した配列を順番に登録処理メソッドへ*/
//        foreach ($toto_vote_result as $var){
//            $this->setTotoVotesTable($var);
//        }
        
        
        //debug($toto_vote_result);
        //debug($toto_vote_result_3);
        
        /*投票率を返す*/
        return $toto_vote_result_3;
    }
    
    //チームの情報を取得
    public function getTotoTeamInfo(){
        //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //チーム情報を格納

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', TOTO_VOTE_TOTO_ONE);
        
        
    }
    
    //過去の投票率の取得
    public function getPastTotoVote($past_vote_url = TOTO_VOTE_TOTO_ONE_BACKNUMBER){
        
         //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //チーム情報を格納

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', $past_vote_url);
        
        //$crawler_vote->filter('#data_check_box1 > a')->attr('href');
        //バックナンバーの取得
        /*1ページ分の過去の投票率へのリンクを取得*/
        $links = $crawler_vote->filter('div.datawatch-archives a')->each(function( $node ){
            return array(
            'link_loc' => TOTOONEURL. $node->attr('href'),
            //'content_loc' => $node->attr('src')
            );
        });
        //debug($links);
        
        /*個々のバックナンバーを取得*/
//        $crawler_vote->filter('td')->each(function($node)use(&$toto_vote)
//        {
//                if($node->text() !== NULL){
//                    //echo (string)$node->text() . "<br />";
//                    //var_dump($node->text());
//                    $toto_vote[] =(string)$node->text();
//                }
//
//        });
//        
//        return $crawler_vote;
        return $links;
    }
    
    //selectLink(Gouttteライブラリ)
    // xpathを内部で使用
    public function test($value){
     //public function selectLink($value)

        $xpath = sprintf('descendant-or-self::a[contains(concat(\' \', normalize-space(string(.)), \' \'), %s) ', static::xpathLiteral(' '.$value.' ')).
        sprintf('or ./img[contains(concat(\' \', normalize-space(string(@alt)), \' \'), %s)]]', static::xpathLiteral(' '.$value.' '));

        return $this->filterRelativeXPath($xpath);
    }
    
    /*totovotesテーブルにデータを登録
     *  $toto_vote_result 投票率を格納した配列
     *  */
    public function setTotoVotesTable($toto_vote_result){
        /*Totovoteモデルを使用する
         * Compnent からmodel を使用する場合
         * ClassResistry::init()　を使用して指定する
        */
        $toto_vote_Instance = ClassRegistry::init('Totovote');
        //$toto_vote_set_result;  //SQL実行結果の格納
        //$toto_vote_info = $toto_vote_result['vote'];
       
        //debug($toto_vote_result);
        
        /*登録しようとしている回が登録されているか判定*/
        $options = array(
            'conditions' => array(
                //'heldtime' => 700,    //テスト用
                'heldtime' => $toto_vote_result[0],
                'no' => $toto_vote_result[2],
            )
        );
        $first_result =  $toto_vote_Instance->find('first',$options);
        debug($first_result);
        
       
        /*データ登録処理*/
        if(!$first_result){
            //INSERT
            //データの作成
            /*
            $data = array(
                        'Totovote' =>array(
                            'heldtime' => $toto_vote_result['held_time'],
                            'helddate' => $toto_vote_result['held_date'],
                            'no' => $toto_vote_result['vote']['No'][0],
                            'card' => $toto_vote_result['vote']['card'][0],
                            'all_vote' => $toto_vote_result['vote']['all_vote'][0],
                            '1_vote' => $toto_vote_result['vote']['1_vote'][0],
                            '0_vote' => $toto_vote_result['vote']['0_vote'][0],
                            '2_vote' => $toto_vote_result['vote']['2_vote'][0],
            ));
             * 
             */
            $data = array(
//                        'Totovote' =>array(
                            'heldtime' => $toto_vote_result[0],
                            'helddate' => $toto_vote_result[1],
                            'no' => $toto_vote_result[2],
                            'card' => $toto_vote_result[3],
                            'all_vote' => $toto_vote_result[4],
                            '1_vote' => $toto_vote_result[5],
                            '0_vote' => $toto_vote_result[6],
                            '2_vote' => $toto_vote_result[7],
  //                          )
                );
            
            if(!empty($data)){
                //$toto_vote_set_result = $toto_vote_Instance->save($data);
                $toto_vote_set_result = $toto_vote_Instance->save($data);
                debug($toto_vote_set_result);
            }
        }else{
            //UPDATE
            /*
            $data = array(
                        //'Totovote' =>array(
                            'helddate' => "'". $toto_vote_result['held_date'] ."'",
                            'no' => "'". $toto_vote_result['vote']['No'][0] ."'",
                            'card' => "'". $toto_vote_result['vote']['card'][0] ."'",
                            'all_vote' => "'". $toto_vote_result['vote']['all_vote'][0] ."'",
                            '1_vote' => "'". $toto_vote_result['vote']['1_vote'][0] ."'",
                            '0_vote' => "'". $toto_vote_result['vote']['0_vote'][0]. "'",
                            '2_vote' => "'". $toto_vote_result['vote']['2_vote'][0]. "'",
            );
            $conditions = array(
                'heldtime' => $toto_vote_result['held_time'],
            );
             * 
             */
            $data = array(
                        //'Totovote' =>array(
                            'helddate' => "'". $toto_vote_result[1] ."'",
                            'no' => "'". $toto_vote_result[2] ."'",
                            'card' => "'". $toto_vote_result[3] ."'",
                            'all_vote' => "'". $toto_vote_result[4] ."'",
                            '1_vote' => "'". $toto_vote_result[5] ."'",
                            '0_vote' => "'". $toto_vote_result[6]. "'",
                            '2_vote' => "'". $toto_vote_result[7]. "'",
            );
            //debug($data);
            $conditions = array(
                'heldtime' => $toto_vote_result[0],
                'no' => $toto_vote_result[2],
            );
            if(!empty($data) && !empty($options)){          
                $toto_vote_up_result = $toto_vote_Instance->updateAll($data,$conditions);
                debug($toto_vote_up_result);
            }
        }

//        
//        debug($toto_vote_set_result);
    }
    
    /*totovotesテーブルからデータを読み込み*/
    public function getTotoVoteDb(){
        $data = $this->Totovote->find('all');
        debug($data);
    }
    
}
?>