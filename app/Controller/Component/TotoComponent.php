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
        
        /*Goal3の情報を取得*/
        $this->getGoal3VoteByYJ();
    }
    
    
    /*goal3の投票率を取得
     * goal2にも対応
     *
     * *      */
    public function getGoal3VoteByYJ(){
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
         * 
         * 連想配列　に格納するか、
         * 多重配列　に格納するか
         * 検討
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