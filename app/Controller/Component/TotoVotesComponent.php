<?php

/*Toto情報関連コンポーネント
 *
 *  */
use Goutte\Client;
require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';

//define('TOT','http://www.totoone.jp/');

/*shellからモデルを使用する*/
$toto_vote_model = ClassRegistry::init('Totovote');
$toto_mini_model = ClassRegistry::init('Minivote');
$toto_vote_model = ClassRegistry::init('Goal3vote');

/*totoの投票率を返す*/
class TotoVotesComponent extends Component{
    
    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
    public $components = array("Toto");
    
    /*toto公式サイトよりマッチ情報を取得
     *
     * 
     * 
     *      */
    public function getTotoMatchInfo($url = TOTO_OFFICIAL,$param = ""){
        //Goutteオブジェクト生成
        $client_vote = new Client(); 

        /*Goutteライブらリを使用して次ページの遷移の場合*/
        $client = new \Goutte\Client();
        
        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', $url);
        
        //debug($client_vote);
        $held_time = (int)$param + 1;
        //debug($held_time);
        
        /*official page より取得*/
        $held_time_o = $this->getHeldTime();
        //debug($held_time_o);
        
        /* 取得ページの決定
         * 
         * 
         * 
         */
        if($held_time == $held_time_o){
            /*DB＋1と最新回一緒(そのまま)*/
        }else if($held_time < $held_time_o){
            /*最新回取得の必要（Official Site）*/
            $held_time = $held_time_o;
        }else if($held_time > $held_time_o){
            $held_time = $held_time_o;
        }
        //debug($held_time);
        //$held_time = 754;
        /*次の回の開催回のページがあるかチェック*/
        $url_next = "http://www.toto-dream.com/dci/I/IPA/IPA01.do?op=disptotoLotInfo&holdCntId=0".$held_time;
        try{
            $craw = $client->request("GET", $url_next);
        } catch (Exception $ex) {
            /*official page より取得*/
            $held_time_str = $this->getHeldTime();
            preg_match("#\d+#", $held_time_str,$m);
            $held_time = $m[0]; //開催回の数字を保存
            $craw =  $this->transionTotoPage($held_time_str);
            
        }  finally {
            /*最終的な処理あれば*/
        }
        
        
        
        //debug($craw);
        
        
        /***** 遷移先から情報を取得  ****/
        
        /* 開催しているくじの取得 */
        $held_lot = array();    //開催くじの保持
        
        $craw->filter('p.non > a')->each(function( $node )use(&$held_lot){
            $lot = trim($node->text());
            $held_lot[$lot] = $lot;
        });
        
        /*重複、空データ削除*/
        $held_lot = array_filter($held_lot,"strlen");
        $held_lot = array_unique($held_lot);
        
        //var_dump($held_lot);
        
        $toto_match = array();      //Totoの試合情報の保持
        $box_no = NULL;             //テーブル番号の識別
        $result = array();          //返却用変数
        
        $result['held_time'] = $held_time;
        
        /*Toto(開催されている場合取得)*/
        if(array_key_exists("toto", $held_lot)){
             //var_dump("toto");
             $toto_match = $this->getTotoMatchCard($craw);
             //$box_no["toto"] = "toto";
             $box_no = "toto";
             $result['toto'] = $toto_match;
        }
        
        
       /*mini(開催されている場合取得)*/
       if(array_key_exists("mini toto A組", $held_lot)){
           //何番目のテーブルか取得
           $mini_a_match = $this->getMiniMatchCard($craw,"A",$box_no);
           //$box_no["mini-A"] = "mini-A";
           $box_no = $box_no."mini-A";
           //debug($mini_a_match);
           $result['mini-A'] = $mini_a_match;
       }
       if(array_key_exists("mini toto B組",$held_lot)){
           $mini_b_match = $this->getMiniMatchCard($craw,"B",$box_no);
           //$box_no["mini-B"] = "mini-B";
           $box_no = $box_no."mini-B";
           //debug($mini_b_match);
           $result['mini-B'] = $mini_b_match;
       }
       /*goal(開催されている場合取得)*/
       if(array_key_exists("totoGOAL3", $held_lot)){
            $goal3_match = $this->getGoal3MatchCard($craw,3);
            //debug($goal3_match);
            $result['goal'] = $goal3_match;
       }else if(array_key_exists("totoGOAL2", $held_lot)){
            $goal2_match = $this->getGoal3MatchCard($craw,2);
            $result['goal'] = $goal2_match;
       }
       
        return $result;
    }
    
    /*開催回の取得*/
    public function getHeldTime(){
        //Goutteオブジェクト生成
        $client_vote = new Client(); 

        /*Goutteライブらリを使用して次ページの遷移の場合*/
        $client = new \Goutte\Client();
        
        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', TOTO_OFFICIAL);
        
        //debug($client_vote);
        
        $held_times;  //開催回の取得
        
        /*現在表示されている開催回取得*/
        $crawler_vote->filter('.chancecopy img')->each(function( $node )use(&$held_times){
            $held = trim($node->attr("alt"));
            if($held){
                $held_times = $held;
            }
        });
        
        //debug($held_times);
        
        
        //
        if(!preg_match("#(第\d{3}回)#", $held_times)){
            $crawler_vote->filter('.chancecopy p')->each(function( $node )use(&$held_times){
            $held = trim($node->text());
            if($held){
                $held_times = $held;
            }
        });
        
            debug($held_times);
            $pattern = "#(第\d{3}回)#";
            preg_match_all($pattern, $held_times, $held_m);
            //debug($held_m);
            $held_time = $held_m[0][0];
            preg_match('/\d+/', $held_time,$m);
            $held_time = $m[0];
        
        }else{
         /* toto 開催回*/
             /**開催回のテキストリンクを作成
         * 
         */
            $pattern = "#(第\d{3}回)#";
            preg_match_all($pattern, $held_times, $held_m);
            $pre_held = $held_m[0][0];  //前回開催回の取得
            $held_time = $held_m[0][1]; //今回開催回の取得

            preg_match('/\d+/', $held_time,$m);
            $held_time = $m[0];
        }
        
        //var_dump($held_times);
        return $held_time;
    }
    
    
    /* totoの開催カードを取得 */
    public function getTotoMatchCard($craw){
        $match_info = array();  //対戦カード
        $match_date = array();  //対戦日
        $craw->filter('.kobetsu-format3  td')->each(function( $node )use(&$match_info,&$match_date){
            $info = trim($node->text());
            if(preg_match("#^\d{2}/\d{2}$#", $info)){
                //対戦日の格納
                $match_date[] = $info;
            }
                $match_info[] = $info;
        });
        
        //var_dump($match_info);
        
        $data_c = 8;
        $match_c = 13;
      
        $slice_count = $data_c * $match_c;
        
        $toto_match = array_slice($match_info, 0, $slice_count); //totoの情報だけ切り出し

//        unset($toto_match['VS']);
//        unset($toto_match['データ']);

        //var_dump($toto_match);
        
        $toto_match = array_values($toto_match);
        //debug($toto_match);
            
        
        /*対戦ごとに分割*/
        $size = 8;
        $match = array_chunk($toto_match, $size);
        //var_dump($match);
        
        
        return $match;
        
    }
    
    
    /* miniの開催カードの取得 */
    public function getMiniMatchCard($craw,$type,$box_no){
        $match_info = array();  //対戦カード
        $match_date = array();  //対戦日
        $mini_flag= FALSE;      //miniの情報を取り出すのに使用
        //debug($box_no);
        $craw->filter('.kobetsu-format3 td')->each(function( $node )use(&$match_info,&$match_date,&$mini_flag){            
            $info = trim($node->text());
            $match_info[] = $info;    
        });
        //debug($match_info);
        
        //var_dump($match_info);
        $data_c = 8;    //データの個数
        $match_c = 5;
        
        /*取得テーブルを設定*/
        $begin_no;
        if($box_no === NULL && $type === "A"){
            $begin_no = 0;  //toto未開催回(A組）
        }else if($box_no === NULL && $type === "B"){
            $begin_no = $data_c * 5; //toto未開催回(B組）
        }else if($box_no === "toto" && $type === "A"){
            $begin_no = $data_c * 13;   //toto存在回(A組)
        }else if($box_no === "totomini-A" && $type === "B"){
            $begin_no = $data_c * 13 + $data_c * 5; //toto存在回(B組）
        }
        //var_dump($begin_no);
        $slice_count = $data_c * $match_c;        
        //debug($match_info);
        
        $toto_match = array_slice($match_info, $begin_no, $slice_count); //miniの情報だけ切り出し
        
        
        $toto_match = array_values($toto_match);

        /*対戦ごとに分割*/
        $match = array_chunk($toto_match, $data_c);
        //debug($match);
        
        /*各カードにA組 or B組を付与*/
        $result = array();      //返却用
        foreach($match as $var){
            array_push($var, $type);
            $result[] = $var;
        }
        
        //debug($result);
        
        return $result;
        
    }
    
    /*goal3の開催カードの取得*/
    public function getGoal3MatchCard($craw,$type){
        $match_info = array();  //対戦カード
        $match_date = array();  //対戦日
        //debug($box_no);
        $craw->filter('.kobetsu-format3 td')->each(function( $node )use(&$match_info,&$match_date,&$mini_flag){            
            $info = trim($node->text());
            $match_info[] = $info;    
        });
        //debug($match_info);
        
        //var_dump($match_info);
        $data_c = 8;    //データの個数
        $match_c = $type;
        
        /*取得テーブルを設定*/
        $begin_no;
        
        $count = count($match_info);    //配列の個数
        $begin_no = $count - $data_c * $match_c;
        
        //var_dump($begin_no);
        $slice_count = $data_c * $match_c;        
        //debug($match_info);
//        
        $toto_match = array_slice($match_info, $begin_no, $slice_count); //goalの情報だけ取り出し
        //debug($toto_match);
        
        $toto_match = array_values($toto_match);

        /*対戦ごとに分割*/
        $result = array_chunk($toto_match, $data_c);
        //debug($result);
        
        return $result;
    }






    /*totoの詳細ページへ
     * 詳細ページのCrawClient インスタンスを返す
     * 
     *      */
    protected function transionTotoPage($held_time){
        
        if(strlen($held_time) < 4){ //3桁,4桁の場合のみ対応
            $held_time = "0".$held_time;
        }
        $url = "http://www.toto-dream.com/dci/I/IPA/IPA01.do?op=disptotoLotInfo&holdCntId=";
       
        $url = $url . $held_time;
        
         //Goutteオブジェクト生成
        $client = new Client(); 
        
        //toto(開催ページ）のCrawClientインスタンス取得
        $result = $client->request('GET', $url);
        
        return $result;
    }




    /*次ページのCrawオブジェクトを取得
     * Sympony クライアント使用の場合
     * 
     *      */
    protected function getNextCrawBySymponey($craw,$title){
         //Goutteオブジェクト生成
        $client = new Goutte\Client();
        
        /*Goutteライブらリを使用して次ページの遷移の場合*/
        $link_craw = $client->click($craw->selectLink($title)->link());
        
        return $link_craw;
    }
    
    
}