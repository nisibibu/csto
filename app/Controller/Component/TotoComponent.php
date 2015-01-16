<?php

/*Toto情報関連コンポーネント
 *
 *  */
use Goutte\Client;
require_once 'C:\xampp\htdocs\cake\app\Vendor/goutte/goutte.phar';
/*定数*/
//totoOne
define('TOTOONE', 'www.totoone.jp/');
//TOTOの投票率の取得用URL
define('TOTO_VOTE', 'http://www.totoone.jp/blog/datawatch/');
//TOTOの投票率の過去取得用URL
define('TOTO_VOTE_BACKNUMBER','http://www.totoone.jp/blog/datawatch/archives.php');

$toto_vote_model = ClassRegistry::init('Totovote');

/*totoの投票率を返す*/
class TotoComponent extends Component{
    
    public $uses = array('POST','Live','Totovote');    //使用するモデルを宣言
    
    public function getTotoVote(){
        //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //Toto投票率を格納

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', TOTO_VOTE);
        
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
        //連想配列の作成（結果データの生成）
        $vote_temp =  array_chunk($toto_vote, 7);        //一試合毎に分割
        
        //debug($vote_temp);
        
        /*toto_vote_result araay
        /* array(      
         *         各対戦カードの情報
         *         .................
         * )
         */
        foreach ($vote_temp as $var){
            //始めの不要データを取り除いて格納
            $toto_data = array_slice($var, 1,6);    //対戦カード～各々の投票率
            array_unshift($toto_data, $held_time,$held_date);
            $toto_vote_result[] = $toto_data;
        }
 
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
        
        //debug($toto_vote_result);
        //debug($toto_vote_result_2);
        
        
        /*取得したデータのDB登録*/
        //$this->setTotoVotesTable($toto_vote_result[3]);
        /*取得した配列を順番に登録処理メソッドへ*/
//        foreach ($toto_vote_result as $var){
//            $this->setTotoVotesTable($var);
//        }
        
        
        //debug($toto_vote_result);
        //debug($toto_vote_result_2);
        
        /*投票率を返す*/
        return $toto_vote_result_2;
    }
    
    //チームの情報を取得
    public function getTotoTeamInfo(){
        //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //チーム情報を格納

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', TOTO_VOTE);
        
        
    }
    
    //過去の投票率の取得
    public function getPastTotoVote(){
        
         //Goutteオブジェクト生成
        $client_vote = new Client();
        $toto_vote = array();   //チーム情報を格納

        //totoマッチング、投票率HTMLを取得
        $crawler_vote = $client_vote->request('GET', TOTO_VOTE_BACKNUMBER);
        
        //$crawler_vote->filter('#data_check_box1 > a')->attr('href');
        //バックナンバーの取得
        /*1ページ分の過去の投票率へのリンクを取得*/
        $links = $crawler_vote->filter('div.datawatch-archives a')->each(function( $node ){
            return array(
            'link_loc' => $node->attr('href'),
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
    }
    
    //selectLing(Gouttteライブラリ)
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