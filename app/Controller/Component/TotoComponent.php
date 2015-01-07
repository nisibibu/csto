<?php

/*Toto情報関連コンポーネント
 *
 *  */
use Goutte\Client;
require_once '../Vendor/goutte/goutte.phar';
/*定数*/
//TOTOの投票率の取得用URL
define('TOTO_VOTE', 'http://www.totoone.jp/blog/datawatch/');

class TotoComponent extends Component{
    public function getTotoVote(){
        //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //Toto投票率を格納

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', TOTO_VOTE);

        $crawler_vote->filter('td')->each(function($node)use(&$toto_vote)
        {
                if($node->text() !== NULL){
                    echo (string)$node->text() . "<br />";
                    //var_dump($node->text());
                    $toto_vote[] =(string)$node->text();
                }

        });
       return $toto_vote;
    }
}
?>