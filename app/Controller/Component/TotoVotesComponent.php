<?php

/*Toto情報関連コンポーネント
 *
 *  */
use Goutte\Client;
require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';

//define('TOT','http://www.totoone.jp/');

/*shellからモデルを使用する*/
$toto_vote_model = ClassRegistry::init('Totovote');

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
        
        $held_times;  //開催回の取得
        
        /*現在表示されている開催回取得*/
        $crawler_vote->filter('.chancecopy img')->each(function( $node )use(&$held_times){
            $held = trim($node->attr("alt"));
            if($held){
                $held_times = $held;
            }
        });
        
        //var_dump($held_times);
        
        /**開催回のテキストリンクを作成
         * 
         */
        $pattern = "#(第\d{3}回)#";
        preg_match_all($pattern, $held_times, $held_m);
        //var_dump($held_m);
        
        $pre_held = $held_m[0][0];  //前回開催回の取得
        $held_time = $held_m[0][1]; //今回開催回の取得
        
        preg_match('/\d+/', $held_time,$m);
        $held_time = $m[0];
        
        var_dump($held_time);
        
        /*開催回ページへ*/
        $craw =  $this->transionTotoPage($held_time);
        
        
        
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
        
        var_dump($held_lot);
        
        
        /*Toto(開催されている場合取得)*/
        if($held_lot['toto']){
            $this->getTotoMatchCard($craw);
        }
        
        
       /*mini(開催されている場合取得)*/
//       if($held_lot["mini toto A組"]){
//            $this->getMiniMatchCard();
//       }
//       if($held_lot["mini toto B組"]){
//            $this->getMiniMatchCard();
//       }
//       /*goal(開催されている場合取得)*/
//       if($held_lot["totoGOAL3"]){
//            $this->getGoal3MatchCard();
//       }
       
        
    }
    
    /* totoの開催カードを取得 */
    public function getTotoMatchCard($craw){
        $match_info = array();  //対戦カード
        $craw->filter('.kobetsu-format3  td')->each(function( $node )use(&$match_info){
            $info = trim($node->text());
            $match_info[$info] = $info;
        });
        
        $toto_match = array_slice($match_info, 0, 103); //totoの情報だけ切り出し

        unset($toto_match['VS']);
        unset($toto_match['データ']);

        $toto_match = array_values($toto_match);
        
        /*日付毎に分割*/
        
        /*先頭に日付を付加*/
        
        /*対戦ごとに分割*/
        $size = 6;
        $match = array_chunk($toto_match, $size);
        
        var_dump($match);
        
        return $match;
        
    }
    
    
    /* miniの開催カードの取得 */
    public function getMiniMatchCard($craw,$type){
        
    }
    
    /*goal3の開催カードの取得*/
    public function getGoal3MatchCard($craw,$type=3){
        
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